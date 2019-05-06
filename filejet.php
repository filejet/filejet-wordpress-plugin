<?php
/**
 * @package FileJet
 */
/*
Plugin Name: FileJet Pro
Plugin URI: https://filejet.io/
Description: <strong>Professional image optimization</strong> for your Wordpress site.
Version: 1.0
Author: FileJet
Text Domain: FileJet
*/

/*
Copyright 2012-2015 FileJet.
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'FILEJET_VERSION', '1.0' );
define( 'FILEJET__MINIMUM_WP_VERSION', '4.0' );
define( 'FILEJET__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

register_activation_hook( __FILE__, array( 'Filejet', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'Filejet', 'plugin_deactivation' ) );

if ( is_admin()) {
	require_once( FILEJET__PLUGIN_DIR . 'class.filejet-action.php' );
	require_once( FILEJET__PLUGIN_DIR . 'class.filejet-admin.php' );
	add_action( 'init', array( 'Filejet_Admin', 'init' ) );
}

require_once( FILEJET__PLUGIN_DIR . 'class.filejet.php' );
add_action( 'init', array( 'Filejet', 'init' ) );