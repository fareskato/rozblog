<?php
/**
 * Checkout login form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( is_user_logged_in() || 'no' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ) {
	return;
}
?>

<div class="col-lg-6">
<?php
$info_message  = apply_filters( 'woocommerce_checkout_login_message', __( 'Returning customer?', 'woocommerce' ) );
//$info_message .= ' <a href="#" class="showlogin">' . __( 'Click here to login', 'woocommerce' ) . '</a>';
wc_print_notice( $info_message, 'notice' );
?>
<div class="col-lg-12 ">
<!--  Add checkbox before login form  -->
    <div class="checkout-login-form-trigger hidden-sm hidden-md hidden-lg">
        <input type="checkbox" value="None" id="form-trigger" name="check" />
        <span><?php echo __('Sign up') ?></span>
        <label for=""></label>
        <span class="glyphicon glyphicon-chevron-down form-trigger-arrow" aria-hidden="true"></span>
    </div>
    <div class="clearfix"></div>
    <?php
    woocommerce_login_form(
        array(
            'message'  => '',//,__( 'If you have shopped with us before, please enter your details in the boxes below. If you are a new customer please proceed to the Billing &amp; Shipping section.', 'woocommerce' ),
            'redirect' => wc_get_page_permalink( 'checkout' ),
            'hidden'   => false
        )
    );
    ?>
</div>


        </div>


