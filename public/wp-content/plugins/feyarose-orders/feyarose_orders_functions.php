<?php
/*
* @author Fares
*    Get the product ID:
**/
if(!function_exists('getProductId')) {
    function getProductId()
    {
        $allProducts = unserialize(get_option('feyaroseorders_products_types'));
        foreach ($allProducts as $key => $value) {
            $productID = $value[1];
        }
        return $productID;
    }
}

/*
* @author Fares
* Replace array key
**/
if(!function_exists('replace_key')) {
    function replace_key($find, $replace, $array) {
        $arr = array();
        foreach ($array as $key => $value) {
            if ($key == $find) {
                $arr[$replace] = $value;
            } else {
                $arr[$key] = $value;
            }
        }
        return $arr;
    }
}
if(!function_exists('feyaroseorders_get_details_order')) {
    function feyaroseorders_get_details_order($orderid)
    {
        $orderDetails = array(
            'post' => null,
            'items' => array(),
        );
        $sql="SELECT * FROM wp_posts where ID='".$orderid."' LIMIT 0,1";
        global $wpdb;
        $post = $wpdb->get_results($sql);
        $orderDetails['post'] = $post[0];
        $postmeta = $wpdb->get_results("SELECT * FROM wp_postmeta WHERE post_id =".$orderid);
        $metas = array();
        foreach($postmeta as $meta) {
            $metas[$meta->meta_key] = $meta->meta_value;
        }
        $orderDetails['post_meta'] = $metas;
        $orderDetails['customer'] = get_userdata( $orderDetails['post']->post_author );
        $itemquery = "SELECT * FROM wp_woocommerce_order_items WHERE order_id =".$orderid;
        $items =  $wpdb->get_results($itemquery);
        foreach($items as $item) {
            $itemmetaquery = "SELECT * FROM wp_woocommerce_order_itemmeta WHERE order_item_id =".$item->order_item_id;
            $itemMeta =  $wpdb->get_results($itemmetaquery);
            $metas = array();
            foreach($itemMeta as $meta) {
                $metas[$meta->meta_key] = $meta->meta_value;
            }
            $orderDetails['items'][$item->order_item_id] = array ('item' => $item, 'item_meta' => $metas);
        }
        return $orderDetails;
    }
}

//    and post_modified > subdate(now(),".$checkSpan.")
if(!function_exists('feyaroseorders_get_completed_orders')) {
    function feyaroseorders_get_completed_orders() {
        $lastCheck = get_option('feyaroseorders_lastcheck', mktime(0,0,0,1,1,2015));
        $now = mktime();
        $checkSpan = floor(($now - $lastCheck)/(60*60*24));

        update_option('feyaroseorders_lastcheck',  mktime(0,0,0,1,1,2015));

        global $wpdb;
        $sql="SELECT *
              FROM wp_posts
              where post_type='shop_order'
              and post_status in ('wc-completed', 'wc-on-hold', 'wc-processing', 'wc-delivered')
              and post_modified > subdate(now(),".$checkSpan.")
              order by post_modified desc";
        $posts = $wpdb->get_results($sql);
        $orders = array();
        if(count($posts) > 0) {
            foreach($posts as $order) {
                $orders[] = feyaroseorders_get_details_order($order->ID);
            }
        }
        return $orders;
    }
}
if(!function_exists('feyaroseorders_get_orders_for_deliverydate')) {
    function feyaroseorders_get_orders_for_deliverydate($ts, $city  = null) {
        $ordersDeliveryDates = unserialize(get_option('feyaroseorders_orderdeliverydates', serialize(array())));
        $orders = array();
        if($city != null) {
            foreach($ordersDeliveryDates[$ts][$city] as $orderid=>$orderstr) {
                $orders[$orderid] = feyaroseorders_get_details_order($orderid);
            }
        } else {
            foreach($ordersDeliveryDates[$ts] as $city_delivery => $orders_delivery) {
                foreach ($orders_delivery as $orderid => $orderstr) {
                    $orders[$orderid] = feyaroseorders_get_details_order($orderid);
                }
            }
        }


        return $orders;
    }
}
if(!function_exists('feyaroseorders_util_getMetaValue')) {
    function feyaroseorders_util_getMetaValue($metakeysearch, $metaArray)
    {
        foreach ($metaArray as $key => $value) {
            if (strpos($key, $metakeysearch) !== false) {
                return $value;
            }
        }
        return false;
    }
}
if(!function_exists('feyaroseorders_util_getSearchLabel')) {
    function feyaroseorders_util_getSearchLabel($name, $reverse_return = false, $reverse_search = false)
    {
        $name = str_replace('   ', ' - ', $name);
        $aname = explode(' - ', $name);
        if (isset($aname[1])) {
            $name = $aname[1];
        }
        //echo ICL_LANGUAGE_CODE;
        $listOfNames = array(
            'Количество роз в букете' => array('id' => 'nbroses', 'en' => 'Quantity of roses in the bouquet', 'ru' => 'Количество роз в букете'),
            'Дата доставки' => array('id' => 'deliverydate', 'en' => 'Date of delivery', 'ru' => 'Дата доставки'),
            'Временной интервал доставки' => array('id' => 'deliveryperiod', 'en' => 'Time of delivery', 'ru' => 'Временной интервал доставки'),
            'Улица' => array('id' => 'deliverystreet', 'en' => 'Street', 'ru' => 'Улица'),
            'Домофон\/код' => array('id' => 'deliverycode', 'en' => 'Intercom/code', 'ru' => 'Домофон/код'),
            'Дом' => array('id' => 'deliverybuilding', 'en' => 'Home', 'ru' => 'Дом'),
            'Квартира' => array('id' => 'deliveryappartment', 'en' => 'Apartment', 'ru' => 'Квартира'),
            'Этаж' => array('id' => 'deliveryetage', 'en' => 'Floor', 'ru' => 'Этаж'),
            'Метро' => array('id' => 'deliverymetro', 'en' => 'Metro', 'ru' => 'Метро'),
            'Город' => array('id' => 'deliverytown', 'en' => 'City', 'ru' => 'Город'),
            'Адрес доставки' => array('id' => 'deliveryaddress', 'en' => 'Delivery address', 'ru' => 'Адрес доставки'),
            'ФИО' => array('id' => 'recipientname', 'en' => 'Full name', 'ru' => 'ФИО'),
            'Телефон' => array('id' => 'recipientphone', 'en' => 'Phone', 'ru' => 'Телефон'),
            'Получатель' => array('id' => 'recipient', 'en' => 'Receiver', 'ru' => 'Получатель'),
            'postcard_flowers' => array('id' => 'postcard', 'en' => 'Postcard to flowers', 'ru' => 'Открытка к букету'),
            'no_postcards' => array('id' => 'postcard-without', 'en' => 'no card', 'ru' => 'Без открытки'),
            'krem_piag' => array('id' => 'postcard-krim-piaget', 'en' => 'Cream Piaget', 'ru' => 'Крим Пьяже'),
            'anjy_romantika' => array('id' => 'postcard-andgiromantika', 'en' => 'Angie Romantica', 'ru' => 'Анджи Романтика'),
            'pink_romantika' => array('id' => 'postcard-pink-romantika', 'en' => 'Pink Romantica', 'ru' => 'Пинк Романтика'),
            "pink_oharaa" => array('id' => 'postcard-pink-ohara', 'en' => 'Pink O’Hara', 'ru' => "Пинк О'Хара"),
            'iv_pyadg' => array('id' => 'postcard-yves-piaget', 'en' => 'Yves Piaget', 'ru' => 'Ив Пьяже'),
            'av_angard' => array('id' => 'postcard-avant-garde', 'en' => 'Avant-Garde', 'ru' => 'Авангард'),
            'Текст на открытке' => array('id' => 'postcard-message', 'en' => 'Text on the card', 'ru' => 'Текст на открытке'),
            'Комментарий к доставке' => array('id' => 'delivery-comment', 'en' => 'Commentary on delivery', 'ru' => 'Комментарий к доставке'),
            'Москва' => array('id' => 'stock-city-moscow', 'en' => 'Moscow', 'ru' => 'Москва'),
            'Санкт-Петербург' => array('id' => 'stock-city-peter', 'en' => 'Saint-Petersburg', 'ru' => 'Санкт-Петербург'),
            'В пределах КАД' => array('id' => 'stock-city-mkad-peter', 'en' => 'For the KAD', 'ru' => 'В пределах КАД'),
            'За КАД в пределах Санкт-Петербурга' => array('id' => 'stock-city-peter-kad', 'en' => 'Within the KAD St. Petersburg', 'ru' => 'За КАД в пределах Санкт-Петербурга'),
            'В пределах МКАД' => array('id' => 'stock-city-mkad-mosc', 'en' => 'Within the MKAD', 'ru' => 'В пределах МКАД'),
            'Зеленоградский АО; Троицкий АО' => array('id' => 'stock-city-grad', 'en' => 'Zelenograd AO ; Trinity АО', 'ru' => 'Зеленоградский АО ; Троицкий АО'),
            'За МКАД в пределах Москвы \(кроме Зеленоградского АО и Троицкого АО\)' => array('id' => 'stock-city-trinity', 'en' => 'Outside MKAD within Moscow (except Zelenograd АО and Trinity АО )', 'ru' => 'За МКАД в пределах Москвы (кроме Зеленоградского АО и Троицкого АО)'),
            'Delivery period' => array('id' => 'deliveryperiod', 'en' => 'Delivery period', 'ru' => 'Delivery period'),
//        'TEST'                    => array('id'=>'additional_field_620','en'=>'TEST-en','ru'=>'TEST'),
        );
        foreach ( $listOfNames as $origName => $aTranslation ) {
            if($reverse_search == true) {
                if ( preg_match( "/^" . strtolower( $aTranslation['id'] ) . "(.*)/i", strtolower( $name ) ) > 0 ) {
                    if ( $reverse_return == true ) {
                        return $aTranslation[ICL_LANGUAGE_CODE];
                    }
                    return $origName;
                }
            } else {
                if ( preg_match( "/^" . strtolower( $origName ) . "(.*)/i", strtolower( $name ) ) > 0 ) {
                    if ( $reverse_return == true ) {
                        return $aTranslation[ICL_LANGUAGE_CODE];
                    }
                    return $aTranslation['id'];
                }
            }
        }
        return $name;
    }
}

/* Update stock function */
if(!function_exists('feyaroseorders_update_stock_data')) {
    function feyaroseorders_update_stock_data($orderid = null)
    {
        $ordersDeliveryDates = unserialize(get_option('feyaroseorders_orderdeliverydates', serialize(array())));
        $productsTypesReference = unserialize(get_option('feyaroseorders_products_types'));
        if($productsTypesReference == null) {
            $productsTypesReference = array();
            update_option('feyaroseorders_products_types',serialize($productsTypesReference));
        }

        $list_stocks = unserialize(get_option('feyaroseorders_stock'));
        update_option('feyaroseorders_stock', serialize($list_stocks));
        /*END REMISE A ZERO DES QTY TO DELIVER*/
        if ($orderid == null) {
            $orders = feyaroseorders_get_completed_orders();
        } else {
            $orders = array();
            $orders[] = feyaroseorders_get_details_order($orderid);
        }

        if (count($orders) > 0) {
            $list_stocks = unserialize(get_option('feyaroseorders_stock'));
            $aStocksToUpdate = array();
            foreach ($orders as $order) {
                //get the order meta of the delivery town and delivery date
                $city = null;
                $deliveryDate = null;
                if (isset($order['post_meta']['additional_field_394'])) {
                    $city = $order['post_meta']['additional_field_394'];
                }
                foreach ($order['items'] as $itemObj) {
                    $item_productId = $itemObj['item_meta']['_product_id'];
                    if($item_productId != null) {
                        $roses = feyaroseorders_util_getMetaValue(feyaroseorders_util_getSearchLabel('nbroses', false, true), $itemObj['item_meta']);
                        $city_item = ($city == null) ? feyaroseorders_util_getMetaValue(feyaroseorders_util_getSearchLabel('deliverytown', false, true), $itemObj['item_meta']) : $city;
                        $qty = feyaroseorders_util_getMetaValue('_qty', $itemObj['item_meta']);
                        $isBouquet = true;

                        $roseStock = unserialize(get_option('feyaroseorders_stock_'.$item_productId, null));
                        // check if the order is rose or bouquet
                        foreach($productsTypesReference as $refType){
                            if($refType[1] == $item_productId){
                                if($refType[0] == 'Bouquet'){
                                   $roseStock = null;
                                }
                            }
                        }

                        if($roseStock == null) {
                            $tempStock = $list_stocks;
                        } else {
                            if(array_key_exists('feyaroseorders_stock_'.$item_productId,$aStocksToUpdate)) {
                                $tempStock = unserialize($aStocksToUpdate['feyaroseorders_stock_'.$item_productId]);
                            } else {
                                $tempStock = unserialize(get_option('feyaroseorders_stock_'.$item_productId));
                            }
                            $isBouquet = false;
                        }

                        if($tempStock == null) {
                            $tempStock = array();
                        }
                        $deliveryTS = null;
                        if (isset($order['post_meta']['additional_field_98'])) {
                            $deliveryDate = $order['post_meta']['additional_field_98'];
                        } else {
                            $deliveryDate = feyaroseorders_util_getMetaValue(feyaroseorders_util_getSearchLabel('deliverydate', false, true), $itemObj['item_meta']);
                        }

                        $dateExpl = explode('-', $deliveryDate);
                        //print_r($dateExpl);
                        $deliveryTS = ($deliveryTS == null) ? mktime(0, 0, 0, $dateExpl[1], $dateExpl[2], intval($dateExpl[0])) : $deliveryTS;

                        if(!isset($tempStock[$deliveryTS])) {
                            $tempStock[$deliveryTS] = array();
                        }
                        if(!isset($tempStock[$deliveryTS]['orders'])) {
                            $tempStock[$deliveryTS]['orders'] = array();
                        }
                        //if the order is not in the records, update stocks
                        if (!isset($tempStock[$deliveryTS]['orders'][$order['post']->ID])) {
                            $tempStock[$deliveryTS]['orders'][$order['post']->ID] = array();
                        }
                        if(!isset($tempStock[$deliveryTS]['to_deliver'])) {
                            $tempStock[$deliveryTS]['to_deliver'] = 0;
                        }


                        //if the item is not in the order record
                        if (!isset($tempStock[$deliveryTS]['orders'][$order['post']->ID][$itemObj['item']->order_item_id])) {
                            $tempStock[$deliveryTS]['orders'][$order['post']->ID][$itemObj['item']->order_item_id] = $itemObj['item']->order_item_id;
                            $tempStock[$deliveryTS]['to_deliver'] = ($isBouquet) ? $tempStock[$deliveryTS]['to_deliver'] + ($roses * $qty) : $tempStock[$deliveryTS]['to_deliver'] + $qty ;
                        }
                        //update the general option for deliveries

                        if(!isset($ordersDeliveryDates[$deliveryTS])) {
                            $ordersDeliveryDates[$deliveryTS] = array();
                            array_push($ordersDeliveryDates[$deliveryTS], $order['post']->ID);
                        } else {
                            array_push($ordersDeliveryDates[$deliveryTS], $order['post']->ID);
                        }
                        if(!isset($ordersDeliveryDates[$deliveryTS][$city_item])) {
                            $ordersDeliveryDates[$deliveryTS][$city_item] = array();
                            array_push($ordersDeliveryDates[$deliveryTS][$city_item], $order['post']->ID);
                        } else {
                            array_push($ordersDeliveryDates[$deliveryTS][$city_item], $order['post']->ID);
                        }
                        if($isBouquet == true) {
                           $list_stocks = $tempStock;
                        } else {
                            $aStocksToUpdate['feyaroseorders_stock_'.$item_productId]= serialize($tempStock);
                        }
                    }
                }
            }
            update_option('feyaroseorders_stock', serialize($list_stocks));
            if(count($aStocksToUpdate) > 0) {
                foreach($aStocksToUpdate as $sName => $sObj) {
                    update_option($sName, $sObj);
                }
            }

        }
        /*update general delivery dates*/
        update_option('feyaroseorders_orderdeliverydates', serialize($ordersDeliveryDates));
        /*Update disabled dates*/
        $dates_disabled = array();
        $delayMax = get_option('feyaroseorders_delaymaxdelivery');
        $tsMax = mktime(0, 0, 0, date('m'), date('d') + $delayMax, date('Y'));
        foreach ($list_stocks as $stockDate) {
            if ($stockDate['date'] > $tsMax) {
                break;
            }
            if ($stockDate['stock'] - $stockDate['to_deliver'] <= 0) {
                $dates_disabled[] = date('Y-m-d', $stockDate['date']);
            }
        }
        update_option('feyaroseorders_datesdisabled', serialize($dates_disabled));
        return true;
    }
}

function feyaroseorders_init_stocks()
{
    $productsTypesReference = unserialize(get_option('feyaroseorders_products_types'));
    $bouquetStock = unserialize(get_option('feyaroseorders_stock'));
    foreach ($productsTypesReference as $ref) {
        $productType = $ref[0];
        $productID = $ref[1];
        $roseStock = ($productType == 'Bouquet') ? null : unserialize(get_option('feyaroseorders_stock_'.$productID));
        for ($i = 0; $i <= 90; $i++) {
            $ts = mktime(0, 0, 0, date('m'), date('d') + $i, date('Y'));
            if($productType == 'Bouquet'){
                if(!array_key_exists($ts, $bouquetStock) || !isset($bouquetStock[$ts]) || empty($bouquetStock[$ts])){
                    $bouquetStock[$ts] = array(
                        'date' => $ts,
                        'stock' => 500,
                        'orders' => array(),
                        'to_deliver' => 0
                    );
                }
                 /*
                    if($bouquetStock[$ts]['stock'] == 0){
                        $bouquetStock[$ts]['stock'] = 500;
                    }
                  */
            }else{
                if(!is_array($roseStock)) {
                    $roseStock = array();
                }
                if(!array_key_exists($ts, $roseStock) || !isset($roseStock[$ts]) || empty($roseStock[$ts])){
                    $roseStock[$ts] = array(
                        'date' => $ts,
                        'stock' => 100,
                        'orders' => array(),
                        'to_deliver' => 0
                    );
                }
                /*
                if( $roseStock[$ts]['stock'] == 0){
                    $roseStock[$ts]['stock'] = 100;
                }
                */

            }
        }
        if($roseStock != null) {
            update_option('feyaroseorders_stock_'.$productID,serialize($roseStock));
        }

    }
    update_option('feyaroseorders_stock',serialize($bouquetStock));
}




// Init the stock and update it (3 months)
/*
if(!function_exists('feyaroseorders_init_stocks')) {
    function feyaroseorders_init_stocks(){
        $productsTypesReference = unserialize(get_option('feyaroseorders_products_types'));
        $bouquetStock = unserialize(get_option('feyaroseorders_stock'));
        $aStocks = array();
        foreach($productsTypesReference as $ref) {
            $productType = $ref[0];
            $productID = $ref[1];
            for($i = 0; $i <= 90; $i++){
                $ts = mktime(0, 0, 0, date('m'), date('d')+ $i, date('Y'));
                $defaultStock = 500;
                $stockObj = $bouquetStock;
                if($productType != 'Bouquet'){
                    $defaultStock = 100;
                    $stockName ='feyaroseorders_stock_'.$productID;
                    if(!isset($aStocks[$productID])) {
                        $stockObj = unserialize(get_option($stockName));
                        if(!is_array($stockObj)) {
                            $stockObj = array();
                        }
                        $aStocks[$productID] = $stockObj;
                    }
                }
                if(!array_key_exists($ts, $stockObj) || !isset($stockObj[$ts]) || empty($stockObj[$ts])){
                    $stockObj[$ts] = array(
                        'date' => $ts,
                        'stock' => $defaultStock,
                        'orders' => array(),
                        'to_deliver' => 0
                    );
                }

                if($stockObj[$ts]['stock'] == 0 ){
                    $stockObj[$ts]['stock'] == $defaultStock;
                }

                if($productType != 'Bouquet'){
                    $aStocks[$productID] = $stockObj;
                } else {
                    $bouquetStock = $stockObj;
                }

            }
        }
        foreach($aStocks as $pid => $stockObj) {
            update_option('feyaroseorders_stock_'.$pid, serialize($stockObj));
        }
        update_option('feyaroseorders_stock', serialize($bouquetStock));
    }
}
*/





