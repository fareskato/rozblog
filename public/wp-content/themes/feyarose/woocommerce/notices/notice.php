<?php
/**
 * Show messages
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! $messages ){
	return;
}

?>

<?php foreach ( $messages as $message ) : ?>
    <div class="col-lg-12">
        <div class="woocommerce-info"><h2><?php echo wp_kses_post( $message ); ?></h2></div>
    </div>

<?php endforeach; ?>
