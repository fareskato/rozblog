<?php
/**
 * Cart Page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<!-- Display message if there are less than 12 roses in the cart -->
<div class="col-lg-12" ><div class="woocommerce-message woocommerce-success twelve-roses-message"><?php echo less_than_twelve_rose(); ?></div></div>
<?php
wc_print_notices();
do_action( 'woocommerce_before_cart' ); ?>
<div class="col-lg-12">
<form action="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" method="post">
<?php do_action( 'woocommerce_before_cart_table' ); ?>
<table class="shop_table cart" cellspacing="0">
	<thead>
		<tr>
			<!-- <th class="product-thumbnail">&nbsp;</th> -->
			<th class="product-name" colspan="2"><?php _e( 'Product', 'woocommerce' ); ?></th>
			<th class="product-price"><?php //_e( 'Price', 'woocommerce' ); ?></th>
			<th class="product-quantity"><?php _e( 'Quantity', 'woocommerce' ); ?></th>
			<th class="product-subtotal"><?php _e( 'Total', 'woocommerce' ); ?></th>
            <th class="product-remove">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
    <?php
    //tests

    ?>
		<?php do_action( 'woocommerce_before_cart_contents' ); ?>
		<?php
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product= apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				?>
                <!-- FARES: Check if the product is simple or not -->
				<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
					<td class="product-thumbnail">
						<?php
							$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
							if ( ! $_product->is_visible() )
								//echo $thumbnail;
								printf( '<span >%s</span>', $thumbnail );
                          /*  Check if the product is bouquet */
                                $bouquet = $_product->post->post_title;
                         $imagedir =    get_stylesheet_directory_uri();
                        if($bouquet == "bouquet" || $bouquet == "Букет"){
                            $bouquetType = $cart_item['addons'][2]['value'];
                            switch($bouquetType){
                                case "Серебряный микс":
                                case "Silver mix":
                                $thumbnail = "<img src=' $imagedir/images/products/silver.jpg ' />";
                                    break;
                                case "Пастельный микс":
                                case "Pastel mix":
                                    $thumbnail = "<img src=' $imagedir/images/products/pastel.jpg ' />";
                                    break;
                                case "Осенний микс":
                                case "Autumn mix":
                                    $thumbnail = "<img src=' $imagedir/images/products/autumnal.jpg ' />";
                                    break;
                                case "Темный микс":
                                case "Dark mix":
                                    $thumbnail = "<img src=' $imagedir/images/products/dark.JPG ' />";
                                    break;
                            }
                            printf( '<span >%s</span>', $thumbnail );
                        } else {
                            printf( '<span >%s</span>', $thumbnail );
                        }
						?>
					</td>
                    <!-- FARES:hide the product name -->
					<td class="product-name">
                        <div class="hide">
                            <?php
							if ( ! $_product->is_visible() )
								echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ) . '&nbsp;';
							else
								echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s </a>', $_product->get_permalink( $cart_item ), $_product->get_title() ), $cart_item, $cart_item_key );
                        ?>
                        </div>
                        <?php
							// Meta data
//							 echo WC()->cart->get_item_data( $cart_item );
							 print_r (WC()->cart->get_item_data( $cart_item ));
               				// Backorder notification
               				if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) )
               					echo '<p class="backorder_notification">' . __( 'Available on backorder', 'woocommerce' ) . '</p>';
						?>
					</td>
					<td class="product-price">
						<?php
							echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
						?>
					</td>
					<td class="product-quantity">
						<?php
							if ( $_product->is_sold_individually() ) {
								$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
							} else {
								$product_quantity = woocommerce_quantity_input( array(
									'input_name'  => "cart[{$cart_item_key}][qty]",
									'input_value' => $cart_item['quantity'],
									'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
									'min_value'   => '0'
								), $_product, false );
							}
							echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key );
                        echo $cart_item['quantity'];
						?>
					</td>
					<td class="product-subtotal">
						<?php
							echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
						?>
					</td>
                    <td class="product-remove">
                        <?php
                        echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf( '<a href="%s" class="remove" title="%s">&times;</a>', esc_url( WC()->cart->get_remove_url( $cart_item_key ) ), __( 'Remove this item', 'woocommerce' ) ), $cart_item_key );
                        ?>
                    </td>
				</tr>
                <?php
			}
		}
		do_action( 'woocommerce_cart_contents' );
		?>
		<tr class="cart_item add_product_row">
			<td colspan="6" class="actions">
				<!-- FARES: go to product page(post) -->
				<div class="back-to-product"><?php icl_link_to_element(12610,'post',__('Add Product','feyarose')); ?>
					<div class="add_to">

					</div>
				</div>
			</td>
		</tr>
		<tr style="float: right;text-align: right;">
			<td colspan="6" class="actions">
				<div class="cart-collaterals">
                    <?php do_action( 'woocommerce_cart_collaterals' ); ?>

                    <?php //woocommerce_cart_totals(); ?>
                </div>
				<?php if ( WC()->cart->coupons_enabled() ) { ?>
					<div class="coupon">
						<label for="coupon_code"><?php _e( 'Coupon', 'woocommerce' ); ?>:</label> <input type="text"
						                                                                                 name="coupon_code"
						                                                                                 class="input-text"
						                                                                                 id="coupon_code"
						                                                                                 value=""
						                                                                                 placeholder="<?php _e( 'Coupon code', 'woocommerce' ); ?>"/>
						<input type="submit" class="button" name="apply_coupon"
						       value="<?php _e( 'Apply Coupon', 'woocommerce' ); ?> "/>
						<?php do_action( 'woocommerce_cart_coupon' ); ?>
						<div class="add-coupon"></div>
					</div>
				<?php } ?>
				<input type="submit" class="button white-button" name="update_cart"
				       value="<?php _e( 'Update Cart', 'woocommerce' ); ?>"/> <input type="submit"
				                                                                     class="checkout-button button pink-button alt wc-forward"
				                                                                     name="proceed"
				                                                                     value="<?php _e( 'Proceed to Checkout', 'woocommerce' ); ?>"/>

				<?php do_action( 'woocommerce_cart_actions' ); ?>

				<?php wp_nonce_field( 'woocommerce-cart' ); ?>
			</td>
		</tr>
		<?php do_action( 'woocommerce_after_cart_contents' ); ?>
		</tbody>
	</table>
	<?php do_action( 'woocommerce_after_cart_table' ); ?>
</form>
<!--
<div class="cart-collaterals">
	<?php //do_action( 'woocommerce_cart_collaterals' ); ?>

	<?php //woocommerce_cart_totals(); ?>

</div>
-->
</div>
<?php do_action( 'woocommerce_after_cart' ); ?>


