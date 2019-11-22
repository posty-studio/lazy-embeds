<?php

class Lazy_Embeds_YouTube extends Lazy_Embeds_Base {
	public function __construct() {
		$this->provider = 'youtube';
		add_filter( 'render_block', [ $this, 'replace_youtube_embed' ], 10, 2 );
	}

	/**
	 * Get the YouTube video ID from a YouTube URL.
	 *
	 * @param string $url
	 * @return string
	 */
	private function get_youtube_id_from_url( $youtube_url ) {
		// https://stackoverflow.com/questions/3452546/how-do-i-get-the-youtube-video-id-from-a-url/27728417#27728417
		$regex = '/^.*(?:(?:youtu\.be\/|v\/|vi\/|u\/\w\/|embed\/)|(?:(?:watch)?\?v(?:i)?=|\&v(?:i)?=))(?\'youtube_id\'[^#\&\?]*).*/';

		return preg_match( $regex, $youtube_url, $matches ) ? $matches['youtube_id'] : '';
	}

	/**
	 * Get the YouTube thumbnail URL from a YouTube ID.
	 *
	 * @param string $id
	 * @return string
	 */
	private function get_youtube_thumbnail_url_from_id( $youtube_id ) {
		return esc_url_raw( "https://i.ytimg.com/vi/{$youtube_id}/hqdefault.jpg" );
	}

	/**
	 * @return string
	 */
	public function get_iframe_html() {
		ob_start();
		?>

		<picture class="wp-block-lazy-embeds__thumbnail">
			<source srcset="<?php echo esc_url( $this->attributes->thumbnail ); ?>" type="image/jpeg">
			<img src="<?php echo esc_url( $this->attributes->thumbnail ); ?>" alt="<?php printf( __( 'Thumbnail for %s', 'lazy-embeds' ), esc_attr( $this->attributes->title ) ); ?>">
		</picture>

		<?php if ( isset( $this->attributes->title ) ) : ?>
			<span class="wp-block-lazy-embeds__youtube-title"><?php echo esc_html( $this->attributes->title ); ?></span>
		<?php endif; ?>

		<div role="button" tabindex="0" class="wp-block-lazy-embeds__youtube-button" aria-label="<?php esc_attr_e( 'Play', 'lazy-embeds' ); ?>">
			<svg viewBox="0 0 68 48" xmlns="http://www.w3.org/2000/svg"><g fill-rule="nonzero" fill="none"><path d="M66.52 7.74c-.78-2.93-2.49-5.41-5.42-6.19C55.79.13 34 0 34 0S12.21.13 6.9 1.55c-2.93.78-4.63 3.26-5.42 6.19C.06 13.05 0 24 0 24s.06 10.95 1.48 16.26c.78 2.93 2.49 5.41 5.42 6.19C12.21 47.87 34 48 34 48s21.79-.13 27.1-1.55c2.93-.78 4.64-3.26 5.42-6.19C67.94 34.95 68 24 68 24s-.06-10.95-1.48-16.26z"/><path fill="#FFF" d="M45 24L27 14v20"/></g></svg>
		</div>

		<?php
		return ob_get_clean();
	}

	/**
	 * Replace the YouTube embed iframe with our own wrapper.
	 *
	 * @param string $block_content
	 * @param array $block
	 * @return string
	 */
	public function replace_youtube_embed( $block_content, $block ) {
		// Sanity check
		if ( $block['blockName'] !== 'core-embed/youtube' ) {
			return $block_content;
		}

		$youtube_id = $this->get_youtube_id_from_url( $block['attrs']['url'] );

		if ( ! $youtube_id ) {
			return $block_content;
		}

		$this->attributes = $this->get_iframe_attributes_from_block_content( ['width', 'height', 'title'], $block_content );
		$this->attributes->id = $youtube_id;
		$this->attributes->thumbnail = $this->get_youtube_thumbnail_url_from_id( $youtube_id );

		return $this->replace_block( $block_content );
	}
}
