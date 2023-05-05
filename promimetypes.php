<?php
/**
 * Plugin Name: Pro Mime Types
 * Plugin URI: https://wordpress.org/plugins/pro-mime-types/
 * Description: Allows you to edit mime types with or without Pro Sites (depends if enabled). Created for multisites.
 * Version: 1.0.7
 * Author: Sybre Waaijer
 * Author URI: https://cyberwire.nl/
 * Text Domain: promimetypes
 */

function pmt_locale_init() {
	$plugin_dir = basename(dirname(__FILE__));
	load_plugin_textdomain( 'promimetypes', false, $plugin_dir . '/language/');
}
add_action('plugins_loaded', 'pmt_locale_init');

require_once( dirname( __FILE__ ) . '/lib/mimetypes.php' ); //global variable: $promimes

require_once( dirname( __FILE__ ) . '/lib/shortcode.php' ); //Shortcode for showing mime types
require_once( dirname( __FILE__ ) . '/lib/admin-init.php' ); //Admin area init
require_once( dirname( __FILE__ ) . '/lib/admin-tab1.php' ); //Admin area tab 1
require_once( dirname( __FILE__ ) . '/lib/admin-tab2.php' ); //Admin area tab 2

require_once( dirname( __FILE__ ) . '/lib/execute.php' ); //Execute

//* Core rewrites
require_once( dirname( __FILE__ ) . '/lib/core/ext2type.php' ); // Extension to Type (image/audio/video/document/etc) (function ext2type())
require_once( dirname( __FILE__ ) . '/lib/core/imagemimestoexts.php' ); //Image extension check (function wp_check_filetype_and_ext())

//* MultiSite/Single support (redundant)
function pmt_site_admin() {
	if ( function_exists( 'is_super_admin' ) ) {
		return is_super_admin(); //MultiSite
	} elseif ( function_exists( 'is_site_admin' ) ) {
		return is_site_admin(); //Single
	} else {
		return true; //If all else fails, should throw an error in case?
	}
}

//* Pro Sites active check
function is_prosites_active() {
	if ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'pro-sites/pro-sites.php' ) ) {
		return true;
	} else {
		return false;
	}
}
