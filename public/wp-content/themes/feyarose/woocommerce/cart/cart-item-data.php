<?php
/**
 * Cart item data (when outputting non-flat)
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version 	2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<div class="variation">
	<?php
		foreach ( $item_data as $data ) :
            $theLabelKey = (array_key_exists('key', $data)) ? 'key' : 'label';
			$key = sanitize_text_field( $data[$theLabelKey] );

            $keyClass = feyarose_getCleanNameOfProductAddon($data[$theLabelKey]);
            $cleanName = feyarose_getCleanNameOfProductAddon($data[$theLabelKey], true);
            if($keyClass=='deliveryperiod') {

            }
            if(

                 $keyClass == 'deliverybuilding'
                 || $keyClass == 'deliverytown'

            ) {
                $cleanName = '';
            } else {
                $cleanName = $cleanName . ':';
            }
            //print_r($key. ' ' . $cleanName);
	?>
            <div class="container-<?php echo sanitize_html_class($keyClass); ?> container-variation">
                <div class="variation-<?php echo sanitize_html_class( $key ); ?> label-key label-<?php echo sanitize_html_class($keyClass); ?>">
                    <?php echo $cleanName;//wp_kses_post( $cleanName ); ?>
                </div>
                <div class="variation-<?php echo sanitize_html_class( $key ); ?> label-value value-<?php echo sanitize_html_class($keyClass); ?>">
                    <?php echo wp_kses_post($data['value'] ); ?>
                </div>
            </div>

	<?php endforeach; ?>
</div>
