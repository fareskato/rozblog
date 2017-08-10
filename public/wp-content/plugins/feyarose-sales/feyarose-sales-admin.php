<?php
/**
 * Created by PhpStorm.
 * User: fares-altima
 * Date: 15.06.2016
 * Time: 12:41
 */
    /* kick them out if access directly */
if (!defined("ABSPATH")) {
    exit;
}

?>


<?php
    /* Set default coupon value */
$default_coupon = null;
$dates_chosen = false;
$coupon_chosen = false;

$current_user = wp_get_current_user();
$roles = $current_user->roles;
$role = $roles[0];


if(!($role == 'administrator' || $role == 'shop_manager')) {
    $coupon_chosen = true;
    $default_coupon = 'rblavka10';
}

if(isset($_POST['filter']) && $_POST['coupons']){
    $default_coupon = $_POST['coupons'];
    if($_POST['coupons'] == 'all') {
        $coupon_chosen = false;
    } else {
        $coupon_chosen = true;
    }
}
/* get all coupons */
$args = array(
    'posts_per_page'   => -1,
    'orderby'          => 'title',
    'order'            => 'asc',
    'post_type'        => 'shop_coupon',
    'post_status'      => 'publish',
);
$coupons = get_posts( $args );
$coupon_names = array();
foreach ( $coupons as $coupon ) {
    $coupon_name = $coupon->post_title;
    array_push( $coupon_names, $coupon_name );
}
$coupon_names = array_unique($coupon_names);
    /* display the coupon list (depending on user role) */



    /* get all orders to display the total price*/
global $wpdb;
$whereDate='';
if(isset($_POST['filter'])){
    $dates_chosen = true;
    $from = $_POST['start_date'];
    $to = $_POST['end_date'];
    $twoDates = true;

    //if no date is chosen
    if(strlen(trim($_POST['start_date'])) < 2 && strlen(trim($_POST['end_date'])) < 2 ) {
        $twoDates = false;
        $whereDate = '';
    }

    //if there is only start date
    if(strlen(trim($_POST['start_date'])) > 2 && strlen(trim($_POST['end_date'])) < 2) {
        $whereDate = "AND DATE(posts.post_date) > '".$_POST['start_date']."'";
        $twoDates = false;
    }

    //if there is only end date
    if(strlen(trim($_POST['end_date'])) > 2 && strlen(trim($_POST['start_date'])) < 2) {
        $whereDate = "AND DATE(posts.post_date) < '".$_POST['end_date']."'";
        $twoDates = false;
    }

    if($twoDates == true) {
        // switch between start and end date
        if(strtotime($_POST['start_date']) > strtotime($_POST['end_date']) ){
            $from = $_POST['end_date'];
            $to = $_POST['start_date'];
        }else{
            $from = $_POST['start_date'];
            $to = $_POST['end_date'];
        }
        $whereDate = "AND DATE(posts.post_date) BETWEEN '$from' AND '$to'";
    }

}
$query = "SELECT * FROM {$wpdb->posts} as posts
            WHERE   posts.post_type     = 'shop_order'
            AND     posts.post_status   IN ( '" . implode( "','", array( 'wc-completed', 'wc-delivered' ) ) . "' )
            ".$whereDate."
            ORDER BY posts.post_date DESC ";

$all_orders = $wpdb->get_results( $query );


    /* create array for specific coupon */
$oneCouponArray = array();
foreach ($all_orders as $order){
    $order_obj = new WC_Order( $order->ID );
    $coupons = $order_obj->get_used_coupons();
    foreach ($coupons as $coupon){
        if($coupon_chosen == true) {
            if($coupon == $default_coupon){
                $oneCouponArray[] = $order_obj;
            }
        } else {
            $oneCouponArray[] = $order_obj;
        }

    }
}
$status = array('wc-completed'=>'Completed','wc-delivered'=>'Delivered');
    /* Display messages */
$total_price_for_date_or_all = 0;
$dat_currency = '';
foreach ($oneCouponArray as $date_item){
    $dat_currency = $date_item->order_currency;
    $total_filtered_price = $date_item->get_total();
    $total_price_for_date_or_all+=$total_filtered_price;
}
$formatted_price = number_format($total_price_for_date_or_all,0, ' ', ' ');

?>
<h2>Orders with coupons</h2>
<?php
if($default_coupon != 'all' && $coupon_chosen == true) {
    echo "<h3>Coupon used: ".$default_coupon."</h3>";
}

if(isset($_POST['filter'])){



    $from_date = ($from != '') ? date("d/m/Y", strtotime($from)) : '';
    $to_date = ($to != '') ? date("d/m/Y", strtotime($to) ) : '';
    echo "<div class=''>";
    echo "<h3>";
    if($from_date == '' && $to_date == '') {
        echo "Total is <strong>".$formatted_price."  ".$dat_currency."</strong>";
    }
    if($from_date != '' && $to_date == '') {
        echo "Total from ".$from_date." is <strong>".$formatted_price."  ".$dat_currency."</strong>";
    }
    if($from_date == '' && $to_date != '') {
        echo "Total until ".$to_date." is <strong>".$formatted_price."  ".$dat_currency."</strong>";
    }
    if($from_date != '' && $to_date != '') {
        echo "Total between ".$from_date." and ".$to_date." is <strong>".$formatted_price."  ".$dat_currency."</strong>";
    }
    echo "</h3>";
    echo "</div>";

}else{
    echo "<div class=''>
            <h3> Total  is <strong>$formatted_price  $dat_currency</strong></h3>
          </div>";
}
?>
<form action="" method="post" id="sales-form">
    <label for="start-date">From </label>
    <input type="text" class="filter_date" name="start_date" id="start-date" value="<?php echo  $_POST['start_date'] ?>"/>
    <label for="end-date">To</label>
    <input type="text" class="filter_date" name="end_date" id="end-date" value="<?php echo  $_POST['end_date'] ?>"/>
    <?php
    if($role == 'administrator' || $role == 'shop_manager'): ?>
            <label for="coupons-list">Select Coupon</label>
            <select name="coupons" id="coupons-list">
                <option value="all"<?php echo ($coupon_chosen == false || $default_coupon == 'all') ? 'selected' : '' ?>>All Coupons</option>
                <?php
                foreach ($coupon_names as $coupon_name){
                    $selected = ($coupon_name == $default_coupon) ? 'selected' : '';
                    echo "<option value='$coupon_name' ".$selected.">$coupon_name</option>";
                }
                ?>
            </select>
    <?php endif; ?>
    <input type="submit" name="filter" value="choose">
</form>
    <!-- Display the coupon select list depending on the user role -->
<table class="tablesorter" id="sales_table">
    <thead>
        <td>Status</td>
        <td>Purchased</td>
        <td>Customer</td>
        <td>Coupons</td>
        <td>Date</td>
        <td>Total</td>
    </thead>
    <tbody>
    <?php
        foreach ($oneCouponArray as $order):
        $coupons = $order->get_used_coupons();?>
                        <tr>
                            <th><?php echo  ($order->post_status == 'wc-completed') ? $status['wc-completed'] : $status['wc-delivered']  ;?></th>
                            <th><?php echo  "#". $order->id ." - ".   $order->billing_email ;?></th>
                            <th><?php echo $order->billing_first_name . " " . $order->billing_last_name  ?></th>
                            <th><?php foreach ($coupons as $coupon){echo $coupon;}  ?> </th>
                            <th><?php echo  get_post_time('Y/m/d',true,$order->id, true); ?></th>
                            <th><?php echo $order->get_formatted_order_total(); ?></th>
                        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
    <!-- pagination -->
    <div id="pager" class="pager">
        <form>
            <span class="first pager-button">First</span>
            <span class="prev pager-button">Previous</span>
            <span class="pagedisplay"></span>
            <span class="next pager-button">Next</span>
            <span class="last pager-button">Last</span>
        </form>
    </div>






