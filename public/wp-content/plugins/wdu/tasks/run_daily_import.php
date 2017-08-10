<?php
ob_start();
error_reporting(15);
$path_to_store = preg_replace('/wp-content.*/', '', dirname(__FILE__));
if(!file_exists($path_to_store . '/wp-load.php')){
  $path_to_store = preg_replace('/wp-content.*/', '', $_SERVER['SCRIPT_FILENAME']);
}

require_once($path_to_store . '/wp-load.php');

require_once WDU_DIR . '/lib/wdu.php';
$junk = ob_get_contents();
ob_end_clean();

wdu_do_daily_import();

// http://localhost/wp-content/plugins/wdu/tasks/run_daily_import.php
?>