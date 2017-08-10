<?php
/*
  Plugin Name: Feyaros popup
  Description: Display certain page in popup window one time per day also display the roses on the home page!
  Version: 1.0
  Author: Altima Russia
  Author URI: http://www.altima-agency.com
  License: GPL
 */

// create custom plugin settings menu
add_action('admin_menu', 'feyarose_popup_plugin_create_menu');
function feyarose_popup_plugin_settings_page() {
    include('feyarose-popup-admin.php');
}
function feyarose_popup_plugin_create_menu()
{
//*******************************************
//create new menu item in the dashboard
    add_menu_page('popup Plugin Settings', 'Feyarose Popup', 'manage_options', __FILE__, 'feyarose_popup_plugin_settings_page', 'dashicons-visibility');
//*******************************************
}
add_action( 'admin_init', 'my_deletecookie' );
function my_deletecookie() {
    if(isset($_POST['reset_cookie'])) {
        setcookie("feyarose", false, (time() + 12 * 60 * 60) * (-1));
    }

}