<?php

namespace Lazy_Embeds;

class Setup {
	public function __construct() {
		$this->set_constants();
		$this->set_locale();
		$this->load_styles();
		$this->load_scripts();
	}

	private function set_constants() {
		define( 'LAZY_EMBEDS_VERSION', '1.0.0' );
		define( 'LAZY_EMBEDS_ASSETS', plugin_dir_url( __DIR__ ) . 'assets/' );
	}

	private function set_locale() {
		load_plugin_textdomain(
			'lazy-embeds',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}

	private function load_styles() {
		add_action( 'wp_enqueue_scripts', function() {
			wp_enqueue_style( 'lazy-embeds', LAZY_EMBEDS_ASSETS . 'css/lazy-embeds.css', [], LAZY_EMBEDS_VERSION );
		} );
	}

	private function load_scripts() {
		add_action( 'wp_enqueue_scripts', function() {
			wp_enqueue_script( 'lazy-embeds', LAZY_EMBEDS_ASSETS . 'js/lazy-embeds.js', [], LAZY_EMBEDS_VERSION, true );
		} );
	}

	public function run() {
		if ( ! is_admin() ) {
			new YouTube();
			new Vimeo();
		}
	}
}
