<?php
/*
Plugin Name: Feyarose Orders handling and export
Plugin URI: http://www.rozblog.ru
Description: Handling orders of bouquets and export of orders
Author: Altima Russia
Version: 1.0
Author URI: http://www.altima-agency.com
*/
function feyaroseorders_admin() {
    include('calendar.php');
    include('feyarose_orders_functions.php');
    include('feyarose_orders_admin.php');
}
function feyaroseorders_admin_actions() {
    //$page = add_menu_page( 'Feyarose Orders', 'Feyarose Orders', 'edit_shop_order', 'Feyarose_Orders', 'feyaroseorders_admin' );
    $page=  add_menu_page( 'Feyarose Orders', 'Feyarose Orders', 'edit_shop_orders', __FILE__, 'feyaroseorders_admin' );
}
add_action('admin_menu', 'feyaroseorders_admin_actions');

function admin_register_head() {
    $siteurl = get_option('siteurl');
    $url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/calendar.css';
    echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
}
add_action('admin_head', 'admin_register_head');
/*
 * Frontend ajax stuff
 * */
add_action("wp_ajax_FeyaroseOrders_Init", "FeyaroseOrders_Init");
add_action("wp_ajax_nopriv_FeyaroseOrders_Init", "FeyaroseOrders_Init");

function FeyaroseOrders_Init(){
    global $wpdb;
    // use $wpdb to do your inserting
    //if(is_checkout()) {
    $obj_init = array();
    $obj_init['disabled_dates'] = unserialize(get_option('feyaroseorders_datesdisabled'));
    //$obj_init['stock'] = $stocks;
    $obj_init['delay_max'] = get_option('feyaroseorders_delaymaxdelivery');
    $obj_init['site_url'] = site_url();
    //header('Content-Type: application/json');
    wp_send_json($obj_init);
    //}
}

/*
 * Include plugin scripts into product page
 * */
function enqueue_feyaroseorders_scripts() {
    if(is_product()) {
        wp_enqueue_script('feyaroseorders', plugin_dir_url(__FILE__) . 'feyarose_orders.js', array());
        wp_enqueue_style( 'feyaroseorders-phonejscss', plugin_dir_url(__FILE__) . 'phone-js/css/intlTelInput.css' );
        wp_enqueue_script('feyaroseorders-phonejs', plugin_dir_url(__FILE__) . 'phone-js/js/intlTelInput.min.js', array());
    }
    if(is_checkout()) {
        wp_enqueue_script('feyaroseorders', plugin_dir_url(__FILE__) . 'feyarose_orders.js', array());
        wp_enqueue_style( 'feyaroseorders-phonejscss', plugin_dir_url(__FILE__) . 'phone-js/css/intlTelInput.css' );
        wp_enqueue_script('feyaroseorders-phonejs', plugin_dir_url(__FILE__) . 'phone-js/js/intlTelInput.min.js', array());
        wp_enqueue_script('feyaroseorders-checkoutjs', plugin_dir_url(__FILE__) . 'feyaroseorders_checkout.js', array());
    }
}
// Chose a better action!
add_action('wp_enqueue_scripts', 'enqueue_feyaroseorders_scripts');

function feyaroseorders_woocommerce_payment_complete( $order_id ) {
    include('feyarose_orders_functions.php');
    feyaroseorders_update_stock_data($order_id);
}
function feyaroseorders_processing($order_id) {
    include('feyarose_orders_functions.php');
    feyaroseorders_update_stock_data($order_id);
    error_log("$order_id set to PROCESSING", 0);
}
function feyaroseorders_completed($order_id) {
    include('feyarose_orders_functions.php');
    feyaroseorders_update_stock_data($order_id);
    error_log("$order_id set to COMPLETED", 0);
}

add_action('woocommerce_payment_complete', 'feyaroseorders_woocommerce_payment_complete');
add_action('woocommerce_order_status_processing', 'feyaroseorders_processing');
add_action('woocommerce_order_status_completed', 'feyaroseorders_completed');

function feyaroseorders_check_if_valid_cart_item($cart_item, $quantity = null)
{
    include('feyarose_orders_functions.php');
    $nbroses = false;
    $deliverydate = false;
    $valid_item = array('valid' => true, 'message' => '');

    foreach($cart_item['addons'] as $addon) {
        if(strpos($addon['name'], feyaroseorders_util_getSearchLabel('nbroses')) !== false){
            $nbroses = $addon['value'];
            if($nbroses <= 0) {
                $nbroses = false;
            }
        }
        if(strpos($addon['name'], feyaroseorders_util_getSearchLabel('deliverydate')) !== false){
            $deliverydate = $addon['value'];
            if($deliverydate == '') {
                $deliverydate = false;
            }
        }
    }
    if($nbroses != false && $deliverydate != false) {

        $tsDate1 = strtotime($deliverydate);
        $tsDate = mktime(0,0,0,date('m',$tsDate1),date('d', $tsDate1), date('Y', $tsDate1));
        $remainingStock = feyaroseorders_get_remaining_stock_for_date($tsDate);
        $qty = ($quantity != null ) ? $quantity : $cart_item['quantity'];
        if($remainingStock - ($nbroses * $qty) < 0) {
            $valid_item['valid'] = false;
            $valid_item['message'] = 'There is not enough roses in stock';
        }
    }
    if($deliverydate == false && $nbroses !== false) {
        $valid_item['valid'] = false;
        $valid_item['message'] = 'Please select a delivery date';
    }
    return $valid_item;
}

function feyaroseorders_validate_add_cart_item( $passed, $product_id, $quantity, $variation_id = '', $variations= '' ) {
    global $woocommerce;
    $cart = $woocommerce->cart->get_cart();
    $cart_length = count($cart);
    $i = 0;
    if($cart_length == 0) {
        return $passed;
    } else {
        foreach($cart as $cart_item_key => $values ) {
            $i++;
            if($i == $cart_length) {
                $valid_item  = feyaroseorders_check_if_valid_cart_item($values);
                if($valid_item['valid'] == false) {
                    wc_add_notice( __( $valid_item['message'], 'textdomain' ), 'error' );
                    return false;
                } else {
                    return true;
                }
            }
        }
    }
}
//add_filter( 'woocommerce_simple_add_to_cartwoocommerce_simple_add_to_cart', 'feyaroseorders_validate_add_cart_item', 10, 5 );
//add_filter('woocommerce_add_to_cart_validation', 'feyaroseorders_validate_add_cart_item', 10, 5);
//add_filter( 'woocommerce_update_cart_validation', 'feyaroseorders_woocommerce_update_cart_action', 10, 4 );
//add_action( 'woocommerce_check_cart_items', 'feyaroseorders_check_cart_items' );

function feyaroseorders_woocommerce_update_cart_action($bool, $cart_item_key, $values, $quantity)
{
    $valid_item = feyaroseorders_check_if_valid_cart_item($values, $quantity);
    if ($valid_item['valid'] == false) {
        wc_add_notice(__($valid_item['message'], 'textdomain'), 'error');
        return false;
    }
    return $bool;
}

function feyaroseorders_check_cart_items($instance, $number = null)
{
    global $woocommerce;
    $cart = $woocommerce->cart->get_cart();
    $cart_length = count($cart);
    $i = 0;
    foreach ($cart as $cart_item_key => $values) {
        $i++;
        if ($i == $cart_length) {
            $valid_item = feyaroseorders_check_if_valid_cart_item($values);
            if ($valid_item['valid'] == false) {
                wc_add_notice(__($valid_item['message'], 'textdomain'), 'error');
                return false;
            } else {
                return true;
            }
        }
    }
}
//add_action('woocommerce_check_cart_items', 'feyaroseorders_check_cart_items');

//add_filter('woocommerce_update_cart_validation', 'feyaroseorders_woocommerce_update_cart_action', 10, 4);
/*
*====================================================
*   Feyarose orders admin ajax the new version
*====================================================
* @author Fares
*   handle the calendar stock in the admin panel with AJAX
*/
/* Apply scripts and styles in the admin dashboard **/
function feyaroseorders_enqueue_admin_script()
{
    if (is_admin()) {
//        wp_enqueue_script('feyarosrorders_add_admin_bootdtrap_script', plugin_dir_url(__FILE__) . 'bootstrap.min.js',array('jquery'));
        wp_enqueue_script('feyarosrorders_add_admin_script', plugin_dir_url(__FILE__) . 'feyaroseorders_admin.js', array('jquery'));
//        wp_enqueue_style( 'feyaroseorders_add_admin_bootdtrap_style', plugin_dir_url(__FILE__) . 'bootstrap.min.css' );
        wp_localize_script('feyarosrorders_add_admin_script', 'ROZBLOG_STOCK', array('security' => wp_create_nonce()));

    }

    /* define ajax action **/
    //add_action('wp_ajax_nopriv_get_stock_modal', 'feyaroseorders_modal_ajax');
    add_action('wp_ajax_get_stock_modal', 'feyaroseorders_modal_ajax');
    add_action('admin_enqueue_scripts', 'feyaroseorders_enqueue_admin_script');
}
add_action('admin_enqueue_scripts', 'feyaroseorders_enqueue_admin_script');

//add_filter( 'woocommerce_simple_add_to_cartwoocommerce_simple_add_to_cart', 'feyaroseorders_validate_add_cart_item', 10, 5 );

//add_filter( 'woocommerce_add_to_cart_validation', 'feyaroseorders_validate_add_cart_item', 10, 5 );

/* ajax callback function which will handle the process **/
function feyaroseorders_modal_ajax()
{
    include('calendar.php');
    include('calendarpeter.php');
    include('feyarose_orders_functions.php');
    /* get all products */
    $allProducts = unserialize(get_option('feyaroseorders_products_types'));
    // get the CITY(stock city) variable from feyaroseorders_admin.js
    $CITY = "Москва";
    $YEAR = $_POST['year'];
    $MONTH = $_POST['month'];
    //feyaroseorders_update_stock_data();
    $stockToDisplay = unserialize(get_option('feyaroseorders_stock'));


    $ID = intval($_POST['ID']);
    $isBouquet = true;
    foreach ($allProducts as $refType) {
        if($ID == $refType[1]) {
            if($refType[0] != 'Bouquet') {
                $stockToDisplay = unserialize(get_option('feyaroseorders_stock_'.$ID));
                $isBouquet = false;
                break;
            }
        }
    }
    $wp_post = get_post($ID);
    $stockLabel = ($isBouquet) ? 'Bouquet Stock' : $wp_post->post_title.' Stock';
    $stockType =  ($isBouquet) ? 'bouquet' : 'product';

    $cal = new Calendar($ID);
    $cal->setCity("Москва");
    $cal->setStockType($stockType);
    $cal->setStockLabel($stockLabel);
    $cal->setBaseUrl('?page=feyarose-orders%2Ffeyarose_orders.php');
    $cal->setStockData($stockToDisplay);
    $mosCalendar = $cal->show($YEAR,$MONTH);
    wp_send_json($mosCalendar);
}

add_action('wp_ajax_get_stock_modal', 'feyaroseorders_modal_ajax');
