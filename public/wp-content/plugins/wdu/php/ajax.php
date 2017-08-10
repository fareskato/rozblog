<?php

preg_match_all('/function wdu_ajax_(\w+)/', file_get_contents(__FILE__), $m);
foreach($m[1] as $action){
  add_action('wp_ajax_' . $action,  'wdu_ajax_' . $action);
  add_action('wp_ajax_nopriv_' . $action,  'wdu_ajax_' . $action);
}

/**
 * Admin Ajax
 **/

function wdu_ajax_update() {
//  global $wdu_object;
//	global $wpdb; // this is how you get access to the database

//  error_reporting(E_ALL | E_STRICT);
// ini_set('display_errors', 1);

  require_once(WDU_DIR . '/lib/UploadHandler.php');
  require_once(WDU_DIR . '/lib/pgcsv.php');
  ob_start();
  $upload_handler = new UploadHandler();
  $json = ob_get_contents();
  $data = json_decode($json, true);
  ob_end_clean();
  $file = $data['files'][0];
  foreach(CSV::iterate($file['path'], false, $_POST['delimiter']) as $row){
    $headers = $row;
    break;
  }
  // var_dump($headers); exit;

  $guesses = array();
  $response = array('file' => $file, 'headers' => $headers);

  if(count($headers) < 2) $response['warning'] = 'this does not appear to be a multi-column CSV file (check delimiter)';

  foreach($headers as $header){
    if(!isset($guesses['sku']) && preg_match('/sku/i', $header)) $guesses['sku'] = $header;
    if(!isset($guesses['price']) && preg_match('/price/i', $header)) $guesses['price'] = $header;
    if(!isset($guesses['stock']) && preg_match('/stock|inventory/i', $header)) $guesses['stock'] = $header;
  }
  $response['guesses'] = $guesses;
  if(!isset($response['warning']) && !isset($guesses['sku'])) $warning = "sku field must be selected";
  if(!isset($response['warning']) && !isset($guesses['price']) && !isset($guesses['stock'])) $warning = "either price or stock field must be selected" ;
  if(isset($warning)) $response['warning'] = $warning;

  ob_start();
  ?>
  <tr>
    <td>Sku Column:</td>
    <td><select name="sku_column">
    <?php foreach($headers as $header){ ?>
      <option value="<?php echo $header ?>" <?php if(isset($guesses['sku']) && $guesses['sku'] == $header) echo 'selected' ?>><?php echo $header ?></option>
    <?php } ?>
    </select></td>
  </tr>
  <tr>
    <td><select name="column_to_update">
      <option value="_price" <?php if(isset($guesses['price'])) echo 'selected' ?>>Price</option>
      <option value="_stock" <?php if(isset($guesses['stock'])) echo 'selected' ?>>Stock</option>
    </select> Column:</td>
    <td><select name="ctu_column">
    <?php foreach($headers as $header){ ?>
      <option value="<?php echo $header ?>" <?php if(in_array($header, array(@$guesses['price'], @$guesses['stock']))) echo 'selected' ?>><?php echo $header ?></option>
    <?php } ?>
    </select></td>
  </tr>
  <?php
  $response['html'] = ob_get_contents();
  ob_end_clean();
  $json = json_encode($response);
  if(json_last_error()){
    // var_dump($response); exit;
    $json = json_encode(array('warning' => 'There seems to be a problem with this CSV file (check delimiter)'));
  }
  echo $json;
  wdu_update_options();
  exit;
}

function wdu_update_options() {
  $options = get_option('wdu_options');
  $options['delimiter'] = preg_replace('/(\\\\)+/', '\\', $_POST['delimiter']);
  $options['chunk_size'] = $_POST['chunk_size'];
  update_option('wdu_options', $options );
}

function wdu_ajax_run_test() {
  global $wdu_object;
  require_once WDU_DIR . '/lib/wdu.php';
//	global $wpdb; // this is how you get access to the database
  wdu_update_options();
  $options = get_option('wdu_options');
  
  $result = $wdu_object->test($_POST);
  header('Content-type: application/json');
  echo json_encode($result);
  
  exit;
}

function wdu_ajax_run_update() {
  global $wdu_object;
  require_once WDU_DIR . '/lib/wdu.php';
  wdu_update_options();
  $options = get_option('wdu_options');
  $result = $wdu_object->update($_POST);

  header('Content-type: application/json');
  echo json_encode($result);
  
  exit;
}

function wdu_ajax_update_schedule() {
  global $wdu_object;

  require_once WDU_DIR . '/lib/wdu.php';
  $options = get_option('wdu_options');

  $daily_task = array();

  if(@isset($_POST['file']['path'])) $daily_task['source'] = $_POST['file']['path'];

  foreach(array('source', 'delimiter', 'chunk_size', 'sku_column', 'column_to_update', 'ctu_column') as $key){
    if(!isset($_POST[$key])) continue;
    $daily_task[$key] = $_POST[$key];
  }

  $options['daily_task'] = $daily_task;
  update_option('wdu_options', $options );

  if($str = $_POST['next_event']){

    wp_clear_scheduled_hook('wdu_daily_import');
    wp_schedule_event(strtotime($str), 'daily', 'wdu_daily_import');

    //next_event	03:00:46
  }

  header('Content-type: application/json');
  echo json_encode($options['daily_task']);
  
  exit;
}

function wdu_ajax_clear_schedule() {
  global $wdu_object;

  require_once WDU_DIR . '/lib/wdu.php';
  $options = get_option('wdu_options');
  $options['daily_task'] = array();
  update_option('wdu_options', $options );

  header('Content-type: application/json');
  echo json_encode(array('status' => 'success'));
  
  exit;
}



?>