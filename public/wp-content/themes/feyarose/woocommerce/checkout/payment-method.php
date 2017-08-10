<?php
/**
 * Output a single payment method
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<li class="payment_method_<?php echo $gateway->id; ?>">
	<div class="one-payment-method-wrapper col-xs-12">
		<span class="payment-button hidden-sm hidden-md hidden-lg">
			<span class="glyphicon glyphicon-ok payment-chosen" aria-hidden="true"></span>
			<?//php echo ($gateway->chosen) ? '<span class="glyphicon glyphicon-ok payment-chosen" aria-hidden="true"></span>' : '' ?>
		</span>
		<input id="payment_method_<?php echo $gateway->id; ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" />
		<label for="payment_method_<?php echo $gateway->id; ?>">
			<?php echo $gateway->get_title(); ?> <?php echo $gateway->get_icon(); ?>
		</label>
	</div>
	<?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
		<div class="payment_box payment_method_<?php echo $gateway->id; ?>" <?php if ( ! $gateway->chosen ) : ?>style="display:none;"<?php endif; ?>>
			<?php $gateway->payment_fields(); ?>
		</div>
	<?php endif; ?>
</li>
