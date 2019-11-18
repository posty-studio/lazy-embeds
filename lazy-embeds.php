<?php
/**
 * Plugin Name: Lazy Embeds
 * Description: Lazy embeds for the WordPress Block Editor.
 * Author: Daniel Post
 * Author URI: https://danielpost.com
 * License: GPL-3.0
 * Version: 1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'LAZY_EMBEDS_VERSION', '1.0.0' );
define( 'LAZY_EMBEDS_ASSETS', plugin_dir_url( __FILE__ ) . 'assets/' );

require plugin_dir_path( __FILE__ ) . 'includes/class-lazy-embeds.php';

$plugin = new Lazy_Embeds();
$plugin->run();
