<?php

class Lazy_Embeds_Wrapper {
	public function __construct() {
		add_filter( 'render_block', [ $this, 'replace_youtube_embed' ], 10, 2 );
	}

	/**
	 * Get the YouTube Video ID from a Youtube URL.
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
		return "https://i.ytimg.com/vi/{$youtube_id}/maxresdefault.jpg";
	}

	/**
	 * Get specific iframe attributes (title, width, height) from the block content.
	 *
	 * @param string $block_content
	 * @return array
	 */
	private function get_iframe_attributes_from_block_content( $block_content ) {
		$regex = '/(width|height|title)=\"([^"]*)\"/';

		return preg_match_all( $regex, $block_content, $matches ) ? array_combine( $matches[1], $matches[2] ) : [];
	}

	/**
	 * Get wrapper spacing percentage from width and height.
	 *
	 * @param int $width
	 * @param int $height
	 * @return float
	 */
	private function get_wrapper_spacing( $width, $height ) {
		return round( $height / $width * 100, 2 );
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
		if ( $block['blockName'] !== 'core-embed/youtube' || is_admin() ) {
			return $block_content;
		}

		if ( strpos( $block_content, 'yeet' ) === false ) {
			return $block_content;
		}

		$youtube_id = $this->get_youtube_id_from_url( $block['attrs']['url'] );

		if ( ! $youtube_id ) {
			return $block_content;
		}

		$attributes = $this->get_iframe_attributes_from_block_content( $block_content );
		$spacing = $this->get_wrapper_spacing( (int) $attributes['width'], (int) $attributes['height'] );
		$thumbnail_url = $this->get_youtube_thumbnail_url_from_id( $youtube_id );

		$wrapper = '<div class="lazy-embeds-wrapper" data-lazy-embeds-youtube-id="' . esc_attr( $youtube_id ) . '" style="padding-bottom:' . esc_attr( $spacing ) .'%; background-image: url(\'' . esc_url($thumbnail_url) . '\')">';

		if ( isset( $attributes['title'] ) ) {
			$wrapper .= '<span class="lazy-embeds-wrapper__title">' . esc_html( $attributes['title'] ) . '</span>';
		}

		$wrapper .= '<button class="lazy-embeds-wrapper__button" aria-label="' . __('Play', 'lazy-embeds') . '"><svg viewBox="0 0 68 48" xmlns="http://www.w3.org/2000/svg"><g fill-rule="nonzero" fill="none"><path d="M66.52 7.74c-.78-2.93-2.49-5.41-5.42-6.19C55.79.13 34 0 34 0S12.21.13 6.9 1.55c-2.93.78-4.63 3.26-5.42 6.19C.06 13.05 0 24 0 24s.06 10.95 1.48 16.26c.78 2.93 2.49 5.41 5.42 6.19C12.21 47.87 34 48 34 48s21.79-.13 27.1-1.55c2.93-.78 4.64-3.26 5.42-6.19C67.94 34.95 68 24 68 24s-.06-10.95-1.48-16.26z"/><path fill="#FFF" d="M45 24L27 14v20"/></g></svg></button>';
		$wrapper .= '</div>';

		return preg_replace('/<iframe.*><\/iframe>/', $wrapper, $block_content);
	}
}
