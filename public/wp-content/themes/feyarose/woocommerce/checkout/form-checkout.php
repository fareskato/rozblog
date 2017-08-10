<?php
/**
 * Checkout Form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="col-lg-12" ><div class="woocommerce-message woocommerce-success twelve-roses-message"><?php echo less_than_twelve_rose(); ?></div></div>
<?php
wc_print_notices();

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout
if ( ! $checkout->enable_signup && ! $checkout->enable_guest_checkout && ! is_user_logged_in() ) {
	echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) );
	return;
}


// filter hook for include new pages inside the payment method
$get_checkout_url = apply_filters( 'woocommerce_get_checkout_url', WC()->cart->get_checkout_url() ); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( $get_checkout_url ); ?>" enctype="multipart/form-data">
    <div class="container-fluid">
	<?php if ( sizeof( $checkout->checkout_fields ) > 0 ) : ?>
        <div class="row-fluid">
		    <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
        </div>

		<div class="row-fluid" id="customer_details">
            <div class="col-lg-6 col-sm-12 col-xs-12 woocommerce_checkout_shipping-wrapper">
                <?php   do_action( 'woocommerce_checkout_shipping' ); ?>
            </div>
            <div class="col-lg-6 col-sm-12 col-xs-12 woocommerce_checkout_billing-wrapper">
                <?php do_action( 'woocommerce_checkout_billing' ); ?>
            </div>
            <div class="col-xs-12 hidden-sm hidden-md hidden-lg woocommerce_checkout_shipping-wrapper-recipient-data">
                <h3><?php echo __('Recipient','feyarose'); ?></h3>
            </div>
            <div class="col-xs-12 hidden-sm hidden-md hidden-lg ship-to-wrapper">
                <!-- Ship to this address -->
                <div class="col-xs-12 ship-this-address ship-to">
                    <?php
                    if ( empty( $_POST['ship_to_this_address'] ) ) {

                        $ship_to_this_address = get_option( 'woocommerce_ship_to_destination' ) === 'shipping' ? 1 : 0;
                        $ship_to_this_address = apply_filters( 'woocommerce_ship_to_different_address_checked', $ship_to_this_address );
                    } else {
                        $ship_to_different_address = $checkout->get_value( 'ship_to_different_address' );
                    }
                    ?>
                    <h3 id="ship-to-this-address">
                        <label for="ship-to-this-address-checkbox" class="checkbox" style="font-weight:normal;"><?php _e( 'Ship to this address?', 'woocommerce' ); ?></label>
                        <input id="ship-to-this-address-checkbox" checked="checked" class="input-checkbox" <?php checked( $ship_to_this_address, 1 ); ?> type="checkbox" name="ship_to_this_address" value="1" />
                    <span class="to-this-address-trigger hidden-sm hidden-md hidden-lg">
                        <span class="glyphicon glyphicon-ok hidden-sm hidden-md hidden-lg" aria-hidden="true"></span>
                    </span>

                    </h3>
                </div>
                <!-- Ship to different address -->
                <div class="col-xs-12 ship-another-address ship-to">
                    <?php
                    if ( empty( $_POST['ship_to_different_address'] ) ) {

                        $ship_to_different_address = get_option( 'woocommerce_ship_to_destination' ) === 'shipping' ? 0 : 1;
                        $ship_to_different_address = apply_filters( 'woocommerce_ship_to_different_address_checked', $ship_to_different_address );
                    } else {
                        $ship_to_different_address = $checkout->get_value( 'ship_to_different_address' );
                    }
                    ?>
                    <h3 id="ship-to-different-address">
                        <label for="ship-to-different-address-checkbox" class="checkbox" style="font-weight:normal;"><?php _e( 'Ship to a different address?', 'woocommerce' ); ?></label>
                        <input id="ship-to-different-address-checkbox" class="input-checkbox" <?php checked( $ship_to_different_address, 1 ); ?> type="checkbox" name="ship_to_different_address" value="0" />
                    <span class="to-another-address-trigger hidden-sm hidden-md hidden-lg">
                        <span class="glyphicon glyphicon-ok hidden-sm hidden-md hidden-lg" aria-hidden="true"></span>
                    </span>
                    </h3>
                </div>

                <div class="col-xs-12 hidden-sm hidden-md hidden-lg" id="another-billing-fields"></div>
            </div>

		</div>
        <!--
        <?php if(feyarose_get_cart_roses() > 0) : ?>
            <div class="row-fluid">
                <div class="col-lg-offset-6  col-lg-6 woocommerce-message woocommerce-success" style="margin-left: 50%; margin-bottom: 1em;">
                    <?php _e('Comme ya des roses dans ton panier, dis en commentaire ce que tu veux en faire','feyarose'); ?>
                </div>
            </div>
        <?php endif; ?>
        -->
        <div class="row-fluid">
            <div class="clear"></div>
		    <?php  do_action( 'woocommerce_checkout_after_customer_details' ); ?>
        </div>

        <div class="row-fluid">
            <div class="col-lg-12">
                <hr>
                <h3 id="order_review_heading"><?php _e( 'Your order', 'woocommerce' ); ?></h3>
            </div>

        </div>

	<?php endif; ?>
        <div class="row-fluid">
            <div class="col-lg-12">
                <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
            </div>
        </div>
        <div id="order_review" class="woocommerce-checkout-review-order row-fluid">
            <div class="col-lg-12">

                <?php do_action( 'woocommerce_checkout_order_review' ); ?>
            </div>
        </div>

	<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
    </div>
</form>
            <div class="container-fluid">
                <div class="row-fluid">
<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
                </div>
            </div>
        </div>
    </div>
</div>