<?php foreach ( $addon['options'] as $i => $option ) :

	$price = $option['price'] > 0 ? '(' . woocommerce_price( get_product_addon_price_for_display( $option['price'] ) ) . ')' : '';

	$selected = isset( $_POST[ 'addon-' . sanitize_title( $addon['field-name'] ) ] ) ? $_POST[ 'addon-' . sanitize_title( $addon['field-name'] ) ] : array();
	if ( ! is_array( $selected ) ) {
		$selected = array( $selected );
	}

	$current_value = ( in_array( sanitize_title( $option['label'] ), $selected ) ) ? 1 : 0;
	?>

	<p class="form-row form-row-wide addon-wrap-<?php echo sanitize_title( $addon['field-name'] ) . '-' . $i; ?> <?php echo feyarose_getCleanNameOfProductAddon($option['label']); ?>">
        <img class="hidden-xs" style="vertical-align: baseline" src=<?php bloginfo('template_directory'); ?>/images/is_present.png  />&nbsp;
        <label for="<?php echo feyarose_getCleanNameOfProductAddon($option['label']); ?>"><input id="<?php echo feyarose_getCleanNameOfProductAddon($option['label']); ?>" type="checkbox" class="addon addon-checkbox" name="addon-<?php echo feyarose_getCleanNameOfProductAddon( $addon['field-name'],true ); ?>[]" data-price="<?php echo get_product_addon_price_for_display( $option['price'] ); ?>" value="<?php echo feyarose_getCleanNameOfProductAddon($option['label'],true); ?>" <?php checked( $current_value, 1 ); ?> /> <?php echo feyarose_getCleanNameOfProductAddon( $option['label'] . ' ' . $price,true ); ?></label>

	</p>


<?php endforeach; ?>