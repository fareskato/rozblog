<?php foreach ( $addon['options'] as $key => $option ) :
	$addon_key     = 'addon-' . sanitize_title( $addon['field-name'] );
	$option_key    = empty( $option['label'] ) ? $key : sanitize_title( $option['label'] );
	$current_value = isset( $_POST[ $addon_key ] ) && isset( $_POST[ $addon_key ][ $option_key ] ) ? $_POST[ $addon_key ][ $option_key ] : '';
	$price         = $option['price'] > 0 ? '(' . woocommerce_price( get_product_addon_price_for_display( $option['price'] ) ) . ')' : '';
	?>

	<p style="display: inline-block" class="form-row form-row-wide addon-wrap-<?php echo sanitize_title( $addon['field-name'] ); ?> <?php echo feyarose_getCleanNameOfProductAddon($option['label']); ?> ">
		<?php if ( ! empty( $option['label'] ) ) : ?>
			<!-- <label for="<?php echo feyarose_getCleanNameOfProductAddon($option['label']); ?>"><?php echo wptexturize( $option['label'] ) . ' ' . $price; ?></label> -->
		<?php endif; ?>
        <label for="<?php echo feyarose_getCleanNameOfProductAddon($option['label']); ?>"><?php echo feyarose_getCleanNameOfProductAddon($option['label'], true); ?> : </label>
		<input id="<?php echo feyarose_getCleanNameOfProductAddon($option['label']); ?>" placeholder="<?php //echo feyarose_getCleanNameOfProductAddon($option['label'], true); ?>" type="text" class="input-text addon addon-custom" data-price="<?php echo get_product_addon_price_for_display( $option['price'] ); ?>" name="<?php echo $addon_key ?>[<?php echo $option_key; ?>]" value="<?php echo esc_attr( $current_value ); ?>" <?php if ( ! empty( $option['max'] ) ) echo 'maxlength="' . $option['max'] .'"'; ?> />
	</p>

<?php endforeach; ?>