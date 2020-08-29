<?php 

/**
 * Plugin Name:       Guest Post
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Create post from the front end using this plugin.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Kirandeep Singh
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       my-basics-plugin
 * Domain Path:       /languages
 */



// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  i am just p;lugin, Do not call plugin directly.';
	exit;
}


//Defined variables
define( 'GUEST_POSR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GUEST_POSR_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );

require_once( GUEST_POSR_PLUGIN_DIR . 'actions-file.php' );

$init = new Gust_Post();
$init->index();

register_activation_hook( __FILE__,'plugin_activation' );

function plugin_activation(){
	
}


register_deactivation_hook( __FILE__, array( 'Akismet', 'plugin_deactivation' ) );


 ?>