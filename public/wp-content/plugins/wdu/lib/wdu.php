<?php
/*******************
* wdu.php
* @author - P Guardiario <pguardiario@gmail.com>
*******************/

set_time_limit(0);


if(version_compare(PHP_VERSION, '5.3.0') < 0) die("Error: $argv[0] requires php version 5.3.0 or greater.");

require_once dirname(__FILE__) . '/pgcsv.php';

/**
 * Wfusop
 */
class Wfusop{
  /**
   * Instantiate the browser object
   */
  function __construct(){
  }

  function get_skus($chunk_size = 1){
    global $wpdb;
    if($chunk_size == 0) $chunk_size = 1;
    $skus = array();
    $total = $wpdb->get_var("select count(*) from $wpdb->postmeta where meta_key='_sku'");
    $ranges = array();
    $last = 0;
    for($i=0; $i<$chunk_size; $i++){
      $next = $last + ceil($total / $chunk_size);
      $ranges[$last] = $next - $last; 
      $last = $next;
    }

    foreach($ranges as $start => $end){
      $sql = "select post_id, meta_value from $wpdb->postmeta where meta_key='_sku' order by post_id limit " . $start . ", " . $end;

      $results = $wpdb->get_results($sql);

      foreach($results as $result) {
        $skus[$result->meta_value] = $result->post_id;
      }
    }

    return $skus;
  }

  function test($vars){
    global $wpdb, $woocommerce;
    $start_time = microtime(true);
    $affected_rows = 0;
    $data = array();
    $skus = $this->get_skus();
    // var_dump($skus); exit;
    //echo $vars['file']['path'];
    foreach(CSV::iterate($vars['file']['path'], true, $vars['delimiter']) as $row){
      if($sku = @$skus[$row[$vars['sku_column']]]){
        $data[$row[$vars['ctu_column']]][] = $sku;
      }
    }

    //var_dump($data); exit;

    foreach($data as $key => $ids){
      if(empty($ids)) continue;
      $sql = "select count(*) from $wpdb->postmeta where meta_value != '" . $key . "' and meta_key='" . $vars['column_to_update'] . "' and post_id in(" . implode(',', $ids) . ")";
      //echo $sql . "\n";
      if($num = $wpdb->get_var($sql)){
         $affected_rows += $num;
      }
    }

    $end_time = microtime(true);

    $retval = array(
      'status' => 'success',
      'affected_rows' => $affected_rows,
      'elapsed_time' => round($end_time - $start_time, 3),
    );
    return $retval;
  }
  
  function combine($column_to_update, $ctu_column){
    $keys = preg_split('/,\s*/', $column_to_update);
    $values = preg_split('/,\s*/', $ctu_column);
    if(count($keys) != count($values)) die('bad column count!');
    return array_combine($keys, $values);
  }

  function update($vars){
    $combined = $this->combine($vars['column_to_update'], $vars['ctu_column']);
    $results = array();

    switch(true){
      case isset($vars['file']['path']): $input = $vars['file']['path']; break;
      case file_exists($vars['source']): $input = $vars['source']; break;
      case preg_match('/^(http|ftp)/', $vars['source']):
        require_once dirname(__FILE__) . '/pgbrowser.php';
        $input = get_temp_dir() . 'daily_import.csv';
        $b = new PGBrowser();
        $file = $b->get($vars['source']);
        file_put_contents($input, $file->body);
        break;
      case preg_match('/^recipe:(\w+)/i', $vars['source'], $m):
        $matches = glob(dirname(__FILE__) . '/../recipes/' . $m[1] . '.*');
        if(count($matches) != 1) die('unknown or ambiguous recipe: ' . $m[1]);
        require $matches[0];
        $input = get_temp_dir() . 'daily_import.csv';
        break;
      default: die('Unknown source: ' . $vars['source']);
    }

    foreach($combined as $column_to_update => $ctu_column){
      $post = array_merge($vars, array('column_to_update' => $column_to_update, 'ctu_column' => $ctu_column));
      $results[] = $this->update_column($post, $input);
    }

    return array(
      'status' => count(array_filter($results, function($el){return $el['status'] !== 'success';})) ? 'failed' : 'success',
      'elapsed_time' => @array_reduce($results, function($n, $x){return $n + $x['elapsed_time'];}),
      'affected_rows' => @implode(', ', array_map(function($x){return $x['affected_rows'];}, $results))
    );
  }

  function update_column($vars, $input){
    global $wpdb, $woocommerce;
    $start_time = microtime(true);
    $affected_rows = 0;

    $data = array();

    $skus = $this->get_skus();

    foreach(CSV::iterate($input, true, $vars['delimiter']) as $row){
      if($sku = @$skus[$row[$vars['sku_column']]]){
        if(!isset($row[$vars['ctu_column']])) return array('status' => 'failed');
        $data[$row[$vars['ctu_column']]][] = $sku;
      }
    }

    foreach($data as $key => $ids){
      if(empty($ids)) continue;
      $sql = "update $wpdb->postmeta set meta_value = '" . $key . "' where meta_key='" . $vars['column_to_update'] . "' and post_id in(" . implode(',', $ids) . ")";

      if($num = $wpdb->query($sql)){
         $affected_rows += $num;
      }

      if('_sku' == $vars['column_to_update']){
        $sql = "update $wpdb->postmeta set meta_value = '" . ((0 == $key) ? 'outofstock': 'instock'). "' where meta_key='_stock_status' and post_id in(" . implode(',', $ids) . ")";
        //echo $sql . "\n";
        $wpdb->query($sql);
      }
    }

    $end_time = microtime(true);

    $retval = array(
      'status' => 'success',
      'affected_rows' => $affected_rows,
      'elapsed_time' => round($end_time - $start_time, 3),
    );
    return $retval;
  }
  
  function generate_template($column){
    global $wpdb;
    $fields = array(
      'stock' => '_stock',
      'price' => '_price'
    );
    $field = $fields[$column];
    $options = get_option('wdu_options');
    $chunk_size = $options['chunk_size'];
    if($chunk_size == 0) $chunk_size = 1;
    $skus = $this->get_skus($chunk_size);

    $total = $wpdb->get_var("select count(*) from $wpdb->postmeta where meta_key='" . $field . "'");
    $ranges = array();
    $last = 0;
    for($i=0; $i<$chunk_size; $i++){
      $next = $last + ceil($total / $chunk_size);
      $ranges[$last] = $next - $last; 
      $last = $next;
    }
    $ids = array_flip($skus);
    // var_dump($options); exit;

    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename=" . $column . ".csv");

    $fields = array('sku', 'name', $column);
    $csv = new CSV('php://output', $fields, $options['delimiter'] ? $options['delimiter'] : ',');

    foreach($ranges as $start => $end){
      $sql = "select post_id, meta_value from $wpdb->postmeta where meta_key='" . $field . "' order by post_id limit " . $start . ", " . $end;
// echo $sql . "\n";
      $results = $wpdb->get_results($sql);

      foreach($results as $result) {
        if(!$sku = @$ids[$result->post_id]) continue;
        $name = $wpdb->get_var("select post_title from $wpdb->posts where id=" . $result->post_id);
        $item = array(
          'sku' => $sku,
          'name' => $name,
          $column => $result->meta_value
        );
        $csv->save($item);

        //echo $result->meta_value . "\n";
        //$skus[$result->meta_value] = $result->post_id;
      }
    }
    $csv->close();
  }
}

$wdu_object = new Wfusop();


// just for testing
if(isset($argv) && basename(__FILE__) == $argv[0]){

  //$path_to_store = dirname(__FILE__) . '/../../../..';
  $path_to_store = 'E:\\cygwin\\home\\owner\\htdocs\\store';

  require_once('../../../htdocs/store/wp-load.php');

  error_reporting(E_ALL);
  ini_set('display_errors', 1);

//  $wdu_object->generate_template('price'); exit;
//  $wdu_object->generate_template('stock'); exit;

  $post = array(
    'action' => 'test',
    'source' => 'recipe:newegg',
    'delimiter' => ',',
    'chunk_size' => '1',
    'sku_column' => 'sku',
    'column_to_update' => '_price',
    'ctu_column' => 'price'
  );

//{"file":"\/tmp\/stock.csv","delimiter":",","chunk_size":"39","sku_column":"sku","column_to_update":"_stock","ctu_column":"stock"}

  $result = $wdu_object->update($post);
  var_dump($result);

// update wp_postmeta set meta_value=concat('wc_', post_id) where meta_key='_sku';
}


?>