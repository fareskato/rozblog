<?php
/*
	Plugin Name: Woocommerce Easy Checkout Fields Editor
	Plugin URI: http://phppoet.com
	Description: lets you Add/edit/delete checkout fields for woocoomerce. 
    Version: 1.2.2
	Author: phppoet
	Author URI: http://phppoet.com
	
	Text Domain: phppoet-checkout-fields
	Domain Path: /languages
	Requires at least: 3.3
    Tested up to: 4.1
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


 if( !defined( 'pcfme_PLUGIN_URL' ) )
define( 'pcfme_PLUGIN_URL', plugin_dir_url( __FILE__ ) );



load_plugin_textdomain( 'pcfme', false, basename( dirname(__FILE__) ).'/languages' );

//include the classes
include dirname( __FILE__ ) . '/include/pcmfe_core_functions.php';
include dirname( __FILE__ ) . '/include/update_checkout_fields_class.php';
include dirname( __FILE__ ) . '/include/add_order_meta_fields_class.php';
include dirname( __FILE__ ) . '/include/manage_extrafield_class.php';

include dirname( __FILE__ ) . '/include/admin/pcfme_admin_settings.php';



/*
 * Plugin Update Checker
 */

    require dirname( __FILE__ ) . '/plugin-update-checker/plugin-update-checker.php';
       $MyUpdateChecker = PucFactory::buildUpdateChecker(
          'http://phppoet.com/updates/?action=get_metadata&slug=phppoet-checkout-fields', //Metadata URL.
           __FILE__, //Full path to the main plugin file.
          'phppoet-checkout-fields' //Plugin slug. Usually it's the same as the name of the directory.
    );
?>