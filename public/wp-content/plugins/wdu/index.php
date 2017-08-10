<?php
/*
Plugin Name: WC Daily Update
Plugin URI: http://wdu.pay4data.com/
Description: Schedule Daily Fast Updates for your store Prices or Stock
Author: P Guardiario
Version: 0.4
Author URI: http://wdu.pay4data.com/
*/

define('WDU_DIR', plugin_dir_path( __FILE__ ));
define('WDU_URL', plugin_dir_url(__FILE__));


function wdu_init(){
  $wdu_options = get_option('wdu_options');
}

/**
 * Adds admin menu page(s)
 */
function wdu_admin_menu() {
  require_once WDU_DIR . '/php/tools-wdu.php';
  $wdu_tools_page = add_submenu_page('tools.php', 'WC Daily Update', 'WC Daily Update','administrator', 'tools-wdu.php', 'wdu_tools_page');
}

/**
 * Adds wdu options
 */
function wdu_admin_init(){
  register_setting( 'wdu_options', 'wdu_options' );
  $current_version = 0.4;
  $version = get_option('wdu_version');
  if ($version != $current_version) {
    // Do whatever upgrades needed here.
    // update_option('wdu_version', $current_version);
    // $notices = get_option('wdu_deferred_admin_notices', array());
    // $notices[] = "Upgraded version $version to $current_version.";
    // update_option('wdu_deferred_admin_notices', $notices);
  }
}


/**
 * Admin notices
 */
add_action('admin_notices', 'wdu_admin_notices');
function wdu_admin_notices() {
  if ($notices= get_option('wdu_deferred_admin_notices')) {
    foreach ($notices as $notice) {
      echo "<div class='updated'><p>$notice</p></div>";
    }
    delete_option('wdu_deferred_admin_notices');
  }
}

/**
 * Activation /Deactivation hooks
 */

function wdu_register_deactivation_hook() {
  wp_clear_scheduled_hook('wdu_daily_import');
}

function wdu_register_activation_hook() {
  global $wpdb;

  $default_wdu_options = array(
    'delimiter' => ',',
    'use_custom_delimiter' => false,
    'chunk_size' => '5',
    'daily_task' => array()
  );
  add_option('wdu_options', $default_wdu_options);
  // $wpdb->query(file_get_contents(WDU_DIR . 'sql/install.sql'));

  wp_schedule_event(time(), 'daily', 'wdu_daily_import');
  wdu_add_notice('Activation Complete!');
}

function wdu_add_notice($notice){
  $notices = get_option('wdu_deferred_admin_notices', array());
  $notices[] = "WC Daily Update: " . $notice;
  update_option('wdu_deferred_admin_notices', $notices);
}

register_activation_hook(__FILE__, 'wdu_register_activation_hook');
register_deactivation_hook(__FILE__, 'wdu_register_deactivation_hook');

add_action('init', 'wdu_init' );
add_action('admin_menu', 'wdu_admin_menu');
add_action('admin_init', 'wdu_admin_init' );

if($_SERVER && preg_match('/admin-ajax.php/', $_SERVER['REQUEST_URI']) && preg_match('/wdu/', $_SERVER['HTTP_REFERER']))
  require_once WDU_DIR . "php/ajax.php";

// foreach(glob(WDU_DIR . "php/*.php") as $filename) require_once $filename;


add_action('wdu_daily_import', 'wdu_do_daily_import');

function wdu_do_daily_import(){
  global $wdu_object;
  require_once WDU_DIR . '/lib/wdu.php';

  $options = get_option('wdu_options');
  $task = $options['daily_task'];
  if(!isset($task) || !isset($task['source'])){
    return;
  }
  
  $result = $wdu_object->update($task);

  if($result['status'] == 'success'){
    wdu_add_notice($result['affected_rows'] . ' products were updated in ' . $result['elapsed_time'] . ' seconds on ' . date('m/d/Y H:i:s'));
  } else {
    wdu_add_notice('There was an error with daily task on: ' . date('m/d/Y H:i:s'));
  }

};
////////////////

?>