<?php
/**
 * Composited Product Quantity.
 *
 * @version  3.0.5
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $quantity_min == $quantity_max ) {

	?><div class="quantity quantity_hidden" style="display:none;"><input class="qty" type="hidden" name="wccp_component_quantity[<?php echo $component_id; ?>]" value="<?php echo $quantity_min; ?>" /></div><?php

} else {

 	woocommerce_quantity_input( array(
 		'input_name'  => 'wccp_component_quantity[' . $component_id . ']',
 		'min_value'   => $quantity_min,
		'max_value'   => $quantity_max,
 		'input_value' => isset( $_POST[ 'wccp_component_quantity' ][ $component_id ] ) ? $_POST[ 'wccp_component_quantity' ][ $component_id ] : apply_filters( 'woocommerce_composited_product_quantity', max( $quantity_min, 1 ), $quantity_min, $quantity_max, $product, $component_id, $composite_product )
 	), $product );

}
