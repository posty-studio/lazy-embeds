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

require_once( trailingslashit( dirname( __FILE__ ) ) . 'autoload.php' );

$lazy_embeds = new Lazy_Embeds\Setup();
$lazy_embeds->run();
