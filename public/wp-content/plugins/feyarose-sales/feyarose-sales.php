<?php
/*
  Plugin Name: Feyaros sales
  Description: Display completed and delivered orders for authorized users .
  Version: 1.0
  Author: Altima Russia
  Author URI: http://www.altima-agency.com
  License: GPL
 */
/* create custom plugin settings menu */
/* Kick out if he user is not(admin , shop_manager and lavka) */
/*
if( !current_user_can( 'administrator' ) && !current_user_can( 'shop_manager' ) && !current_user_can( 'lavka' ))  {
    add_action( 'admin_menu', 'remove_feyarose_sales_page' );
}else{
*/
    add_action('admin_menu', 'feyarose_sales_plugin_create_menu');
//}
// Remove feyarose-sales plugin
function remove_feyarose_sales_page() {
    remove_menu_page( "feyarose-sales.php");
}

function feyarose_sales_plugin_settings_page() {
    include('feyarose-sales-admin.php');
}
/* create new menu item in the dashboard */
function feyarose_sales_plugin_create_menu()
{
    add_menu_page('sales Plugin Settings', 'Feyarose Sales', 'edit_posts', __FILE__, 'feyarose_sales_plugin_settings_page', 'dashicons-format-aside');
}
/* Include plugin scripts and style files */
function enqueue_feyarose_sales_scripts() {
    wp_enqueue_script('feyarossales-jquery-ui-script', plugin_dir_url(__FILE__) . 'js/jquery-ui.min.js', array('jquery-ui-core') , 0.1, TRUE);
    wp_enqueue_script('feyarossales-table-sort', plugin_dir_url(__FILE__) . 'js/jquery.tablesorter.min.js', array());
    wp_enqueue_script('feyarossales-table-sort-pager', plugin_dir_url(__FILE__) . 'js/jquery.tablesorter.pager.min.js', array());
    wp_enqueue_script('feyarossalesjquery', plugin_dir_url(__FILE__) . 'js/feyarosesales.js', array());
    wp_enqueue_style( 'feyaroseorders-jquery-ui-theme', plugin_dir_url(__FILE__) . 'css/jquery-ui.theme.min.css' );
    wp_enqueue_style( 'feyaroseorders-jquery-ui-style', plugin_dir_url(__FILE__) . 'css/jquery-ui.min.css' );
    wp_enqueue_style( 'feyaroseorders-theme-default', plugin_dir_url(__FILE__) . 'css/theme.default.css' );
    wp_enqueue_style( 'feyaroseorders-table-pager', plugin_dir_url(__FILE__) . 'css/jquery.tablesorter.pager.css' );
    wp_enqueue_style( 'feyaroseorders-style', plugin_dir_url(__FILE__) . 'css/feyarose_sales.css' );
}
add_action('admin_enqueue_scripts', 'enqueue_feyarose_sales_scripts');
