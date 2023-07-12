<?php
/**
 * @package Akismet
 */
/*
Plugin Name: Custom Post Type
Plugin URI:
Description: This is custom post type plugin that help you to regiter the custom post type and categories of custom post type. It allows you to update the custom post type name. slug and category name, slug as well. 
Version: 1.0
Requires at least: 5.8
Requires PHP: 5.6.20
Author: WordPress Contributors  - Dev Kirandeep Singh
Author URI: https://profiles.wordpress.org/kirandeepsingh0036/
License: GPLv2 or later
Text Domain:
Copyright 2023 Kirandeep, Inc.
*/


// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  i am just p;lugin, Do not call plugin directly.';
	exit;
}


//Defined variables
define( 'CUSTOM_POST_TYPE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CUSTOM_POST_TYPE_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );

require_once( CUSTOM_POST_TYPE_PLUGIN_DIR . 'admin-page.php' );

$init = new Gust_Post();
$init->index();

register_deactivation_hook(
	__FILE__,
	'delete_options_function'
);

function delete_options_function(){
	delete_option('custom_post_options');
}

?>