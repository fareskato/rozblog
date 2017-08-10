<?php 
/*
Plugin Name: Saphali Woocommerce Yandex.Money (organization)
Plugin URI: http://saphali.com/saphali-woocommerce-plugin-wordpress
Description: Saphali Яндекс.Деньги (организация) - дополнение к Woocommerce, которое подключает систему оплаты Яндекс.Деньги при заключении договора с Яндексом.
Подробнее на сайте <a href="http://saphali.com/saphali-woocommerce-plugin-wordpress">Saphali Woocommerce</a>

Version: 1.1.2
Author: Saphali
Author URI: http://saphali.com/
*/


/*

 Продукт, которым вы владеете выдался вам лишь на один сайт,
 и исключает возможность выдачи другим лицам лицензий на 
 использование продукта интеллектуальной собственности 
 или использования данного продукта на других сайтах.

 */


/* Add a custom payment class to woocommerce
  ------------------------------------------------------------ */
 define('SAPHALI_PLUGIN_VERSION_Y_M_O','1.1.1');
class woocommerce_saphali_ymoney {
	static $version = '1.0';
	static $plugin_url;
	static $plugin_path;
	
	function __construct() {
		woocommerce_saphali_ymoney::$plugin_url = plugin_dir_url(__FILE__);
		woocommerce_saphali_ymoney::$plugin_path = plugin_dir_path(__FILE__);
		
		if ( function_exists( "spl_autoload_register" ) ) {
			spl_autoload_register( array( $this, 'autoload' ) );
    	}
		
		load_plugin_textdomain( 'themewoocommerce_yandex',  false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		
		if (!class_exists('WC_Payment_Gateway') )
				return; // if the woocommerce payment gateway class is not available, do nothing
		//require_once (woocommerce_saphali_ymoney::$plugin_path . "ymoney.php");
		add_filter('woocommerce_payment_gateways', array($this, 'add_gateway') );
	}
	function add_gateway( $methods ) {
		$methods[] = 'ymoney';
		return $methods;
	}
	public function autoload( $class ) {
		if($class == 'ymoney')
		require_once (woocommerce_saphali_ymoney::$plugin_path . "ymoney.php");
	}
	function install( ) {
		$transient_name = 'wc_saph_' . md5( 'payment-yandexmoney-org' . home_url() );
		$pay[$transient_name] = get_transient( $transient_name );
		
		foreach($pay as $key => $tr) {
			if($tr !== false) {
				delete_transient( $key );
			}
		}
	}
}
add_action('plugins_loaded', 'woocommerce_saphali_yandexm', 0);
function woocommerce_saphali_yandexm() {
	new woocommerce_saphali_ymoney();
}
register_activation_hook( __FILE__, array('woocommerce_saphali_ymoney', 'install') );
?>