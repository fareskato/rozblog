<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}



function generate_minicart_items ($items) {
    foreach ( $items as $cart_item_key => $cart_item ) {
        $_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
        $product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

        if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {

            $product_name  = '<span class="minicart-productname">'.$_product->get_title().'</span>';//apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
            $thumbnail     = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
            $product_price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
            ?>
            <li>
                <?php echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf( '<a href="%s" class="remove" title="%s">&times;</a>', esc_url( WC()->cart->get_remove_url( $cart_item_key ) ), __( 'Remove this item', 'woocommerce' ) ), $cart_item_key ); ?>
                <?php if ( ! $_product->is_visible() ) : ?>
                    <?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ) . $product_name . '&nbsp;'; ?>
                <?php else : ?>

                    <?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ) . $product_name . '&nbsp;'; ?>

                <?php endif; ?>
                <?php echo WC()->cart->get_item_data( $cart_item ); ?>

                <?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); ?>
            </li>
        <?php
        }
    }
}

?>

<?php do_action( 'woocommerce_before_mini_cart' ); ?>

<ul class="cart_list product_list_widget <?php echo $args['list_class']; ?>">

	<?php if ( sizeof( WC()->cart->get_cart() ) > 0 ) : ?>

		<?php

        //$items = WC()->cart->get_cart();
        $roses = array();
        $bouquets = array();
        foreach(WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $cart_product_id =  $cart_item['product_id'];
            $cart_product_terms = get_the_terms($cart_product_id,'product_cat');
            $cat_name='';
            if(count($cart_product_terms)>0 && $cart_product_terms != false) {
                foreach($cart_product_terms as $cart_product_category) {
                    $cat_name =  $cart_product_category->name;
                }
            }
            if(($cat_name == 'roz') || ($cat_name == 'Roses') || ($cat_name == 'rose') || ($cat_name == 'Розы') ) {
                $roses[$cart_item_key] = $cart_item;
            } else {
                $bouquets[$cart_item_key] = $cart_item;
            }
        }
        if(count($roses) > 0) {
            echo '<li class="minicart-title">'.__('compose your bouquet', 'woothemes').'</li>';
            generate_minicart_items($roses);
            $msg_roses = less_than_twelve_rose(WC());

            if($msg_roses != '') {
                echo "<li class='minicart-details'>".$msg_roses."</li>";
            }
        }
        if(count($bouquets) > 0) {
            echo '<li class="minicart-title">'.__('bouquet mix', 'woothemes').'</li>';
            generate_minicart_items($bouquets);
        }


		?>

	<?php else : ?>

		<li class="empty"><?php _e( 'No products in the cart.', 'woocommerce' ); ?></li>

	<?php endif; ?>

</ul><!-- end product list -->
<div class="clearfix"></div>
<?php if ( sizeof( WC()->cart->get_cart() ) > 0 ) : ?>

	<p class="total"><strong><?php _e( 'Subtotal', 'woocommerce' ); ?>:</strong> <?php echo WC()->cart->get_cart_subtotal(); ?></p>

	<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

	<p class="buttons">
		<a href="<?php echo WC()->cart->get_cart_url(); ?>" class="btn btn-primary wc-forward"><?php _e( 'View Cart', 'woocommerce' ); ?></a>
		<a href="<?php echo WC()->cart->get_checkout_url(); ?>" class="btn btn-primary checkout wc-forward"><?php _e( 'Checkout', 'woocommerce' ); ?></a>
	</p>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
