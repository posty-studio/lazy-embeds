<?php

class Lazy_Embeds_Base {
	/**
	 * Get specific iframe attributes from the block content.
	 *
	 * @param array $attributes
	 * @param string $block_content
	 * @return array
	 */
	public function get_iframe_attributes_from_block_content( $attributes, $block_content ) {
		$regex = '/(' . join('|', $attributes) . ')=\"([^"]*)\"/';

		return preg_match_all( $regex, $block_content, $matches ) ? array_combine( $matches[1], $matches[2] ) : [];
	}

	/**
	 * Get wrapper spacing percentage from width and height.
	 *
	 * @param int $width
	 * @param int $height
	 * @return float
	 */
	public function get_wrapper_spacing( $width, $height ) {
		// Sensible default.
		if ( ! $width || ! $height ) {
			$width = 16;
			$height = 9;
		}

		return round( $height / $width * 100, 2 );
	}
}
