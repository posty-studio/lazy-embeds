<?php

class Lazy_Embeds_Vimeo extends Lazy_Embeds_Base {
	/**
	 * Video attributes from Vimeo API.
	 *
	 * @var array
	 */
	protected $attributes;

	public function __construct() {
		add_filter( 'render_block', [ $this, 'replace_vimeo_embed' ], 10, 2 );
	}

	/**
	 * Get the Vimeo video ID from a Vimeo URL.
	 *
	 * @param string $url
	 * @return string
	 */
	private function get_vimeo_id_from_url( $vimeo_url ) {
		// https://stackoverflow.com/questions/10488943/easy-way-to-get-vimeo-id-from-a-vimeo-url/34027757
		$regex = '/(?:https?:\/\/)?(?:www\.)?vimeo\.com\/(?:(?:[a-z0-9]*\/)*\/?)?(?\'vimeo_id\'[0-9]+)/';

		return preg_match( $regex, $vimeo_url, $matches ) ? $matches['vimeo_id'] : '';
	}

	/**
	 * Get video attributes from the Vimeo API.
	 *
	 * @param id $vimeo_id
	 * @return array
	 */
	private function get_attributes_from_vimeo_id( $vimeo_id ) {
		$transient_name = "lazy_embeds_vimeo_{$vimeo_id}";

		if ( ( $cached_attributes = get_transient( $transient_name ) ) !== false ) {
			return $cached_attributes;
		}

		$attributes = wp_remote_retrieve_body( wp_remote_get( esc_url_raw( "https://vimeo.com/api/v2/video/{$vimeo_id}.json" ) ) );

		if ( empty( $attributes ) ) {
			return [];
		}

		$attributes = json_decode( $attributes );

		if ( ! is_array ( $attributes ) || !isset( $attributes[0]->title ) ) {
			return [];
		}

		$attributes = $attributes[0];

		// Set thumbnails for both WebP and JPG
		$attributes->thumbnail_large_jpg = str_replace( '.webp', '.jpg', $attributes->thumbnail_large );
		$attributes->thumbnail_large_webp = str_replace( '.jpg', '.webp', $attributes->thumbnail_large );

		set_transient( $transient_name, $attributes, WEEK_IN_SECONDS );

		return $attributes;
	}

	/**
	 * @return string
	 */
	private function get_iframe_html() {
		ob_start();
		?>

		<div class="wp-block-lazy-embeds__vimeo-header">
			<?php if ( isset( $this->attributes->user_url ) && isset( $this->attributes->user_portrait_large )  ) : ?>
				<div class="wp-block-lazy-embeds__vimeo-portrait" aria-hidden="true">
					<a href="<?php echo esc_url( $this->attributes->user_url ); ?>" target="_blank" rel="noopener">
						<img src="<?php echo esc_url( $this->attributes->user_portrait_large ); ?>" alt="<?php esc_attr_e( 'Link to video owner\'s profile', 'lazy-embeds' ); ?>">
					</a>
				</div>
			<?php endif; ?>

			<div class="wp-block-lazy-embeds__vimeo-meta">
				<?php if ( isset( $this->attributes->url ) && isset( $this->attributes->title ) ) : ?>
					<a class="wp-block-lazy-embeds__vimeo-title" href="<?php echo esc_url( $this->attributes->url ); ?>" target="_blank" rel="noopener">
						<?php echo esc_html( $this->attributes->title ); ?>
					</a>
				<?php endif; ?>

				<?php if ( isset( $this->attributes->user_name ) && isset( $this->attributes->user_url ) ) : ?>
					<div class="wp-block-lazy-embeds__vimeo-byline">
						<?php printf(__('From %s', 'lazy-embeds'), '<a class="wp-block-lazy-embeds__vimeo-username" href="' . esc_url( $this->attributes->user_url ) . '" target="_blank" rel="noopener">' . esc_html( $this->attributes->user_name ) . '</a>'); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<div role="button" tabindex="0" class="wp-block-lazy-embeds__vimeo-button" aria-label="<?php esc_attr_e( 'Play', 'lazy-embeds' ); ?>">
			<svg viewBox="0 0 20 20" preserveAspectRatio="xMidYMid"><path d="M1 0l19 10L1 20z"/></svg>
		</div>

		<?php
		return ob_get_clean();
	}

	/**
	 * Replace the Vimeo embed iframe with our own wrapper.
	 *
	 * @param string $block_content
	 * @param array $block
	 * @return string
	 */
	public function replace_vimeo_embed( $block_content, $block ) {
		// Sanity check
		if ( $block['blockName'] !== 'core-embed/vimeo' || is_admin() ) {
			return $block_content;
		}

		$vimeo_id = $this->get_vimeo_id_from_url( $block['attrs']['url'] );

		if ( empty( $vimeo_id ) ) {
			return $block_content;
		}

		$this->attributes = $this->get_attributes_from_vimeo_id( $vimeo_id );

		if ( empty( $this->attributes ) ) {
			return $block_content;
		}

		$spacing = $this->get_wrapper_spacing( (int) $this->attributes->width, (int) $this->attributes->height );

		$block_content = str_replace( 'class="wp-block-embed', 'class="wp-block-lazy-embeds', $block_content );
		$block_content = preg_replace( '/<iframe.*><\/iframe>/', $this->get_iframe_html(), $block_content );
		$block_content = str_replace( '<div class="wp-block-lazy-embeds__wrapper' , '<div data-lazy-embeds-vimeo-id="' . esc_attr( $this->attributes->id ) . '" style="padding-bottom:' . esc_attr( $spacing ) .'%; background-image: url(\'' . esc_url( $this->attributes->thumbnail_large_webp ) . '\')" class="wp-block-lazy-embeds__wrapper', $block_content );

		return $block_content;
	}
}
