<?php

namespace Lazy_Embeds;

class Base {
	protected $attributes;
	protected $provider;

	/**
	 * Get specific iframe attributes from the block content.
	 *
	 * @param array $attributes
	 * @param string $block_content
	 * @return array
	 */
	public function get_iframe_attributes_from_block_content( $attributes, $block_content ) {
		$regex = '/(' . join( '|', $attributes ) . ')=\"([^"]*)\"/';

		return preg_match_all( $regex, $block_content, $matches ) ? (object) array_combine( $matches[1], $matches[2] ) : [];
	}

	/**
	 * Get wrapper spacing percentage from width and height.
	 *
	 * @return float
	 */
	public function get_wrapper_spacing() {
		if ( ! $this->attributes->width || ! $this->attributes->height ) {
			return 56.25; // Equal to 16:9 aspect ratio
		}

		return round( $this->attributes->height / $this->attributes->width * 100, 2 );
	}

	/**
	 * Replace default WordPress block classes and add custom HTML.
	 *
	 * @param string $block_content
	 * @return string
	 */
	public function replace_block( $block_content ) {
		$lazy_embed_content = preg_replace( '/wp-block-embed([^"\s]*)/', 'wp-block-lazy-embeds$1', $block_content );
		$lazy_embed_content = preg_replace( '/<iframe.*><\/iframe>/', $this->get_iframe_html(), $lazy_embed_content );
		$lazy_embed_content = str_replace(
			'<div class="wp-block-lazy-embeds__wrapper',
			'<div data-lazy-embeds-' . esc_attr( $this->provider ) . '-id="' . esc_attr( $this->attributes->id ) . '" style="padding-bottom:' . esc_attr( $this->get_wrapper_spacing() ) . '%;" class="wp-block-lazy-embeds__wrapper',
			$lazy_embed_content
		);

		/**
		 * Filters whether the Lazy Embed should be shown.
		 *
		 * @param bool $enable_lazy_embed Whether the Lazy Embed will be enabled. Default true.
		 * @param string $provider The embed provider.
		 * @param string $id The id of the embed.
		 * @param string $lazy_embed_content The content of the Lazy Embed block.
		 * @param string $block_content The content of the default embed block.
		 */
		$lazy_embed_enabled = (bool) apply_filters( 'lazy_embeds_enable_lazy_embed', true, $this->provider, $this->attributes->id, $lazy_embed_content, $block_content );

		if ( ! $lazy_embed_enabled ) {
			return $block_content;
		}

		return $lazy_embed_content;
	}
}
