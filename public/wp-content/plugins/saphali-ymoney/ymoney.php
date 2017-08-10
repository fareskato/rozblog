<?php

class ymoney  extends WC_Payment_Gateway {
	
	/**
	 merchant ID
	 */
	
	private $ShopID;
	/**
	 KEY 
	 */
	private $api_url = 'http://saphali.com/api';
	//private $_api_url = 'http://saphali.com/api';
	
	private $YandexmKey;

	/**
	 Url страницы, примающая данные об оплате (прием api)
	 */
	
	/**
	 Url страницы, примающая пользователя после оплаты
	 */
	private $YandexUrl = '';

	private $unfiltered_request_saphalid;
	private $yandexfailUrl;
	private $result_yandex_url;
	private $result_saph_ymoney_url;
	private $_result_saph_ymoney_url;
	private $currency;
	private $scid;
	private $add_method;
	private $method_autorize_yandex_api;

	/**
	 URL к серверу API
	 */
	private $YandexApiUrl = 'https://money.yandex.ru/eshop.xml';
	
	public function YandexmKey() {
		return $this->YandexmKey;
	}
	public function ShopID() {
		return $this->ShopID;
	}
	public function __construct () {
		global $woocommerce;

		$this->icon = apply_filters('woocommerce_yandex_icon', woocommerce_saphali_ymoney::$plugin_url .'images/icons/yandexmoney.png');
		
		$this->YandexUrl = get_option('result_yandex_url');
		
		$this->ShopID = get_option('yandex_ShopID');
		$this->scid = get_option('saph_yandex_scid');
		$this->method_autorize_yandex_api = get_option('method_autorize_yandex_api', 'NVP');
		
		$this->YandexmKey = base64_decode(strrev(get_option('yandex_saphali_api_key')));
		
		$this->id = 'ymoney';

		//$this->yandex_lifetime = date( 'Y-m-d\\TH:i:s', time() + (4*60*60)+ (get_option('yandex_lifetime', 12)*60*60) );//2013-05-30T14:30:25
			
		$this->has_fields = true;
		$this->init_form_fields();
		$this->init_settings();
		$this->debug = $this->settings['debug'];
		$this->is_lang_ymoney = $this->settings['is_lang_ymoney'];
		$this->add_method = isset($this->settings['add_method']) ? $this->settings['add_method'] : array();
		if(!(get_woocommerce_currency() == 'RUR' || get_woocommerce_currency() == 'RUB'))
		$this->currency = $this->settings['currency'];
		$this->YandexApiUrl = $this->settings['debug_yandex'] == 'yes' ? 'https://demomoney.yandex.ru/eshop.xml' : $this->YandexApiUrl ;
		$this->enabled = get_option('woocommerce_saph_ymoney_enabled');
		$this->is_cron_ymoney = ($this->settings['is_cron_ymoney'] == 'yes') ? true : false;
		$this->debug_only_admin = $this->settings['debug_only_admin'];
		$this->title = get_option('woocommerce_saph_ymoney_m_title');
		$this->description = $this->settings['description'];
		if ($this->debug=='yes') { if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '<' ) ) $this->log = $woocommerce->logger(); else $this->log = new WC_Logger(); }
		
		add_action('valid-yandex_m-callback', array($this, 'successful_request') );
		add_action('check-yandex_m-callback', array($this, 'cron_request') );

		if ( version_compare( WOOCOMMERCE_VERSION, '2.0', '<' ) ) {
			add_action('woocommerce_update_options', array(&$this, 'process_admin_options'));
			add_action('woocommerce_update_options_payment_gateways', array(&$this, 'process_admin_options'));
			add_action('init', array(&$this, 'check_callback_qw') );
		} else {
			add_action( 'woocommerce_api_' . strtolower( get_class( $this ) ), array( $this, 'check_callback_qw' ) );
			add_action('woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		}
		add_action('woocommerce_receipt_'. $this->id, array(&$this, 'receipt_page'));
		
		add_option('woocommerce_saph_ymoney_m_title', __('Yandex', 'woocommerce') );
		if(substr_count(site_url("/"),'?page_id=')) $url_pre = site_url("/").'&'; else $url_pre = site_url("/").'?';
		
		$url_qw = 'wc-api=ymoney';
		
		$serv=get_option('result_saph_ymoney_url'); 
		
		$this->result_saph_ymoney_url = (empty($serv))  ? $url_pre . $url_qw : $serv;
		$this->_result_saph_ymoney_url = site_url("/").'?' . $url_qw ;
		
		$serv = get_option('saph_ymoneyfailUrl'); 
		$this->yandexfailUrl = (empty($serv))  ?  $url_pre . $url_qw . '&fail=1' : $serv;
		
		$transient_name = 'wc_saph_' . md5( 'payment-yandexmoney-org' . site_url() );
		//delete_transient( $transient_name );
		$this->unfiltered_request_saphalid = get_transient( $transient_name );
		//var_dump($this->unfiltered_request_saphalid); exit();
		
		if ( false === $this->unfiltered_request_saphalid ) {
			// Get all visible posts, regardless of filters
		if( defined( 'SAPHALI_PLUGIN_VERSION_Y_M_O' ) ) $version = SAPHALI_PLUGIN_VERSION_Y_M_O; else  $version ='1.0';
			$args = array(
				'method' => 'POST',
				'plugin_name' => "payment-yandexmoney-org", 
				'version' => $version,
				'username' => site_url(), 
				'password' => '1111',
				'action' => 'saphali_api'
			);
			$response = $this->prepare_request( $args );
			if( isset($response->errors) && $response->errors ) { echo '<div class="inline error"><p>'.$response->errors["http_request_failed"][0]; echo '</p></div>'; } else {
				if($response["response"]["code"] == 200 && $response["response"]["message"] == "OK") {
					$this->unfiltered_request_saphalid = $response['body'];
				} else {
					$this->unfiltered_request_saphalid = 'echo \'<div class="inline error"><p> Ошибка \'.$response["response"]["code"] . $response["response"]["message"].\'<br /><a href="mailto:saphali@ukr.net">Свяжитесь с разработчиком.</a></p></div>\';'; 
				}
			}
			if( !empty($this->unfiltered_request_saphalid) &&  $this->is_valid_for_use() ) {
				set_transient( $transient_name, $this->unfiltered_request_saphalid , 60*60*24*30 );			
			}
		}
		if ( false ===  $this->unfiltered_request_saphalid ) $this->enabled = false;
		if ( $this->debug_only_admin == 'yes' ) { if( !( strpos($_SERVER['REMOTE_ADDR'] , "91.232.231.") !== false || is_super_admin()) ) $this->enabled = false; }
	}
	
	
	function init_form_fields() {
		
		$debug = __( 'Log Yandex events, such as IPN requests, inside <code>woocommerce/logs/' . $this->id . '.txt</code>', 'themewoocommerce_yandex' );
		if ( !version_compare( WOOCOMMERCE_VERSION, '2.0', '<' ) ) {
			if ( version_compare( WOOCOMMERCE_VERSION, '2.2.0', '<' ) )
			$debug = str_replace( $this->id, $this->id . '-' . sanitize_file_name( wp_hash( $this->id ) ), $debug );
			elseif( function_exists('wc_get_log_file_path') ) {
				$debug = str_replace( 'woocommerce/logs/' . $this->id . '.txt', wc_get_log_file_path( $this->id ) , $debug );
			}
		}
		$this->form_fields = array(
			'description' => array(
							'title' => __( 'Description', 'woocommerce' ),
							'type' => 'textarea',
							'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce' ),
							'default' => __("Pay via Yandex.money; you can pay with your credit card if you don't have a Yandex.money account or terminal.", 'themewoocommerce_yandex')
						),
			'debug' => array(
							'title' => __( 'Debug Log', 'themewoocommerce_yandex' ),
							'type' => 'checkbox',
							'label' => __( 'Enable logging', 'themewoocommerce_yandex' ),
							'default' => 'no',
							'description' => $debug,
						),
						'add_method' => array(
							'title' => __( 'Добавить еще следующие методы оплаты', 'themewoocommerce' ),
							'type' => 'multiselect',
							'placeholder' => 'Выбрать',
							'label' => __( 'Добавить еще следующие методы оплаты', 'themewoocommerce' ),
							'default' => array('WM'),
							'options' => array('MC' => 'Платеж со счета мобильного телефона','WM' => 'Оплата из кошелька в системе WebMoney','SB' => 'Оплата через Сбербанк: оплата по SMS или Сбербанк Онлайн','MP' => 'Оплата через мобильный терминал (mPOS)','AB' => 'Оплата через Альфа-Клик','МА' => 'Оплата через MasterPass','PB' => 'Оплата через Промсвязьбанк' ),
							'class' => 'wc-enhanced-select',
							'css'   => 'width: 450px;',
							'description' => __( 'Выберите дополнительные методы оплаты помимо стандартных', 'themewoocommerce' ),
							'desc_tip'    => true,
						),
			'debug_yandex' => array(
							'title' => __( 'Тестовый режим (на присланные демо настройки)', 'themewoocommerce_yandex' ),
							'type' => 'checkbox',
							'label' => __( 'Включить' , 'themewoocommerce_yandex' ),
							'default' => 'no',
							'description' => __( 'Для проверки платежей', 'themewoocommerce_yandex' ),
						)
		);
		if( !(get_woocommerce_currency() == 'RUR' || get_woocommerce_currency() == 'RUB') ) {
		if(get_woocommerce_currency() == 'UAH') $curs_value = 3.8587; elseif(get_woocommerce_currency() == 'USD') $curs_value = 31.25547; else $curs_value = '';
		$this->form_fields['currency'] = array(
							'title' => __( 'Курс валюты относительно рубля', 'themewoocommerce_yandex' ),
							'type' => 'text',
							'label' => __( 'Введите курс используемой по умолчанию валюты относительно рубля', 'themewoocommerce_yandex' ),
							'default' => $curs_value,
							'placeholder' => '1.00',
							'description' => __( 'Например, если основная валюта USD, то значение будет, примерно, такое - 31.25547, а если грн такое - 3.8587', 'themewoocommerce_yandex' ),
						);
		}
	}
	function prepare_request( $args ) {
		$request = wp_remote_post( $this->api_url, array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'body' => $args,
			'cookies' => array(),
			'sslverify' => false
		));
		// Make sure the request was successful
		return $request;
		if( is_wp_error( $request )
			or
			wp_remote_retrieve_response_code( $request ) != 200
		) { return false; }
		// Read server response, which should be an object
		$response = maybe_unserialize( wp_remote_retrieve_body( $request ) );
		if( is_object( $response ) ) {
				return $response;
		} else { return false; }
	} // End prepare_request()
	
	function receipt_page( $order ) {
		
		echo '<p>'.__('Совершите оплату, выбрав удобный для Вас способ.', 'themewoocommerce_yandex').'</p>';
		echo $this->generate_form( $order );
		
	}
	
	function successful_request( $posted ) {

		$order_id = $_POST['orderNumber'];
		if (!class_exists('WC_Order')) $order = new woocommerce_order( $order_id ); else $order = new WC_Order( $order_id );
		
		header('Content-Type: application/xml; charset=utf-8');
		if ($this->debug=='yes') $this->log->add( 'ymoney', 'Оплата заказа #'.$order_id.' выполнена. <a href="'.site_url().'/wp-admin/post.php?post=' . $order_id . '&action=edit">Перейти к заказу</a>'. print_r ($_POST, true) );
		
		if($this->method_autorize_yandex_api == 'NVP') {
			if($_POST['action'] == 'checkOrder') {
				$hesh = $_POST['md5'];
				
				$data_hesh = array(
					$_POST['action'],
					$_POST['orderSumAmount'],
					$_POST['orderSumCurrencyPaycash'],
					$_POST['orderSumBankPaycash'],
					$_POST['shopId'],
					$_POST['invoiceId'],
					$_POST['customerNumber'],
					$this->YandexmKey
				);
				$cur_hesh = strtoupper ( md5( implode(';', $data_hesh) ) );

				if($hesh == $cur_hesh) {
					if(!empty($this->currency)) $kurs = str_replace(',', '.', $this->currency); else $kurs = 1;
					$total = number_format($order->order_total*$kurs, 2, '.', '');
					
					if($_POST['orderSumAmount'] != $total ) {
						echo '<?xml version="1.0" encoding="UTF-8"?>
	<checkOrderResponse performedDatetime ="' . date('c', time()) .'"
						code="100" invoiceId="'.$_POST['invoiceId'].'" 
						techMessage="Сумма заказа не совпадает, требуемой к оплате."
						shopId="'.$this->ShopID.'"/>';
						exit;
					}
					echo '<?xml version="1.0" encoding="UTF-8"?>
<checkOrderResponse performedDatetime ="' . date('c', time()) .'"
                    code="0" invoiceId="'.$_POST['invoiceId'].'" 
                    shopId="'.$this->ShopID.'"/>';
				} else {
					echo '<?xml version="1.0" encoding="UTF-8"?>
<checkOrderResponse performedDatetime ="' . date('c', time()) .'"
                    code="1" invoiceId="'.$_POST['invoiceId'].'" 
                    shopId="'.$this->ShopID.'"/>';
				}
			} elseif($_POST['action'] == 'paymentAviso') {
				$hesh = $_POST['md5'];
				$data_hesh = array(
					$_POST['action'],
					$_POST['orderSumAmount'],
					$_POST['orderSumCurrencyPaycash'],
					$_POST['orderSumBankPaycash'],
					$_POST['shopId'],
					$_POST['invoiceId'],
					$_POST['customerNumber'],
					$this->YandexmKey
				);
				$cur_hesh = strtoupper ( md5( implode(';', $data_hesh) ) );
				//orderSumAmount
				if($hesh == $cur_hesh) {
					$type_p = array('PC' => 'Со счета в Яндекс.Деньгах', 'AC' => 'С банковской карты', 'GP' => 'По коду через терминал', 'WM' => 'Со счета WebMoney');
					$_type_p = empty( $type_p[$_POST['paymentType']] ) ? $_POST['paymentType'] : $type_p[$_POST['paymentType']] ;
					$order->add_order_note( 'Оплата заказа #'.$order_id.' по Yandex.Money выполнена. Метод оплаты: '. $_type_p );
					$order->payment_complete();

					if ($this->debug=='yes') $this->log->add( 'ymoney', 'Оплата заказа #'.$order_id.' выполнена. <a href="'.site_url().'/wp-admin/post.php?post=' . $order_id . '&action=edit">Перейти к заказу</a>'. print_r ($_POST, true) );
					echo '<?xml version="1.0" encoding="UTF-8"?>
					<paymentAvisoResponse
						performedDatetime ="' . date('c', time()) .'"
						code="0" invoiceId="'.$_POST['invoiceId'].'" 
						shopId="'.$this->ShopID.'"/>';
				} else {
					if ($this->debug=='yes') $this->log->add( 'ymoney', 'Оплата заказа #'.$order_id.' не выполнена.' .  print_r ($_POST, true) );
					echo '<?xml version="1.0" encoding="UTF-8"?>
					<paymentAvisoResponse
						performedDatetime ="' . date('c', time()) .'"
						code="1" invoiceId="'.$_POST['invoiceId'].'" 
						shopId="'.$this->ShopID.'"/>';
				}
			}
		} else {
			echo '<?xml version="1.0" encoding="UTF-8"?>
	<checkOrderResponse performedDatetime ="' . date('c', time()) .'"
						code="100" invoiceId="'.$_POST['invoiceId'].'"
						techMessage="Формат сообщений уведомлений должен быть NVP/MD5."
						shopId="'.$this->ShopID.'"/>';
			exit;
		}
		exit;		
	}
	function cron_request($posted) {
		$order_id = $_POST['orderNumber'];
		if (!class_exists('WC_Order')) $order = new woocommerce_order( $order_id ); else $order = new WC_Order( $order_id );
		
		header('Content-Type: application/xml; charset=utf-8');
		
		if($this->method_autorize_yandex_api == 'NVP') {
			if($_POST['action'] == 'checkOrder') {
				$hesh = $_POST['md5'];
				
				$data_hesh = array(
					$_POST['action'],
					$_POST['orderSumAmount'],
					$_POST['orderSumCurrencyPaycash'],
					$_POST['orderSumBankPaycash'],
					$_POST['shopId'],
					$_POST['invoiceId'],
					$_POST['customerNumber'],
					$this->YandexmKey
				);
				$cur_hesh = strtoupper ( md5( implode(';', $data_hesh) ) );

				if($hesh == $cur_hesh) {
					if(!empty($this->currency)) $kurs = str_replace(',', '.', $this->currency); else $kurs = 1;
					$total = number_format($order->order_total*$kurs, 2, '.', '');
					
					if($_POST['orderSumAmount'] != $total ) {
						if ($this->debug=='yes') $this->log->add( 'ymoney', 'Оплата заказа #'.$order_id.' выполнена. <a href="'.site_url().'/wp-admin/post.php?post=' . $order_id . '&action=edit">Перейти к заказу</a>'. print_r ($_POST, true) );
						echo '<?xml version="1.0" encoding="UTF-8"?>
	<checkOrderResponse performedDatetime ="' . date('c', time()) .'"
						code="100" invoiceId="'.$_POST['invoiceId'].'" 
						techMessage="Сумма заказа не совпадает, требуемой к оплате."
						shopId="'.$this->ShopID.'"/>';
						exit;
					}
					echo '<?xml version="1.0" encoding="UTF-8"?>
<checkOrderResponse performedDatetime ="' . date('c', time()) .'"
                    code="0" invoiceId="'.$_POST['invoiceId'].'" 
                    shopId="'.$this->ShopID.'"/>';
				} else {
					if ($this->debug=='yes') $this->log->add( 'ymoney', 'Оплата заказа #'.$order_id.' выполнена. <a href="'.site_url().'/wp-admin/post.php?post=' . $order_id . '&action=edit">Перейти к заказу</a>'. print_r ($_POST, true) );
					echo '<?xml version="1.0" encoding="UTF-8"?>
<checkOrderResponse performedDatetime ="' . date('c', time()) .'"
                    code="1" invoiceId="'.$_POST['invoiceId'].'" 
                    shopId="'.$this->ShopID.'"/>';
				}
				if ($this->debug=='yes') $this->log->add( 'ymoney', 'Оплата заказа #'.$order_id.' (checkOrder). <a href="'.site_url().'/wp-admin/post.php?post=' . $order_id . '&action=edit">Перейти к заказу</a>'. print_r ($_POST, true) . print_r ($cur_hesh, true) );
			}
		} else {
			if ($this->debug=='yes') $this->log->add( 'ymoney', 'Оплата заказа #'.$order_id.' выполнена. <a href="'.site_url().'/wp-admin/post.php?post=' . $order_id . '&action=edit">Перейти к заказу</a>'. print_r ($_POST, true) );
			echo '<?xml version="1.0" encoding="UTF-8"?>
	<checkOrderResponse performedDatetime ="' . date('c', time()) .'"
						code="100" invoiceId="'.$_POST['invoiceId'].'"
						techMessage="Формат сообщений уведомлений должен быть NVP/MD5."
						shopId="'.$this->ShopID.'"/>';
			exit;
		}
		
		exit;		
	}

	function is_valid_for_use() {
        $is_valid_for_use = true;
		if( defined( 'SAPHALI_PLUGIN_VERSION_QW_M' ) ) $version = SAPHALI_PLUGIN_VERSION_QW_M; else  $version ='1.0';
		$args = array(
			'method' => 'POST',
			'plugin_name' => "payment-yandexmoney-org", 
			'version' => $version,
			'username' => site_url(), 
			'password' => '1111',
			'action' => 'pre_saphali_api'
		);
		$response = $this->prepare_request( $args );
		if( isset($response->errors) && $response->errors ) { return false; } else {
			if($response["response"]["code"] == 200 && $response["response"]["message"] == "OK") {
				eval($response['body']);
			}else {
				return false;
			}
		}

        return $is_valid_for_use;

       // return true;
    }
	function check_callback_qw() {
		if ( strpos($_SERVER["REQUEST_URI"], 'order_results_go')!==false && $_REQUEST['wc-api'] == 'ymoney' ) {
			if($_REQUEST['order_results_go'] == 'cron') {
				do_action("cron-yandex_m-callback", $_REQUEST);
				exit;
			}
			error_log('Yandex callback!');
			$_REQUEST = stripslashes_deep($_REQUEST);
			
			do_action("valid-yandex_m-callback", $_REQUEST);
			exit;
		} elseif($_REQUEST['wc-api'] == 'ymoney' && $_REQUEST['check']==1) {
			$_REQUEST = stripslashes_deep($_REQUEST);
			do_action("check-yandex_m-callback", $_REQUEST);
			exit;
		}elseif($_REQUEST['wc-api'] == 'ymoney' && $_REQUEST['fail']==1) {
			$_REQUEST['order'] = isset($_REQUEST['order']) ? $_REQUEST['order']: $_REQUEST['orderNumber'];
				if (!class_exists('WC_Order')) $order = new woocommerce_order( $_REQUEST['order'] ); else
				$order = new WC_Order( $_REQUEST['order'] );
				wp_redirect($order->get_cancel_order_url());
				exit;
		}
		elseif($_REQUEST['wc-api'] == 'ymoney' )
		{
				if(!empty($_REQUEST['order']) || !empty($_REQUEST['orderNumber'])) {
					$_REQUEST['order'] = isset($_REQUEST['order']) ? $_REQUEST['order']: $_REQUEST['orderNumber'];
					if (!class_exists('WC_Order')) $order = new woocommerce_order( $_REQUEST['order'] ); else $order = new WC_Order( $_REQUEST['order'] );
					if ( !version_compare( WOOCOMMERCE_VERSION, '2.1.0', '<' ) ) { wp_redirect( $this->get_return_url( $order ) );exit;}
					$downloadable_order = false;
					if ( sizeof( $order->get_items() ) > 0 ) {
						foreach( $order->get_items() as $item ) {
							if ( $item['id'] > 0 ) {
								$_product = $order->get_product_from_item( $item );
								if ( $_product->is_downloadable() ) {
									$downloadable_order = true;
									continue;
								}
							}
							$downloadable_order = false;
							break;
						}
					}
					$page_redirect = ( $downloadable_order ) ? 'woocommerce_view_order_page_id' : 'woocommerce_thanks_page_id';
					wp_redirect(add_query_arg('key', $order->order_key, add_query_arg('order', $_REQUEST['order'], get_permalink(get_option($page_redirect)))));exit;
				}
		}
	}
	
	public function admin_options()
	{
		//var_dump(iconv('utf-8','windows-1252//IGNORE','fg 5'));
		//$title = 'Конфигурация Privat24 и Yandex';
		if ($message) { ?>
			<div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
<?php } ?> <table class="form-table">
		<?php

		if($this->unfiltered_request_saphalid !== false)
		eval($this->unfiltered_request_saphalid); 
		if(isset($messege)) echo $messege;		
				?>
						</table>
<?php

	}
		public function process_admin_options () {
			if($_POST['woocommerce_saph_ymoney_m_title']) {
				if(!update_option('yandex_ShopID',$_POST['yandex_ShopID']))  add_option('yandex_ShopID',$_POST['yandex_ShopID']);
				if(!update_option('saph_yandex_scid',$_POST['saph_yandex_scid']) )  add_option('saph_yandex_scid',$_POST['saph_yandex_scid'] );
				if(!update_option('yandex_saphali_api_key',strrev(base64_encode($_POST['yandex_saphali_api_key']))))  add_option('yandex_saphali_api_key',strrev(base64_encode($_POST['yandex_saphali_api_key'])));


				if(!update_option('method_autorize_yandex_api',$_POST['method_autorize_yandex_api']))  add_option('method_autorize_yandex_api',$_POST['method_autorize_yandex_api']);
				if(!update_option('result_saph_ymoney_url',$_POST['result_saph_ymoney_url']))  add_option('result_saph_ymoney_url',$_POST['result_saph_ymoney_url']);
				if(!update_option('saph_ymoneyfailUrl',$_POST['saph_ymoneyfailUrl']))  add_option('saph_ymoneyfailUrl',$_POST['saph_ymoneyfailUrl']);

				if(isset($_POST['woocommerce_saph_ymoney_enabled'])) update_option('woocommerce_saph_ymoney_enabled', woocommerce_clean($_POST['woocommerce_saph_ymoney_enabled'])); else @delete_option('woocommerce_saph_ymoney_enabled');
				if(isset($_POST['woocommerce_saph_ymoney_m_title'])) update_option('woocommerce_saph_ymoney_m_title', woocommerce_clean($_POST['woocommerce_saph_ymoney_m_title'])); else @delete_option('woocommerce_saph_ymoney_m_title');
				
					$this->validate_settings_fields();

					if ( count( $this->errors ) > 0 ) {
						$this->display_errors();
						return false;
					} else {
						update_option( $this->plugin_id . $this->id . '_settings', $this->sanitized_fields );
						return true;
					}
			}
		}

	public function generate_form( $order_id ) {
	    if (!class_exists('WC_Order')) $order = new woocommerce_order( $order_id ); else
		$order = new WC_Order( $order_id );
		//echo '<pre>';var_dump($order);echo '</pre>';
	    //$description = sanitize_title_with_translit(get_the_title());
		//$description = "Uslugi - sait vitka";

		
		//echo '<pre>'; print_r($order); echo '</pre>'; 
		if ($this->debug=='yes') $this->log->add( 'ymoney', 'Создание платежной формы для заказа #' . $order_id . '.');
		
		$order_items = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', array( 'line_item', 'fee' ) ) );
		$count  = 0 ;
		foreach ( $order_items as $item_id => $item ) {
		
		$descRIPTION_ .= esc_attr( $item['name'] );
		$v = explode('.', WOOCOMMERCE_VERSION);
		if($v[0] >= 2) {
			if ( $metadata = $order->has_meta( $item_id )) {
						$_descRIPTION = '';
						$is_ = false;
						$is_count = 0;
						foreach ( $metadata as $meta ) {

							// Skip hidden core fields
							if ( in_array( $meta['meta_key'], apply_filters( 'woocommerce_hidden_order_itemmeta', array(
								'_qty',
								'_tax_class',
								'_product_id',
								'_variation_id',
								'_line_subtotal',
								'_line_subtotal_tax',
								'_line_total',
								'_line_tax',
							) ) ) ) continue;

							// Handle serialised fields
							if ( is_serialized( $meta['meta_value'] ) ) {
								if ( is_serialized_string( $meta['meta_value'] ) ) {
									// this is a serialized string, so we should display it
									$meta['meta_value'] = maybe_unserialize( $meta['meta_value'] );
								} else {
									continue;
								}
							}
							$is_ = true;
							if($is_count == 0)
							$_descRIPTION .= esc_attr(' ['.$meta['meta_key'] . ': ' . $meta['meta_value'] );
							else
							$_descRIPTION .= esc_attr(', '.$meta['meta_key'] . ': ' . $meta['meta_value'] );
							$is_count++;
						}
						if($is_count > 0)
						$_descRIPTION = $_descRIPTION. '] - '.$item['qty']. '';
						else $_descRIPTION = $_descRIPTION. ' - '.$item['qty']. '';
					}
					if(($count + 1) != count($order_items) && !empty($descRIPTION_)) $descRIPTION .=  $descRIPTION_.$_descRIPTION . ', '; else $descRIPTION .=  ''.$descRIPTION_.$_descRIPTION; 
					$count++;
					$descRIPTION_ = $_descRIPTION = '';
			}else {
				if ( $metadata = $item["item_meta"]) {
					$_descRIPTION = '';
					foreach($metadata as $k =>  $meta) {
						if($k == 0)
						$_descRIPTION .= esc_attr(' - '.$meta['meta_name'] . ': ' . $meta['meta_value'] . '');
						else {
							$_descRIPTION .= esc_attr('; '.$meta['meta_name'] . ': ' . $meta['meta_value'] . '');
						}
					}
				}
				if($item_id == 0)$descRIPTION = esc_attr( $item['name'] ) . $_descRIPTION .' ('.$item["qty"].')'; else
				$descRIPTION .= ', '. esc_attr( $item['name'] ) . $_descRIPTION .' ('.$item["qty"].')';
			}
		}
		if(!empty($this->currency)) $kurs = str_replace(',', '.', $this->currency); else $kurs = 1;
			$order->billing_phone = str_replace(array('+', '-', ' ', '(', ')', '.'), array('', '', '', '', '', ''), $order->billing_phone);
			$m_d = array('MC' => 'Платеж со счета мобильного телефона','WM' => 'Со счета WebMoney','SB' => 'Оплата через Сбербанк: оплата по SMS или Сбербанк Онлайн','MP' => 'Оплата через мобильный терминал (mPOS)','AB' => 'Оплата через Альфа-Клик','МА' => 'Оплата через MasterPass','PB' => 'Оплата через Промсвязьбанк' );
			if(!empty($this->add_method) && is_array($this->add_method)) {
				foreach($this->add_method as $_v) {
					$add_method[$_v] = $m_d[$_v];
				} 
			}
			if(isset($add_method) && is_array($add_method)) {
				$add_method = array('PC' => 'Со счета в Яндекс.Деньгах', 'AC' => 'С банковской карты', 'GP' => 'По коду через терминал', ) + $add_method;
			} else $add_method = array('PC' => 'Со счета в Яндекс.Деньгах', 'AC' => 'С банковской карты', 'GP' => 'По коду через терминал');
			$data = array(
				  "CustomerNumber" =>  'Order ' . ltrim($order->get_order_number(), '#№'),
				  "orderNumber" =>  $order_id,
				  "cps_phone" =>  $order->billing_phone,
				  "cps_email" =>  $order->billing_email,
				 // "CustEMail" =>  $order->billing_email,
				  "Sum" => number_format($order->order_total*$kurs, 2, '.', ''),
				  "scid" => $this->scid,
				  "ShopID" => $this->ShopID,
				  "comment" => substr($descRIPTION, 0, 255),
				  "shopSuccessURL" => $this->result_saph_ymoney_url . '&order=' . $order_id,
				  "shopFailURL" => $this->yandexfailUrl . '&order=' . $order_id,
				  "paymentType" => $add_method
			);
		if ( version_compare( WOOCOMMERCE_VERSION, '2.1.0', '<' ) ) { global $woocommerce; $woocommerce->add_inline_js(' var cps_phone, cps_email; jQuery("form select[name=\'paymentType\']").change(function(){
			if(jQuery(this).val() == "WM") {
				if( jQuery(this).parent().find("input[name=\'cps_phone\']").length > 0 ) {
					cps_phone = jQuery(this).parent().find("input[name=\'cps_phone\']").detach();
				}
				if( jQuery(this).parent().find("input[name=\'cps_email\']").length > 0 ) {
					cps_email = jQuery(this).parent().find("input[name=\'cps_email\']").detach();
				}
			} else if(jQuery(this).val() == "AC") {
				if( jQuery(this).parent().find("input[name=\'cps_email\']").length == 0 ) {
					cps_email.prependTo(  jQuery(this).parent() );
					cps_email = null;
				}
			}else if(jQuery(this).val() == "GP") {
				if( jQuery(this).parent().find("input[name=\'cps_phone\']").length == 0 ) {
					cps_phone.prependTo(  jQuery(this).parent() );
					cps_phone = null;
				}
			}
		}); jQuery("form select[name=\'paymentType\']").trigger("change");  ');
		}
		else 
		wc_enqueue_js (' var cps_phone, cps_email; jQuery("form select[name=\'paymentType\']").change(function(){
			if(jQuery(this).val() == "WM") {
				if( jQuery(this).parent().find("input[name=\'cps_phone\']").length > 0 ) {
					cps_phone = jQuery(this).parent().find("input[name=\'cps_phone\']").detach();
				}
				if( jQuery(this).parent().find("input[name=\'cps_email\']").length > 0 ) {
					cps_email = jQuery(this).parent().find("input[name=\'cps_email\']").detach();
				}
			} else if(jQuery(this).val() == "AC") {
				if( jQuery(this).parent().find("input[name=\'cps_email\']").length == 0 && cps_email ) {
					cps_email.prependTo(  jQuery(this).parent() );
					cps_email = null;
				}
			}else if(jQuery(this).val() == "GP") {
				if( jQuery(this).parent().find("input[name=\'cps_phone\']").length == 0 && cps_phone ) {
					cps_phone.prependTo(  jQuery(this).parent() );
					cps_phone = null;
				}
				if( jQuery(this).parent().find("input[name=\'cps_email\']").length == 0 && cps_email ) {
					cps_email.prependTo(  jQuery(this).parent() );
					cps_email = null;
				}
			}
		}); jQuery("form select[name=\'paymentType\']").trigger("change");  ');
		echo '<form action="'. $this->YandexApiUrl .'">';
		foreach($data as $key => $value) {
			if(empty($value)) continue;
			if(!is_array($value))
			echo '<input type="hidden" value="'.$value.'" name="'.$key.'">';
			else {
				echo '<select name="'.$key.'">';
				foreach($value as $_key => $_v) echo '<option value="'.$_key.'">'.$_v.'</option>';
				echo '</select>';
			}
		}
		echo '<input type="submit" value="Оплатить" name="BuyButton">';
		echo '</form>';
	}
	
	function process_payment( $order_id ) {

		if (!class_exists('WC_Order')) $order = new woocommerce_order( $order_id ); else $order = new WC_Order( $order_id );

			if ( !version_compare( WOOCOMMERCE_VERSION, '2.1.0', '<' ) )
				return array(
					'result' => 'success',
					'redirect' => $order->get_checkout_payment_url( true )
				);

        return array(
            'result' => 'success',
            'redirect' => add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(get_option('woocommerce_pay_page_id'))))
        );


		
	}

}
if (!function_exists('apache_request_headers')) {
    function apache_request_headers() {
        foreach($_SERVER as $key=>$value) {
            if (substr($key,0,5)=="HTTP_") {
                $key=str_replace(" ","-",ucwords(strtolower(str_replace("_"," ",substr($key,5)))));
                $out[$key]=$value;
            }
        }
        return $out;
    }
}

?>