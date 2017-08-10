<?php
/**
 * Created by PhpStorm.
 * User: Florian
 * Date: 5/12/2015
 * Time: 5:00 PM
 */

// Define Product Types
$types = array('Bouquet', 'Rose');
// Update the stock(Initial)
feyaroseorders_init_stocks();
feyaroseorders_update_stock_data();
// fearch element in array
function search_array($needle, $haystack)
{
    if (in_array($needle, $haystack)) {
        return true;
    }
    foreach ($haystack as $element) {
        if (is_array($element) && search_array($needle, $element))
            return true;
    }
    return false;
}
/*
* @author Fares
*    Get all products from wp_posts table
**/
global $wpdb;
$products = $wpdb->get_results("SELECT * FROM wp_posts WHERE post_type = 'product'");
/*
* @author Fares
*    Create product option(save the product type and ID in the options table)
**/
if ($_POST['select'] == 'Select') {
    $option = $_POST['option'];
    $product_type = $option['product_type'];
    $product_name = $option['product_id'];
    $newInfo[] = array($product_type, $product_name);
    $old_arr = unserialize(get_option('feyaroseorders_products_types'));
    if (count($old_arr) == 0) {
        update_option('feyaroseorders_products_types', serialize($newInfo));
    } else {
        $get_new_new_arr = unserialize(get_option('feyaroseorders_products_types'));
        $newType = $option['product_type'];
        $newname = $option['product_id'];
        if (search_array($newname, $get_new_new_arr)) {
            echo "<div class='error'><h3>" . __('You Have selected this product! please select another one') . "</h3></div>";
        } else {
            $newArray[] = array($newType, $newname);
            $upd_new_new_arr = array_merge($get_new_new_arr, $newArray);
            update_option('feyaroseorders_products_types', serialize($upd_new_new_arr));
            echo "<div class='updated'><h3>" . __('The product has been selected') . "</h3></div>";
        }
    }
}
// Handle form sending
echo "<h2>" . __('Feyarose Stocks') . "</h2>";
//echo "<h4>" . __('Default settings', 'feyaroseorders_trdom') . "</h4>";
if(isset($_POST['rozstore'])) {
    $city = $_POST['rozstore'];
}
$allProducts = unserialize(get_option('feyaroseorders_products_types'));
if ($_POST['feyaroseorders_hidden'] == 'Stocks') {
    $stockToUpdate = null;
    if($_POST['stocktype'] == 'bouquet') {
        if($_POST['rozstore'] == 'Москва') {
            $optionToGet = 'feyaroseorders_stock';
        } else {
            $optionToGet = 'feyaroseorders_peter_stock';
        }
    } else {
        if($_POST['rozstore'] == 'Москва') {
            $optionToGet = 'feyaroseorders_stock_'.$_POST['product_id'];
        } else {
            $optionToGet = 'feyaroseorders_peter_stock_'.$_POST['product_id'];
        }
    }
    $stockToUpdate = unserialize(get_option($optionToGet));
    foreach($_POST['list_stock'] as $stockTS => $stockValue) {
        $stockToUpdate[$stockTS]['stock'] = $stockValue;
    }
    update_option($optionToGet,serialize($stockToUpdate));
    echo "<div class='updated'><h3>" . __('Stocks have been updated') . "</h3></div>";
}
if ($_POST['feyaroseorders_hidden'] == 'Settings') {
    update_option('feyaroseorders_nbrosesbase', $_POST['feyaroseorders_nbrosesbase']);
//    update_option('feyaroseorders_nbrosesbase_rose', $_POST['feyaroseorders_nbrosesbase_rose']);
    update_option('feyaroseorders_delaymaxdelivery', $_POST['feyaroseorders_delaymaxdelivery']);
    echo '<div class="updated"><p><strong>' . __('Settings saved.') . '</strong></p></div>';
}
// check the store
$orders_completed = feyaroseorders_get_completed_orders();
foreach ($orders_completed as $orders) {
    $city = $orders['post_meta']['additional_field_76'];
    if ($city == "Москва") {
        $Moscow = $city;
    } elseif ($city == "Санкт-Петербург") {
        $peters = $city;
    }
}
$cites = array('Москва', 'Санкт-Петербург');
$city = "Москва";
// check the store city to create Initial stock for roses(100 rose)
if(isset($_POST['rozstore'])) {
    $city = $_POST['rozstore'];
}
// create default stock for Roses(100 rose)
/*
foreach ($products as $type => $product) {
    $productID = $product->ID;
    $list_stocks = ($city == "Москва") ? unserialize(get_option('feyaroseorders_stock_' . $productID)) : unserialize(get_option('feyaroseorders_peter_stock_' . $productID));
    if (empty($list_stocks)) {
        $list_stocks = array();
        $start_date = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        for ($i = 0; $i < 365; $i++) {
            $temp_date = $start_date + ($i * 86400);
            $orders = array();
            $list_stocks[$temp_date] = array(
                'date' => $temp_date,
                'stock' => 100,
                'orders' => $orders,
                'to_deliver' => 0
            );
        }
        ($city == "Москва") ? update_option('feyaroseorders_stock_' . $productID, serialize($list_stocks)) : update_option('feyaroseorders_peter_stock_' . $productID, serialize($list_stocks));
    }
}
*/
// Update the stock after create Initial stock
//feyaroseorders_update_stock_data();

//
/*
echo "<pre>";
    print_r(unserialize(get_option('feyaroseorders_stock')  ));
echo "</pre>";
*/
//
//




?>
<div class="wrap">
    <!-- Choose the store -->
    <?php /*
    <form method="post"
          action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>">
        <?php
        echo "<label for='store-select'>" . __('Select your store ') . "</label>";
        echo "<select name='rozstore' id='store-select'>";
        foreach ($cites as $storeCity) {
            $selected = ($_POST['rozstore'] == $storeCity) ? 'selected="selected"' : '';
            echo '<option value="' . $storeCity . '" ' . $selected . '>' . $storeCity . '</option>';
        }
        echo "</select'>";
        echo "<input type='submit' name='store' value='Select'/>";
        ?>
    </form>
    */
    ?>
</div>
<div class="wrap">
    <?php echo "<h4>" . __('Stocks', 'feyaroseorders_trdom') . "</h4>"; ?>
    <div class="wrap">
        <form action="" method="POST">
            <label for="prodtype"><?php echo __('Product Type'); ?></label>
            <select name="option[product_type]" id="prodtype">
                <?php
                foreach ($types as $type) {
                    $selected = ($option['product_type'] == $type) ? 'selected="selected"' : '';
                    echo "<option value='$type' $selected >$type</option>";
                }
                ?>
            </select>
            <label for="productname"><?php echo __('Product Name'); ?></label>
            <select name="option[product_id]" id="productname">
                <?php
                foreach ($products as $type => $product) {
                    $productname = $product->post_title;
                    $selected = ($option['product_id'] == $product->ID) ? 'selected="selected"' : '';
                    echo "<option value='$product->ID' $selected >$productname</option>";
                }
                ?>
            </select>
            <input type="submit" name="select" value="Select">
        </form>
        <table class="widefat" style="width:70%" cellspacing="0">
            <thead>
            <tr>
                <th class="manage-column column-columnname"><?php echo __('Product Type'); ?></th>
                <th class="manage-column column-columnname"><?php echo __('Product Name'); ?></th>
                <th class="manage-column column-columnname"><?php echo __('Stock Details'); ?></th>
                <th class="manage-column column-columnname"><?php echo __('Actions'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            // Get all products from  wp_option table(feyaroseorders_products_types).
            echo "<h2>" . __('All Products') . "</h2>";
            // Delete ROSE STOCK WHEN CLICK ON DELETE BUTTON
            if(isset($_POST['delete']) && $_POST['delete'] == 'Delete') {
                $oldData = unserialize(get_option('feyaroseorders_products_types'));
                $tempData = array();
                for ($i = 0; $i < count($oldData); $i++) {
                    if ($i != $_POST['delete_product']) {
                        $tempData[] = $oldData[$i];
                        $deletedId = $oldData[$_POST['delete_product']][1];
                        $deletedType = $oldData[$_POST['delete_product']][0];
                        //if it was a rose, delete the stock of peter and moscow
                        if($deletedType != 'Bouquet') {
                            delete_option('feyaroseorders_stock_'.$deletedId);
                            delete_option('feyaroseorders_peter_stock_'.$deletedId);
                        }
                    }
                }
                update_option('feyaroseorders_products_types', serialize($tempData));
            }
            // UPDATE PRODUCT TYPE WHEN CLICK ON UPDATE BUTTON
            if(isset($_POST['change-product_type']) && ($_POST['update'] == 'Update') ) {
                $oldDataToUpdate = unserialize(get_option('feyaroseorders_products_types'));
                $tempDataToUpdate = array();
                for ($i = 0; $i < count($oldDataToUpdate); $i++) {
                    if ($i == $_POST['updated_product']) {
                        $tempDataToUpdate1[] = $oldDataToUpdate[$i];
                        $updatedProductId = $tempDataToUpdate1[0][1];
                        if($tempDataToUpdate1[0][0] != 'Bouquet') {
                            delete_option('feyaroseorders_stock_'.$updatedProductId);
                            delete_option('feyaroseorders_peter_stock_'.$updatedProductId);
                        }
                        $tempDataToUpdate1[0][0] = $_POST['change-product_type'];
                    }
                    if ($i != $_POST['updated_product']) {
                        $tempDataToUpdate2[] = $oldDataToUpdate[$i];
                        $updatedProductId = $tempDataToUpdate2[0][1];
                    }
                }
                $newUpdatedArray = array_merge($tempDataToUpdate1,$tempDataToUpdate2);
                update_option('feyaroseorders_products_types', serialize($newUpdatedArray));
            }
            $allProducts = unserialize(get_option('feyaroseorders_products_types'));
            foreach ($allProducts as $key => $value) {
                $productType = $value[0];
                $productID = $value[1];
            }
            foreach ($allProducts as $deletedKey => $value) {
                $productType = $value[0];
                $productID = $value[1];
                foreach ($products as $type => $product) {
                    if ($productID == $product->ID) {
                        $productName = $product->post_title;
                    }
                }
                echo "<tr class='alternate'>"; ?>
                <td class="\column-columnname\">
                    <form action="" method="post">
                        <select name="change-product_type" id="selectedProductType">
                            <?php
                            foreach($types as $type) {
                                $selected = ($productType == $type) ? 'selected="selected"' : '';
                                echo "<option value='$type' $selected >$type</option>";
                            }
                            ?>
                        </select>
                        <input type='submit' id='<?php echo $productID; ?>' class='<?php echo $productType; ?>' name='update' value='Update' />
                        <input type='hidden' name='updated_product' value='<?php echo  $deletedKey   ; ?> '/>
                    </form>
                </td>
                <?php
                echo "<td class=\"column-columnname\">" . $productName . "</td>";
                echo "<td class=\"column-columnname \">
                <a  href='wp-admin/admin.php?page=feyarose-orders%2Ffeyarose_orders&product_id=$productID'  id='$productID' class='view-modal' data-target='#rozblog_stock_modal'  >" . __('View Stock') . "</a></td>";
                echo "<td><form action='' method='POST'><input type='submit' id='$productID' class='button button-primary $productType' name='delete' value='Delete' />"
                    ."<input type='hidden' name='delete_product' value='" . $deletedKey . "'/></form></td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
            ?>
            <!-- Modal start : inside this modal we will display the stock calendar -->
            <div id="rozblog_stock_modal" class="modal fade stock-popup">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-body">
                            <!-- here goes the ajax response -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default"
                                    data-dismiss="modal"><?php echo __("Close"); ?></button>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <!-- Modal end -->
</div>

