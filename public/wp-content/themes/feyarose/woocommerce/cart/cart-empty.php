<?php
/**
 * Empty cart page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

wc_print_notices();

?>

<p class="cart-empty"><?php _e( 'Your cart is currently empty.', 'woocommerce' ) ?></p>

<?php do_action( 'woocommerce_cart_is_empty' ); ?>

<p class="return-to-shop">
    <?php
        $post_id       = icl_object_id( 12610, 'page', true, ICL_LANGUAGE_CODE );
        $post_url      = get_permalink( $post_id );
    ?>

    <a class="button wc-backward" href="<?php echo $post_url;//echo apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ); ?>">
        <?php _e( 'Return To Shop', 'woocommerce' )  ?>
    </a>
</p>
