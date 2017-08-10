<?php foreach ( $addon['options'] as $key => $option ) :
	$addon_key     = 'addon-' . sanitize_title( $addon['field-name'] );
	$option_key    = empty( $option['label'] ) ? $key : sanitize_title( $option['label'] );
	$current_value = isset( $_POST[ $addon_key ] ) && isset( $_POST[ $addon_key ][ $option_key ] ) ? $_POST[ $addon_key ][ $option_key ] : '';
	$price         = $option['price'] > 0 ? '(' . woocommerce_price( get_product_addon_price_for_display( $option['price'] ) ) . ')' : '';
	?>
    <?php
    $id = feyarose_getCleanNameOfProductAddon($option['label']);
    ?>
	<p class="form-row form-row-wide addon-wrap-<?php echo sanitize_title( $addon['field-name'] ); ?> <?php echo $id; ?>">
		<?php if ( ! empty( $option['label'] ) ) : ?>
			<label for="<?php echo $id; ?>"><?php echo wptexturize( $option['label'] ); ?></label>
		<?php endif; ?>
        <p class="hidden-sm hidden-md hidden-lg bouquet-price-mobile col-xs-7"> <?php echo __('Price of your bouquet:','feyarose') ?></p>
        <div class="clear"></div>
        <p class="single-rose-price-mobile hidden-sm hidden-md hidden-lg"><?php _e('Single rose: 250 ', 'feyarose') ?><i class="fa fa-rub"> </i></p>
        <p class="rose_in_bouquet col-sm-12 col-xs-6" style="padding-left: 0"><span><?php echo _e('From 12 to 101. How many roses do you wish?','feyarose') ?></span></p>
        <div class=" feyarose-input-multiplier quantity <?php echo $addon_key; ?> buttons_added bouquet_product_qty col-lg-6 col-sm-10 col-xs-6" id="qty-<?php echo $id; ?>">
            <input id="<?php echo $id; ?>" type="number" step="1" class="input-text addon addon-input_multiplier" data-price="<?php echo get_product_addon_price_for_display( $option['price'] ); ?>" name="<?php echo $addon_key ?>[<?php echo $option_key; ?>]" value="<?php echo ( esc_attr( $current_value ) == '' ? $option['min'] : esc_attr( $current_value ) ); ?>" <?php if ( ! empty( $option['min'] ) || $option['min'] === '0' ) echo 'min="' . $option['min'] .'"'; ?> <?php if ( ! empty( $option['max'] ) ) echo 'max="' . $option['max'] .'"'; ?> />
        </div><br />

        <div class="col-lg-4 bouquet-popup-price" style="clear: both;">
            <p class="operation-price hidden-xs"><?php _e('Price of your creation', 'feyarose') ?></p>
            <p class="single-rose-price hidden-xs"><?php _e('Single rose: 250 ', 'feyarose') ?><i class="fa fa-rub"> </i></p>
        </div>
        <div class="col-xs-12 hidden-sm hidden-md hidden-lg bouquet-mobile-description-container">
            <span class="description-checkbox-color">
<!--                <span for="bouquet-mobile-description">--><?php //echo __('Description','feyarose') ?><!--</span>-->
                <input type="checkbox" id="bouquet-mobile-description" value="description_checkbox" name="bouquet-mobile-description">
                <label for="bouquet-mobile-description"></label>
            </span>
            <b><?php echo __('Description','feyarose') ?></b>
            <div class="bouquet-mobile-description-wrapper"></div>
        </div>
    <script type="text/javascript">

    </script>

		<span class="addon-alert"><?php _e( 'This must be a number!', 'woocommerce-product-addons' ); ?></span>
	</p>

<?php endforeach; ?>
