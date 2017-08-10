<?php
/**
 * Checkout coupon form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! WC()->cart->coupons_enabled() ) {
	return;
}
?>
<div class="col-lg-6">
<?php
$info_message = __( 'Have a coupon?', 'woocommerce' );//. ' <a href="#" class="showcoupon">' . __( 'Click here to enter your code', 'woocommerce' ) . '</a>' );
wc_print_notice( $info_message, 'notice' );
?>
<div class="col-lg-12">
    <form class="checkout_coupon" method="post" style="display: block !important;">
        <div class="form-vcenter-container">
            <div class="form-vcenter-content">
        <p class="form-row form-row-first">
            <input type="text" name="coupon_code" class="input-text" placeholder="<?php _e( 'Coupon code', 'woocommerce' ); ?>" id="coupon_code" value="" />
        </p>

        <p class="form-row form-row-last">
            <input type="submit" class="button" name="apply_coupon" value="<?php _e( 'Apply Coupon', 'woocommerce' ); ?>" />
        </p>
</div>
            </div>

    </form>
</div>
    <div class="clear"></div>
</div>