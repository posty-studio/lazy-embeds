<?php

class Lazy_Embeds {
	public function __construct() {
		$this->plugin_name = 'lazy-embeds';

		$this->load_dependencies();
		$this->set_locale();
		$this->load_styles();
		$this->load_scripts();
	}

	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lazy-embeds-base.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lazy-embeds-youtube.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lazy-embeds-vimeo.php';
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
			new Lazy_Embeds_YouTube();
			new Lazy_Embeds_Vimeo();
		}
	}
}
