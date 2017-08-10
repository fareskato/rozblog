<?php

/**
 * Plugin Name: WooCommerce Quick Export Plugin
 * Description: Adds two new tabs in your WooCommerce reports option page to export all your customers, orders and coupon usage in a CSV file ; and to schedule the exports
 * Version: 2.2
 * Author: MB Création
 * Author URI: http://www.mbcreation.net
 * License: http://codecanyon.net/licenses/regular_extended
 *
 */


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


// Deactivation

register_deactivation_hook( __FILE__, array( 'WooCommerce_Quick_Export_Plugin', 'deactivation' ) );

/**
 * WooCommerce_Quick_Export_Plugin
 *
 * @since 2.0
 */



if ( ! class_exists( 'WooCommerce_Quick_Export_Plugin' ) ) {

class WooCommerce_Quick_Export_Plugin
{

	protected $errors;
	protected $exported_data;
	public $export_settings;

	protected $included_order_keys = array();
	protected $included_order_default_product_keys = array();
	protected $included_order_product_keys = array();
	protected $included_order_product_taxonomies = array();

	protected $included_user_identity_keys = array();
	protected $included_billing_information_keys = array();
	protected $included_shipping_information_keys = array();
	protected $status_for_user_activity = array();

	protected $data_to_repeat = array();
	
	protected $woocommerce_version;
	protected $products_in_columns;
	
	private $endpoint = 'http://tools.mbcreation.com/updater/?item=6040506';
	

	function __construct()
	{	
		global $woocommerce;
    	$this->woocommerce_version = $woocommerce->version;
    	
		if( class_exists( 'Woocommerce' ) ){
			$this->hooks();
		}
		else{
			add_action( 'woocommerce_loaded', array( &$this, 'hooks' ) );
		}
		
		$this->products_in_columns = false;
		
		
		$this->endpoint = apply_filters('qwep_updater_endpoint', $this->endpoint);

		if(!class_exists('lc_update_notifier')) {
			include_once(dirname(__FILE__).'/includes/lc-update-notifier/lc_update_notifier.php');
		}
		
		$lcun = new lc_update_notifier(plugin_dir_path(__FILE__).'index.php', $this->endpoint);
	} //__construct
	
	public function hooks()
	{
		$this->locate();
		
		add_filter( 'woocommerce_reports_charts' , array($this, 'tab' ));
		add_action( 'admin_enqueue_scripts', array($this, 'javascript' ) );
		add_action( 'init', array($this, 'init_class_vars') );
		add_action( 'init', array($this, 'export') );
		add_filter( 'plugin_action_links_'.plugin_basename( __FILE__ ), array($this,'action_links'), 10, 2 );
		add_action( 'current_screen', array( &$this, 'add_help' ), 250 );
		
		if ( is_admin() ) {
    		add_action( 'wp_ajax_qwep_keys', array($this, 'ajax_keys' ));
    		add_action( 'wp_ajax_qwep_keys_query', array($this, 'ajax_keys_query' ));
    	}
				
	} //hooks
	
	public function init_class_vars()
	{
		$this->included_order_keys = $this->wqep_included_order_keys();
		$this->included_user_identity_keys = $this->wqep_included_user_identity_keys();
		$this->included_billing_information_keys = $this->wqep_included_billing_information_keys();
		$this->included_shipping_information_keys = $this->wqep_included_shipping_information_keys();
		
		$this->included_order_default_product_keys = $this->wqep_included_order_default_product_keys();
		$this->included_order_product_keys = $this->wqep_included_order_product_keys();
		$this->included_order_product_taxonomies = $this->wqep_included_order_product_taxonomies();
		
		$this->status_for_user_activity = $this->wqep_status_for_user_activity();

		$this->data_to_repeat = $this->qwep_data_to_repeat();
		$this->errors = array();
	}

	public function locate()
	{	
		load_plugin_textdomain('wqep', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
	}
    
    /*
     * Quick link to product settings
     */
	public function action_links( $links, $file )
	{
    	
    	if(substr($this->woocommerce_version, 0, 3) == '2.0')
    	{
    		array_unshift( $links, '<a href="' . admin_url( 'admin.php?page=woocommerce_reports&tab=wqep-export' ) . '">' . __( 'Go to Quick Export', 'wqep' ) . '</a>' );
    	}
    	else
    	{
    		array_unshift( $links, '<a href="' . admin_url( 'admin.php?page=wc-reports&tab=wqep-export' ) . '">' . __( 'Go to Quick Export', 'wqep' ) . '</a>' );
    	}
 		return $links;

	}


	public function javascript($hook)
	{
        if( 'woocommerce_page_woocommerce_reports' != $hook and 'woocommerce_page_wc-reports' != $hook and 'wc-reports' != substr($hook, -10)) return;
            wp_enqueue_script( 'wqep_custom_script', plugin_dir_url( __FILE__ ) . '/js/wqep-export.js' , array('jquery','jquery-ui-datepicker') );
	
	} //locate

	
	public function add_help()
	{
		$screen = get_current_screen();
		
		$hook = $screen->base;
		
        if( 'woocommerce_page_woocommerce_reports' != $hook and 'woocommerce_page_wc-reports' != $hook and 'wc-reports' != substr($hook, -10)) return;
            
        
       
        
        $content = '<p>' . __( 'Here are the <strong>order meta</strong> you can add using a filter', 'wqep' ).' (<a target="_blank" href="http://pastebin.com/Thw5MGWN">'.__('code example here', 'wqep' ).'</a>) :' . '</p>
        <p id="qwep_order_meta"><a href="#"  class="button">'.__( 'Load the keys', 'wqep' ).'</a></p>
        <p>' . __( 'Here are the <strong>product meta</strong> you can add using a filter', 'wqep' ).' (<a target="_blank" href="http://pastebin.com/FGZJxHgA">'.__('code example here', 'wqep' ).'</a>) :' . '</p><p id="qwep_post_meta"><a href="#"  class="button">'.__( 'Load the keys', 'wqep' ).'</a></p>';
		    	
		if(function_exists('wc_get_order_statuses'))    	
		{
			$content .= '<p>' . __( 'Here are the <strong>taxonomies</strong> you can add using a filter', 'wqep' ).' (<a target="_blank" href="http://pastebin.com/YWTuRU60">'.__('code example here', 'wqep' ).'</a>) :' . '</p><p id="qwep_taxo"><a href="#" class="button">'.__( 'Load the keys', 'wqep' ).'</a></p>';
			
		}
		    	
		
        $screen->add_help_tab( array(
		    'id'	=> 'qwep_add_help',
		    'title'	=> __( 'Quick export', 'wqep' ),
		    'content'	=> $content
			
		));
	
	}
	
	
	
	public function ajax_keys()
	{
		$type = $_POST['type'];
		
		if($type == 'qwep_order_meta')
			echo '<code>'.implode('</code> <code>', $this->getDistinctOrderMeta()) .'</code>';
		
		if($type == 'qwep_post_meta')
			echo '<code>'.implode('</code> <code>', $this->getDistinctPostMeta()) .'</code>';
			
		if($type == 'qwep_taxo')
			echo '<code>'.implode('</code> <code>', $this->getDistinctTaxonomies()) .'</code>';
		
		die;
    }
    
    
    public function ajax_keys_query()
	{
		$type = $_POST['type'];
		$query = '';
		global $wpdb;
		
		if($type == 'qwep_order_meta')
		{
			if(!function_exists('wc_get_order_statuses'))
		{
	   		$query = '
			select distinct(meta_key) from '.$wpdb->prefix.'postmeta, '.$wpdb->prefix.'posts where ID = post_id and (post_type = "shop_order") and post_status = "publish"';
			}
			else
			{
				$statuses = wc_get_order_statuses();
				
				foreach($statuses as $i=>$s)
				{
					$statuses[$i] = $i;
				}
			
				$query = '
			select distinct(meta_key) from '.$wpdb->prefix.'postmeta, '.$wpdb->prefix.'posts where ID = post_id and (post_type = "shop_order") and post_status in ("'.implode('","', $statuses).'")';
			}
	   	
		}
		
		if($type == 'qwep_post_meta')
			$query = '
	   	select distinct(meta_key) from '.$wpdb->prefix.'postmeta, '.$wpdb->prefix.'posts where ID = post_id and (post_type = "product" or post_type = "product_variation") and post_status = "publish"
	   	
	   	UNION 
	   	
	   	select distinct(meta_key) from '.$wpdb->prefix.'woocommerce_order_itemmeta
	   	
	   	';
	   	
	   	if($query)
	   	{
			echo __('Unfortunately, your php memory limit isn\'t high enough to retrieve the keys from your database. You either can increase you php memory limit into your wp-config.php file, nor run this query against your database to get the keys : ', 'wqep').'<br /><code>'.$query.'</code>';
			
	   	}
	   	
		
		die;
    }
	
	public function getDistinctTaxonomies()
	{
		$taxos = get_taxonomies(array('object_type' => array('product') ));
		$taxos2 = array();
		foreach($taxos as $taxo)
		{
			if(substr($taxo, 0, 3) != 'pa_')
				$taxos2[] = $taxo;
		}
		return $taxos2;
	}
	
	
	public function getDistinctOrderMeta()
	{
		global $wpdb;
		
		if(!function_exists('wc_get_order_statuses'))
		{
	   		$query = '
	   	select distinct(meta_key) from '.$wpdb->prefix.'postmeta, '.$wpdb->prefix.'posts where ID = post_id and (post_type = "shop_order") and post_status = "publish"';
	   	}
	   	else
	   	{
	   		$statuses = wc_get_order_statuses();
	   		
	   		foreach($statuses as $i=>$s)
	   		{
	   			$statuses[$i] = $i;
	   		}
	   		
	   		$query = '
	   	select meta_key, meta_value from '.$wpdb->prefix.'postmeta, '.$wpdb->prefix.'posts where ID = post_id and (post_type = "shop_order") and post_status in ("'.implode('","', $statuses).'")';
	   	}

		$results = $wpdb->get_results($query);
		$keys = array();
		
		foreach($results as $data)
		{
			$val = maybe_unserialize($data->meta_value);
			
			if(is_array($val))
			{
				foreach($val as $k=>$v)
					$keys[] = $data->meta_key.'|'.$k;
			}
			else
				$keys[] = $data->meta_key;
		}
		
		
		sort($keys);
		$keys = array_unique($keys);
		
		return $keys;
	}
	
	public function getDistinctPostMeta()
	{
		global $wpdb;
	   	$query = '
	   	select meta_key, meta_value from '.$wpdb->prefix.'postmeta, '.$wpdb->prefix.'posts where ID = post_id and (post_type = "product" or post_type = "product_variation") and post_status = "publish"
	   	
	   	UNION 
	   	
	   	select meta_key, meta_value from '.$wpdb->prefix.'woocommerce_order_itemmeta
	   	
	   	';

		$results = $wpdb->get_results($query);
		$keys = array();
		
		foreach($results as $data)
		{
			$val = maybe_unserialize($data->meta_value);
			
			if(is_array($val))
			{
				foreach($val as $k=>$v)
					$keys[] = $data->meta_key.'|'.$k;
			}
			else
				$keys[] = $data->meta_key;
		}
		
		sort($keys);
		$keys = array_unique($keys);
		
		return $keys;
	}

	public function tab( $charts )
	{
	    $charts['wqep-export'] = array(
			'title'  => __( 'Quick Export', 'wqep' ),
			'charts' => array(
				'overview' => array(
					
					'title'       => __('WooCommerce Quick Export Plugin', 'wqep'),
					'description' => __('To quickly export data from your WooCommerce database, just select the data type and click the export button. <br/> For more informations on options, read the html manual included with the plugin. Thanks for using this Plugin.', 'wqep'),
					'hide_title'  => true,
					'function'    => array($this, 'panel')
				),
			)
		);

		return $charts;
	} //tab


	public function panel()
	{

		if( count( $this->errors ) > 0 ) {
			echo '<div class="error"><ul>'; 
			foreach( $this->errors as $error) {
				echo '<li>'.$error->get_error_message().'</li>';
			}
			echo '</ul></div>'; 
		}
		
		?>
		<div id="poststuff">
		<form id="wqep-form" method="post" action="">
			<div class="postbox">
			<h3><span><?php _e('Data Type', 'wqep');?></span></h3>
			<div class="inside">
			<table class="form-table" id="wqep-form-table-entity">
				<tr valign="top">
					<th scope="row">
						<label for="wqep-entity"><?php _e('Would you like to export customers, orders or coupons ?','wqep');?></label>
					</th>

					<td>
						<select name="wqep_type" id="wqep-entity">
							<option value="customers"><?php _e('Customers', 'woocommerce'); ?></option>
							<option value="orders" ><?php _e('Orders', 'woocommerce'); ?></option>
							<option value="coupons" ><?php _e('Coupons', 'woocommerce'); ?></option>
						</select>
					</td>
				</tr>

				<tr valign="top" id="wqep-td-user-infos">

					<th scope="row"><?php _e('User data', 'wqep');?></th>
					
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span><?php _e('User data', 'wqep');?></span></legend>

							<!-- USER IDENTITY -->
							<label for="wqep-userdata-identity">
								<input name="wqep_userdata[]" type="checkbox" id="wqep-userdata-identity" value="identity" checked="checked">
									<?php _e('User identity', 'wqep'); ?>
							</label><br>

							<!-- USER BILLING INFO -->
							<label for="wqep-userdata-billing">
								<input name="wqep_userdata[]" type="checkbox" id="wqep-userdata-billing" value="billing" checked="checked">
									<?php _e('Billing informations', 'wqep'); ?>
							</label><br>

							<!-- USER SHIPPING INFO -->
							<label for="wqep-userdata-shipping">
								<input name="wqep_userdata[]" type="checkbox" id="wqep-userdata-shipping" value="shipping" checked="checked">
									<?php _e('Shipping informations', 'wqep'); ?>
							</label><br>

							<!-- USER SALES INFO -->
							<label for="wqep-userdata-sales">
								<input name="wqep_userdata[]" type="checkbox" id="wqep-userdata-sales" value="sales" >
									<?php _e('Sales statistics', 'wqep'); ?>
							</label><br>

						</fieldset>
					</td>
				</tr>

				<tr valign="top" id="wqep-td-command-status">

					<th scope="row"><?php _e('Order status', 'wqep');?></th>
					
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span><?php _e('Order status', 'wqep');?></span></legend>

							<?php 
							if(function_exists('wc_get_order_statuses'))
							{
								$shop_order_status = wc_get_order_statuses();
								global $wpdb;
								foreach ($shop_order_status as $i=>$status): 
								
									
								$count_query = ' SELECT COUNT(*) as nb from '.$wpdb->prefix.'posts where post_status="'.$i.'" and post_type="shop_order" ';
		
    								$count = $wpdb->get_var($count_query);
								
								if($count>0):
								?>
								<label for="wqep-status-<?php echo $i;?>">
								<input name="wqep_status[]" type="checkbox" id="wqep-status-<?php echo $i;?>" value="<?php echo $i; ?>" checked="checked">
								<?php echo $status; ?> (<?php echo $count; ?>)
								
							</label><br><?php endif; ?>
							<?php endforeach;
							}
							else
							{	
								$shop_order_status = get_terms( 'shop_order_status', 'orderby=id&hide_empty=1' ); ?>
							<?php foreach ($shop_order_status as $status): ?>
								<label for="wqep-status-<?php echo $status->slug;?>">
								<input name="wqep_status[]" type="checkbox" id="wqep-status-<?php echo $status->slug;?>" value="<?php echo $status->term_id; ?>" checked="checked">
								<?php _e($status->name, 'woocommerce'); ?> (<?php echo $status->count;?>)
							</label><br>
							<?php endforeach; 
							
							}
							?>
					</fieldset>
					</td>
				</tr>
				
				<tr valign="top" id="wqep-td-coupon-description">

					<th scope="row"><?php _e('Coupon description', 'wqep');?></th>
					
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span><?php _e('Coupon description', 'wqep');?></span></legend>

							
								<label for="wqep-coupon-description">
								<input name="wqep_coupon_description" type="checkbox" id="wqep-coupon-description" value="coupon_description">
								<?php _e('Include coupon description', 'wqep');?>
							</label><br>
							
					</fieldset>
					</td>
				</tr>
				
				<tr valign="top" id="wqep-td-command-product-display">

					<th scope="row"><?php _e('Product display', 'wqep');?></th>
					
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span><?php _e('Product display', 'wqep');?></span></legend>

							
								<label for="wqep-status-product-display">
								<input name="wqep_product_display" type="checkbox" id="wqep-status-product-display" value="columns">
								<?php _e('Display each product (order item) in its own row.', 'wqep');?>
							</label><br>
							
					</fieldset>
					</td>
				</tr>
				
				
			</table>
			</div>
			</div>
			<div class="postbox">
			<h3><span><?php _e('Optionnals date range', 'wqep');?></span></h3>
			<div class="inside">
			<table class="form-table" id="wqep-form-table-date-range">
				<tr valign="top">
					<th scope="row"><?php _e('Start Date', 'wqep');?></th>
					<td><input readonly="readonly" type="text" class="text custom_date" name="wqep_start_date" value="" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('End Date', 'wqep');?></th>
					<td><input readonly="readonly" type="text" class="text custom_date" name="wqep_end_date" value="" /></td>
				</tr>
			</table>
			</div>
			</div>
			<div class="postbox">
			<h3><span><?php _e('CSV file options', 'wqep');?></span></h3>
			<div class="inside">
			<table class="form-table" id="wqep-form-table-file-options">
				<tr valign="top">
					<th scope="row"><?php _e('Field Separator', 'wqep');?></th>
					<td><input type="text" class="small-text" name="wqep_separator" value="," /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Line Breaks', 'wqep');?></th>
					<td>
						<input type="text" class="small-text" name="wqep_linebreak" value="\r\n" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Export Format', 'wqep');?></th>
					<td>
					<select name="wqep_exportformat" id="wqep_exportformat">
							<option value="utf8" <?php if(get_locale() != 'zh_CN') echo 'selected="selected"'; ?> ><?php _e('Default (utf-8)', 'wqep'); ?></option>
							<option value="utf16" ><?php _e('Better Excel Support (utf-16)', 'wqep'); ?></option>
							<option value="gbk" <?php if(get_locale() == 'zh_CN') echo 'selected="selected"'; ?> ><?php _e('Chinese Excel Support (gbk)', 'wqep'); ?></option>
					</select>
					</td>
				</tr>
			</table>


			<input type="hidden" name="wqep_action" value="tocsv" />
			<input type="hidden" name="wqep_action_type" value="manual" />
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Export') ?>" />
			</p>
			</div>
			</div>
		</form>
		</div><!-- poststuff -->

		<?php 
	} //panel


	public function wqep_status_for_user_activity()
	{
		$keys = array( 'completed' );

		return apply_filters('wqep_status_for_user_activity_filter', $keys);
	}


	public function qwep_data_to_repeat()
	{
		$keys = array();

		return apply_filters('qwep_data_to_repeat', $keys);
	}


	
	
	/*
	 * Define the keys for orders informations to export
	 */
	public function wqep_included_order_keys()
	{
		$keys = array(
		    //general
			'id', 'status', 'order_date',
			
			//billing infos
			'billing_first_name', 'billing_last_name', 
			'billing_company', 'billing_address_1', 'billing_address_2','billing_city',  
			'billing_postcode', 'billing_country', 'billing_state', 'billing_email', 
			'billing_phone',
			
			//shipping infos
			'shipping_first_name', 'shipping_last_name', 
			'shipping_company', 'shipping_address_1', 'shipping_address_2', 
			'shipping_city', 'shipping_postcode', 'shipping_state', 'shipping_country',
			
			//note
			'customer_note', 
			
			//payment, shipping and total
			'shipping_method_title', 'payment_method_title', 'order_discount', 
			'cart_discount', 'order_tax', 'order_shipping', 'order_shipping_tax', 
			'order_total', 'order_tax_detail', 'completed_date',
			
			//others
			'number_of_different_items',
			'total_number_of_items',
			'used_coupons',
			'coupon_name',
			'coupon_discount'
		);
			
		return apply_filters('wqep_included_order_keys_filter', $keys);
	}
	
	/*
	 * Define the keys for general product informations to export
	 */
	public function wqep_included_order_default_product_keys()
	{
		$keys = array('sku', 'name', 'quantity', 'line_price_without_taxes', 'line_price_with_taxes');
		return apply_filters('wqep_included_order_default_product_keys_filter', $keys);
	}

	/*
	 * Define the keys for additionnal product informations to export
	 */
	public function wqep_included_order_product_keys()
	{
		$keys = array();
		return apply_filters('wqep_included_order_product_keys_filter', $keys);
	}

	public function wqep_included_order_product_taxonomies()
	{
		$keys = array();
		return apply_filters('wqep_included_order_product_taxonomies_filter', $keys);
	}


    
	public function wqep_included_user_identity_keys()
	{
		
		$keys = array(
			'user_registered',
			'user_login',
			'user_email'
		);

		return apply_filters('wqep_included_user_identity_keys_filter', $keys);
	
	}

	public function wqep_included_billing_information_keys()
	{
		
		$keys = array(
			'billing_first_name',  'billing_last_name', 'billing_company',
			'billing_address_1', 'billing_address_2', 'billing_city',
			'billing_postcode', 'billing_country', 'billing_state', 
			'billing_email', 'billing_phone'
		);

		return apply_filters('wqep_included_billing_information_keys_filter', $keys);
	
	}

	public function wqep_included_shipping_information_keys()
	{
		
		$keys = array(
			'shipping_first_name', 'shipping_last_name', 'shipping_company', 
			'shipping_address_1', 'shipping_address_2', 'shipping_city', 
			'shipping_postcode', 'shipping_country', 'shipping_state'
		);

		return apply_filters('wqep_included_shipping_information_keys_filter', $keys);
	
	}


	public function get_data()
	{
		$csv = '';
		$export_csv = true;

		$sep = (empty($this->export_settings['wqep_separator'])) ? ',' : $this->export_settings['wqep_separator'];
		$lb = (empty($this->export_settings['wqep_linebreak'])) ? "\r\n" : stripslashes_deep( $this->export_settings['wqep_linebreak'] );
		
		if($lb == 'rn')
			$lb = "\r\n";

		$start = (empty($this->export_settings['wqep_start_date'])) ? '1970-01-01' : $this->export_settings['wqep_start_date'];
		$end = (empty($this->export_settings['wqep_end_date'])) ? '2020-01-01' : $this->export_settings['wqep_end_date'];
		$end = (strlen($end)==10) ? $end.' 23:59:59' : $end;

		$sep = $this->expand_escape($sep);
		$lb = $this->expand_escape($lb);

		switch ($this->export_settings['wqep_type'])
		{
            case 'customers':
                $export_csv = $this->exportCustomers($csv, $export_csv, $sep, $lb, $start, $end);
				break;
			
			case 'orders':
				$export_csv = $this->exportOrders($csv, $export_csv, $sep, $lb, $start, $end);
				break;
            
            case 'coupons':
				$export_csv = $this->exportCoupons($csv, $export_csv, $sep, $lb, $start, $end);
				break;
				
        } //switch ($this->export_settings['wqep_type'])

        return $export_csv;
			
	} // get_data
	
	
	/*
     * Order export
     */
	protected function exportOrders($csv, $export_csv, $sep, $lb, $start, $end)
	{
        if(isset($this->export_settings['wqep_product_display']) and $this->export_settings['wqep_product_display']=='columns')
        {
        	$this->products_in_columns = true;
        }
        
        if(!function_exists('wc_get_order_statuses'))
        {
			$args = array(
					'posts_per_page' => -1,
					'post_type'   => 'shop_order',
					'post_status' => 'publish',
					'orderby' => 'post_date',
					'order' => 'ASC',
					'date_query' => array(
							array(
										'after' => $start,
										'before' =>  $end,
										'inclusive' => true,
									),
							),
					
					'tax_query'=>array(
							array(
		
								 'taxonomy' =>'shop_order_status',
								 'field' => 'id',
								 'terms' => $this->export_settings['wqep_status']
							)
					)
				);
        }
        else
        {
        	$args = array(
					'posts_per_page' => -1,
					'post_type'   => 'shop_order',
					'post_status' => $this->export_settings['wqep_status'],
					'orderby' => 'post_date',
					'order' => 'ASC',
					'date_query' => array(
							array(
										'after' => $start,
										'before' =>  $end,
										'inclusive' => true,
									),
							),
				);
				
        	
        }
        
        $args = apply_filters('qwep_order_query_args', $args);
        
        $customer_orders = new WP_Query( $args );
        
        $customer_orders = $customer_orders->get_posts();
        
        $customer_orders = apply_filters('qwep_customer_orders', $customer_orders);
        
        $total_orders = (int) sizeof($customer_orders);
        
        //get max items from a commande
        $max_items = $this->getMaxItems($customer_orders);
        $max_coupons = $this->getMaxCoupons($customer_orders);
        
        $i=0;
        
        if( count( $customer_orders ) == 0) {
        	$export_csv = false;
        	$this->errors[] = new WP_Error('error', __('No order to export in that period.', 'wqep'));
        }
        else {
        	$different_taxes = $this->getDifferentTaxes($customer_orders);
        	foreach ( $customer_orders as $customer_order ) {
                
                $this->set_time_limit(20);
                
                if(function_exists('wc_get_order_statuses'))
        			$order = new WC_Order($customer_order);
        		else
        		{
        			$order = new WC_Order();
        			$order->populate( $customer_order );
        		}
        		// adding custom fields.
        
        		// WooCommerce is not loading order meta data anymore
        		if(!isset($order->order_custom_fields))
        		$order->order_custom_fields = get_post_meta( $order->id );
        
        		foreach ($order->order_custom_fields as $key => $value) {
        			$order->$key = $value[0];
        		}
        
        		unset( $order->order_custom_fields );
        
        
        		if($i==0) {
        		
        			// column names for row one
        			$nb_cols_before_products = count($this->included_order_keys);
        			if(in_array('used_coupons', $this->included_order_keys))
        				$nb_cols_before_products--;
        			if(in_array('coupon_name', $this->included_order_keys))
        				$nb_cols_before_products--;
        			if(in_array('coupon_discount', $this->included_order_keys))
        				$nb_cols_before_products--;
        
        			foreach ($this->included_order_keys as $key) {
            			if($key != 'number_of_different_items' and 
            			     $key != 'total_number_of_items' and
            			     $key != 'used_coupons' and
            			     $key != 'coupon_name' and
            			     $key != 'coupon_discount'
                        )
            			{
            				if($key == 'order_tax_detail' and count($different_taxes) > 1)
            				{
            					foreach($different_taxes as $taxslug=>$taxname)
            					{
            						$filter = str_replace(' ', '_', 'qwep_column_name_'.$taxname);
            						$csv.='"'.$this->escape(apply_filters($filter, $taxname)).'"'.$sep;
            					}
            					$nb_cols_before_products += count($different_taxes)-1;
            				}
            				elseif($key != 'order_tax_detail')
            				{
            				    if($key == 'billing_complete_name' or $key == 'shipping_complete_name')
            					{
            					   if($key == 'billing_complete_name')
            					       $csv.='"'.$this->escape(apply_filters('qwep_column_name_billing_complete_name', __('Complete name (billing)', 'wqep'))).'"'.$sep;
            					   if($key == 'shipping_complete_name')
            					       $csv.='"'.$this->escape(apply_filters('qwep_column_name_shipping_complete_name', __('Complete name (shipping)', 'wqep'))).'"'.$sep;
            					}
            					elseif($key=='multiple_shipping')
								{
									$max_pack = $this->getMaxPack($customer_orders);
									for($i=0; $i<$max_pack; $i++)
									{
										$csv.='"'.$this->escape(apply_filters('qwep_column_name_shipping_complete_name', 'Items', $i)).'"'.$sep;
										
										$csv.='"'.$this->escape(apply_filters('qwep_column_name_shipping_complete_name', 'Shipping address', $i)).'"'.$sep;
									}
								}
            					elseif(substr($key, 0, 12) == '_custom_key_')
            					{
            						$custom_key = str_replace(array('_custom_key_', '_'), array('', ' '),$key);
            						$csv.='"'.$this->escape( apply_filters('qwep_column_name_'.$custom_key, __($custom_key, 'woocommerce'))).'"'.$sep;
            					}
            					else
            					{
            					    $filter = str_replace(' ', '_', 'qwep_column_name_'.$key);
                					$key = ucwords(str_replace('_', ' ', $key));
                					$key2 = '';
                					if(strpos($key, 'Billing')!== false)
                					{
                						$key = str_replace('Billing', '', $key);
                						$key2 = ' ('.__('Billing', 'wqep').')';
                					}
                					if(strpos($key, 'Shipping')!== false)
                					{
                						$key = str_replace('Shipping', '', $key);
                						$key2 = ' ('.__('Shipping', 'woocommerce').')';
                					}
                					
                					if(trim($key) == 'Order Date')
                						$key = __('Order Date', 'wqep');
                						
                					elseif(trim($key) == 'Payment Method Title')
                						$key = __('Payment Method Title', 'wqep');
                						
                					elseif(trim($key) == 'Order  Tax')
                						$key = __('Order Tax', 'wqep');
                						
                					elseif(trim($key) == 'Completed Date')
                						$key = __('Completed Date', 'wqep');
                					else
                						$key = __(trim($key), 'woocommerce');
                						
                					$csv.='"'.$this->escape(apply_filters($filter, $key.$key2)).'"'.$sep;
            					
            					}
            				}
            				else //$key == 'order_tax_detail' and count($different_taxes) <= 1)
            				{
            					$nb_cols_before_products--;
            				}
                        }
        			}
        			
        			if(in_array('number_of_different_items', $this->included_order_keys))
        			{
        			    $csv.='"'.$this->escape(apply_filters('qwep_column_name_number_of_different_items',__('Number of different items', 'wqep'))).'"'.$sep;
        			}
        			if(in_array('total_number_of_items', $this->included_order_keys))
        			{
                        $csv.='"'.$this->escape(apply_filters('qwep_column_name_total_number_of_items',__('Total number of items', 'wqep'))).'"'.$sep;
        			}
        			
        			if(!$this->products_in_columns) //products in line
        			{
        				for($i=0; $i<$max_items; $i++)
        				{
        				    //header for general product informations
        				    foreach ($this->included_order_default_product_keys as $pdt_key)
        				    {
        				        if($pdt_key == 'sku')
        				            $label = __('SKU', 'woocommerce');
        				        if($pdt_key == 'name')
        				            $label = __('Name', 'woocommerce');
        				        if($pdt_key == 'quantity')
        				            $label = __('Quantity', 'woocommerce');
        				        if($pdt_key == 'line_price_without_taxes')
        				            $label = __('Line price (without taxes)', 'wqep');
        				        if($pdt_key == 'line_price_with_taxes')
        				            $label = __('Line price (including taxes)', 'wqep');
        				        
        				        $csv.='"'.$this->escape(apply_filters('qwep_column_name_product_'.$pdt_key,  $label.' #'.($i+1), $i)).'"'.$sep;
        				    
        				    }
        					
        					//header for custom product informations
        					foreach ($this->included_order_product_keys as $pdt_key)
        					{
        						$csv.='"'.$this->escape(apply_filters('qwep_column_name_product_'.$pdt_key,  $pdt_key.' #'.($i+1), $i)).'"'.$sep;
        					
        					}
        					
        					foreach ($this->included_order_product_taxonomies as $taxo_key)
        					{
        						$taxo = get_taxonomies(array('name'=> $taxo_key), 'objects');
        						$taxo_name = $taxo_key;
        						
        						if(isset($taxo[$taxo_key]) and isset($taxo[$taxo_key]->label) and $taxo[$taxo_key]->label)
        							$taxo_name = $taxo[$taxo_key]->label;
        						$csv.='"'.$this->escape(apply_filters('qwep_column_name_product_'.$taxo_key,  $taxo_name.' #'.($i+1), $i)).'"'.$sep;
        					
        					}
        					
        				}
        			}
        			else //products in column
        			{
        			    //header for general product informations
                        foreach ($this->included_order_default_product_keys as $pdt_key)
        			    {
        			        if($pdt_key == 'sku')
        			            $label = __('SKU', 'woocommerce');
        			        if($pdt_key == 'name')
        			            $label = __('Name', 'woocommerce');
        			        if($pdt_key == 'quantity')
        			            $label = __('Quantity', 'woocommerce');
        			        if($pdt_key == 'line_price_without_taxes')
        			            $label = __('Line price (without taxes)', 'wqep');
        			        if($pdt_key == 'line_price_with_taxes')
        			            $label = __('Line price (including taxes)', 'wqep');
        			        
        			        $csv.='"'.$this->escape(apply_filters('qwep_column_name_product_'.$pdt_key,  $label)).'"'.$sep;
        			    
        			    }
        				
        				//header for custom product informations  
        				foreach ($this->included_order_product_keys as $pdt_key)
        				{
        					$csv.='"'.$this->escape(apply_filters('qwep_column_name_product_'.$pdt_key,  $pdt_key)).'"'.$sep;
        				
        				}
        				
        				foreach ($this->included_order_product_taxonomies as $taxo_key)
						{
							$taxo = get_taxonomies(array('name'=> $taxo_key), 'objects');
							$taxo_name = $taxo_key;
							
							if(isset($taxo[$taxo_key]) and isset($taxo[$taxo_key]->label) and $taxo[$taxo_key]->label)
								$taxo_name = $taxo[$taxo_key]->label;
							$csv.='"'.$this->escape(apply_filters('qwep_column_name_product_'.$taxo_key,  $taxo_name)).'"'.$sep;
						
						}
        					
        			}
                    
                    if(in_array('used_coupons', $this->included_order_keys))
        			{
                        $csv.='"'.$this->escape(apply_filters('qwep_column_name_used_coupons', __('Used coupons', 'wqep'))).'"'.$sep;
                    }
                    
                    if(in_array('coupon_name', $this->included_order_keys) or in_array('coupon_discount', $this->included_order_keys))
                    {
            			for($i=0; $i<$max_coupons; $i++)
            			{
                            if(in_array('coupon_name', $this->included_order_keys))
            				    $csv.='"'.$this->escape(apply_filters('qwep_column_name_coupon_name', __('Coupon', 'woocommerce').' #'.($i+1), $i)).'"'.$sep;
            				if(in_array('coupon_discount', $this->included_order_keys))
            				    $csv.='"'.$this->escape(apply_filters('qwep_column_name_coupon_discount', __('Discount', 'wqep').' #'.($i+1), $i)).'"'.$sep;
            			}
        			}
        			
        			$csv.=$lb;
        
        		}
        			
        		$cols_before_products = array();
        		foreach ($this->included_order_keys as $key) {
        			

        			if(isset($order->$key))
        			{
        				if($key == 'customer_note')
        				{
        					$note = $this->escape($order->$key);
        					if(apply_filters('qwep_remove_line_breaks_in_customer_note', true))
        					{
        						$note = str_replace(array("\r\n", "\r", "\n"), ' ', $note);
        					}
        					$csv.='"'.$note.'"'.$sep;
        				}
        				elseif($key == 'order_total')
        					$csv.='"'.$this->escape(apply_filters('qwep_order_total_format', $order->$key)).'"'.$sep;
        				else
        					$csv.='"'.$this->escape($order->$key).'"'.$sep;

        				if(in_array($key, $this->data_to_repeat))
        					$cols_before_products[]=$this->escape($order->$key);
        				else
        					$cols_before_products[]='';
        			}
        			else
        			{
        				if($key == 'order_tax_detail' and count($different_taxes) > 1)
        				{
        					foreach($different_taxes as $taxslug=>$taxname)
        					{
        						$tax = $this->getSumTaxes($order->id, $taxslug);
        						$csv.='"'.$this->escape($tax).'"'.$sep;

        						if(in_array('order_tax_detail', $this->data_to_repeat))
		        					$cols_before_products[]=$this->escape($this->escape($tax));
		        				else
		        					$cols_before_products[]='';
        					}
        				
        				}
        				elseif($key == 'shipping_method_title')
        				{
        					$csv.='"'.$this->escape($order->get_shipping_method()).'"'.$sep;

        					if(in_array('shipping_method_title', $this->data_to_repeat))
	        					$cols_before_products[]=$this->escape($order->get_shipping_method());
	        				else
	        					$cols_before_products[]='';
        				}
        				elseif($key == 'shipping_complete_name')
        				{
                            $csv.='"'.$this->escape($order->shipping_first_name.' '.$order->shipping_last_name).'"'.$sep;

                            if(in_array('shipping_complete_name', $this->data_to_repeat))
	        					$cols_before_products[]=$this->escape($order->shipping_first_name.' '.$order->shipping_last_name);
	        				else
	        					$cols_before_products[]='';
        				}
        				elseif($key == 'billing_complete_name')
        				{
                            $csv.='"'.$this->escape($order->billing_first_name.' '.$order->billing_last_name).'"'.$sep;
        				

                            if(in_array('billing_complete_name', $this->data_to_repeat))
	        					$cols_before_products[]=$this->escape($order->billing_first_name.' '.$order->billing_last_name);
	        				else
	        					$cols_before_products[]='';
        				}
        				elseif(substr($key, 0, 12) == '_custom_key_')
        				{
        					$val = apply_filters('wqep_order'.$key, '', $order);
                            $csv.='"'.$this->escape($val).'"'.$sep;

                            if(in_array($key, $this->data_to_repeat))
	        					$cols_before_products[]=$this->escape($val);
	        				else
	        					$cols_before_products[]='';
        				}
        				elseif(strpos($key, '|') !== false)
        				{
        					$data = explode('|', $key);
							$array_key = $data[1];
							$key = $data[0];
							
							$order->$key = maybe_unserialize($order->$key);
							if(isset($order->$key))
							{
								$ok = $order->$key;
								if(isset($ok[$array_key]))
									$csv.='"'.$this->escape($ok[$array_key]).'"'.$sep;
								else
									$csv.='""'.$sep;

								if((in_array($key, $this->data_to_repeat)) and (isset($ok[$array_key])))
		        					$cols_before_products[]=$this->escape($ok[$array_key]);
		        				else
		        					$cols_before_products[]='';
							}
							else
							{
								$csv.='""'.$sep;
								$cols_before_products[]='';
							}
        				}
        				elseif($key == 'status')
        				{
        					if(function_exists('wc_get_order_statuses'))
        					{
        						$statuses = wc_get_order_statuses();
        						if(isset($statuses[$order->post_status]))
        							$st = $statuses[$order->post_status];
        						else
        							$st = $order->post_status;
        						
        						$csv.='"'.$st.'"'.$sep;

        						if(in_array($key, $this->data_to_repeat))
        							$cols_before_products[]=$st;
        						else
        							$cols_before_products[] = '';
        					}
                            
        				}
        				elseif($key == 'multiple_shipping' )
        				{
        					$packages = get_post_meta($order->id, '_wcms_packages', true);
        					global $woocommerce;
        					$data = '';
        					
        					$nb_pack = 0;
        					foreach($packages as $pack)
        					{
        						$nb_pack++;
								$pdts = $pack['contents'];
								$data = '';
                				foreach ( $pdts as $i => $pdt )
                    			{
									$data .= get_the_title($pdt['data']->id) .' x '. $pdt['quantity']."\r";
									
        						}
        						$csv.='"'.$data.'"'.$sep;
        						$cols_before_products[] = '';
        						
        						$data = $woocommerce->countries->get_formatted_address( $pack['full_address'] );
        						$data = str_replace(array('<br />', '<br/>', '<br>'), "\r", $data);
        						$csv.='"'.$data.'"'.$sep;
        						$cols_before_products[] = '';
        					}
        					
        					for($i=$nb_pack; $i<$max_pack; $i++)
        					{
        						$csv.='""'.$sep.'""'.$sep;
        					}
        					
        				}
        				elseif($key != 'order_tax_detail' and $key != 'number_of_different_items' and 
            			     $key != 'total_number_of_items' and
            			     $key != 'used_coupons' and
            			     $key != 'coupon_name' and
            			     $key != 'coupon_discount'
                        )
        				{
        					$csv.='""'.$sep;
        					$cols_before_products[] = '';
        				}
        			}	
        		}
        		
        	
        		$items = $order->get_items();
        		
        		//item totals
        		if(in_array('number_of_different_items', $this->included_order_keys))
                {
                    $csv.='"'.$this->escape(count($items)).'"'.$sep;

                    if(in_array('number_of_different_items', $this->data_to_repeat))
    					$cols_before_products[]=$this->escape(count($items));
    				else
    					$cols_before_products[]='';
        		}
        		
        		if(in_array('total_number_of_items', $this->included_order_keys))
                {
                    $item_counts = 0;
            		foreach($items as $item)
            		{
            			$item_counts += $item['qty'];
            		}
            		$csv.='"'.$this->escape($item_counts).'"'.$sep;

            		if(in_array('total_number_of_items', $this->data_to_repeat))
    					$cols_before_products[]=$this->escape($item_counts);
    				else
    					$cols_before_products[]='';
        		}
        		
        		$items = array_values($items);
        		//items infos
        		for($i=0; $i<$max_items; $i++)
        		{
        		    $this->set_time_limit(20);
        		    
        			if(isset($items[$i]))
        				$csv.=$this->infosProduit($order, $items[$i], $sep);
        			else
        				$csv.=$this->infosProduit($order, null, $sep);
        			
        			if($this->products_in_columns and $i==0)
        				break;
        			
        		}
        		
        		//coupons
        		if(in_array('used_coupons', $this->included_order_keys))
                {
                    $nb_coupons = count($order->get_used_coupons());
        		    $csv.='"'.$this->escape($nb_coupons).'"'.$sep;
                }
                
        		if(in_array('coupon_name', $this->included_order_keys) or in_array('coupon_discount', $this->included_order_keys))
                {
                    foreach($order->get_used_coupons() as $coupon)
            		{
            			if(in_array('coupon_name', $this->included_order_keys))
            				$csv.='"'.$this->escape($coupon).'"'.$sep;
            			
            			if(in_array('coupon_discount', $this->included_order_keys))
                        {
                            $montant_coupon = $this->getCouponAmount($order->id, $coupon);
            			    $csv.='"'.$this->escape($montant_coupon).'"'.$sep;
                        }
            		}
                }
        
        		
        		
        		//items infos
        		if($this->products_in_columns)
        		{
        			for($i=1; $i<count($items); $i++)
        			{	
                        $this->set_time_limit(20);
                        
        				$csv.=$lb;
        				
        				for($j=0; $j<count($cols_before_products); $j++)
        						$csv.= '"'.$cols_before_products[$j].'"'.$sep;						
        				
        				if(isset($items[$i]))
        					$csv.=$this->infosProduit($order, $items[$i], $sep);
        				else
        					$csv.=$this->infosProduit($order, null, $sep);
        			
        				
        			
        			}
        		}
        		
        		$csv.=$lb;
        	
        		$i++;
        	}
        }
        
        $this->exported_data = $csv;
		
		return $export_csv;
	}
	
	/*
     * Coupon usage export
     */
	protected function exportCoupons($csv, $export_csv, $sep, $lb, $start, $end)
	{
		
		$export_description = false;
		if(isset($this->export_settings['wqep_coupon_description']) and $this->export_settings['wqep_coupon_description'] == 'coupon_description') 
			$export_description = true;

		if(function_exists('wc_get_order_statuses'))
		{
			$customer_orders = new WP_Query( array(
					'posts_per_page' => -1,
					'post_type'   => 'shop_order',
					'post_status' => $this->export_settings['wqep_status'],
					'orderby' => 'post_date',
					'order' => 'ASC',
					'date_query' => array(
							array(
										'after' => $start,
										'before' =>  $end,
										'inclusive' => true,
									),
							),
				)
			);
    	}
    	else
    	{
    	
				$customer_orders = new WP_Query( array(
					'posts_per_page' => -1,
					'post_type'   => 'shop_order',
					'post_status' => 'publish',
					'orderby' => 'post_date',
					'order' => 'ASC',
					'date_query' => array(
							array(
										'after' => $start,
										'before' =>  $end,
										'inclusive' => true,
									),
							),
					
					'tax_query'=>array(
							array(
	
								 'taxonomy' =>'shop_order_status',
								 'field' => 'id',
								 'terms' => $this->export_settings['wqep_status']
							)
					)
				)
			);

    	
    	}
    	
    
    	$customer_orders = $customer_orders->get_posts();
    
    	
    	$total_orders = (int) sizeof($customer_orders);
    	
    	$max_coupons = $this->getMaxCoupons($customer_orders);
    
    	$i=0;
    
    	if( count( $customer_orders ) == 0 or $max_coupons == 0) {
    		$export_csv = false;
    		$this->errors[] = new WP_Error('error', __('No coupon used in that period.', 'wqep'));
    	}
    	else {
    	
    		foreach ( $customer_orders as $customer_order ) {
                
                $this->set_time_limit(20);
                
                if(function_exists('wc_get_order_statuses'))
        			$order = new WC_Order($customer_order);
        		else
        		{
        			$order = new WC_Order();
    				$order->populate( $customer_order );
        		}
    			
    		
    			if($i==0) {
    
    				$csv.='"'.$this->escape(__('Order id', 'wqep')).'"'.$sep;
    				$csv.='"'.$this->escape(__('Order date', 'wqep')).'"'.$sep;	
    				$csv.='"'.$this->escape(__('Used coupons', 'wqep')).'"'.$sep;
    
    				for($i=0; $i<$max_coupons; $i++)
    				{
    					$csv.='"'.$this->escape(__('Coupon', 'woocommerce')).' #'.($i+1).'"'.$sep;
    					$csv.='"'.$this->escape(__('Discount', 'wqep')).' #'.($i+1).'"'.$sep;
    					
    					if($export_description)
    						$csv.='"'.$this->escape(__('Description', 'wqep')).' #'.($i+1).'"'.$sep;
    					
    				}
    				
    				$csv.=$lb;
    
    			} // column names for row one
    			
    			$nb_coupons = count($order->get_used_coupons());
    
    			if($nb_coupons>0){	
    	
    			$csv.='"'.$this->escape($order->id).'"'.$sep;
    			$csv.='"'.$this->escape($order->order_date).'"'.$sep;
    
    			
    
    			$csv.='"'.$this->escape($nb_coupons).'"'.$sep;
    			
    			foreach($order->get_used_coupons() as $coupon)
    			{
    				$csv.='"'.$this->escape($coupon).'"'.$sep;
    				
    				$montant_coupon = $this->getCouponAmount($order->id, $coupon);
    				$csv.='"'.$this->escape($montant_coupon).'"'.$sep;
    				
    				if($export_description)
					{
						$coupon = new WC_Coupon( $coupon );
						if(is_object($coupon) and isset($coupon->id))
						{
							$c = get_post($coupon->id);
							if(is_object($c))
								$description = $c->post_excerpt;
							else
								$description = '';
						}
						else
							$description = '';
							
						$csv.='"'.$this->escape($description).'"'.$sep;
					}
    			}
    
    
    			$csv.=$lb;
    			
    			} // has_coupon
    
    			$i++;
    		}
    	}
    	
    	$this->exported_data = $csv;
		
		return $export_csv;
	}
	
	/**
     * Customer export
     */
    protected function exportCustomers($csv, $export_csv, $sep, $lb, $start, $end)
    {
        $args = array(
			'fields' => 'all_with_meta',
			'role' => apply_filters('wqep_user_role', 'customer'),
			'orderby' => 'user_registered',
			'order' => 'ASC',
		);

		$customers = get_users($args);
		
		foreach($customers as $k => $customer) {
            $this->set_time_limit(20);
			if($customer->user_registered<$start or $customer->user_registered>$end){
				unset($customers[$k]);
			}
		}

		// calculate order activity if asked
		if(array_search('sales', $this->export_settings['wqep_userdata'])!==false) {

				$customers = $this->addUserActivity($customers);
		}				

		if(array_search('identity', $this->export_settings['wqep_userdata'])!==false){


				foreach ($this->included_user_identity_keys as $key) {

					$key = ucwords(str_replace('_', ' ', $key));
							$key2 = '';
							if(strpos($key, 'Billing')!== false)
							{
								$key = str_replace('Billing', '', $key);
								$key2 = ' ('.__('Billing', 'wqep').')';
							}
							if(strpos($key, 'Shipping')!== false)
							{
								$key = str_replace('Shipping', '', $key);
								$key2 = ' ('.__('Shipping', 'woocommerce').')';
							}
							
					$csv.='"'.$this->escape(__(trim($key), 'woocommerce').$key2).'"'.$sep;
				}

				
		}
		
		if(array_search('billing', $this->export_settings['wqep_userdata'])!==false){
				
				foreach ($this->included_billing_information_keys as $key) {
					
				
					$key = ucwords(str_replace('_', ' ', $key));
							$key2 = '';

							if(strpos($key, 'Billing')!== false)
							{
								$key = str_replace('Billing', '', $key);
								$key2 = ' ('.__('Billing', 'wqep').')';
							}
						
							
					$csv.='"'.$this->escape(__(trim($key), 'woocommerce').$key2).'"'.$sep;

				}
		}

		if(array_search('shipping', $this->export_settings['wqep_userdata'])!==false){
				
				foreach ($this->included_shipping_information_keys as $key) {
					
				
					$key = ucwords(str_replace('_', ' ', $key));
							$key2 = '';
						
							if(strpos($key, 'Shipping')!== false)
							{
								$key = str_replace('Shipping', '', $key);
								$key2 = ' ('.__('Shipping', 'woocommerce').')';
							}
							
					$csv.='"'.$this->escape(__(trim($key), 'woocommerce').$key2).'"'.$sep;

				}

		}


		if(array_search('sales', $this->export_settings['wqep_userdata'])!==false){

				$csv.='"'.$this->escape(__('Orders count','wqep')).'"'.$sep;
				$csv.='"'.$this->escape(__('Orders total','wqep')).'"'.$sep;

		}
		
		$csv = rtrim($csv, $sep);
		$csv.=$lb;


		if( count( $customers) == 0) {
			$export_csv = false;
			$this->errors[] = new WP_Error('error', __('No customer to export in that period.', 'wqep'));
		}
		else {

			foreach ($customers as $customer) {
                $this->set_time_limit(20);
                
				if(array_search('identity', $this->export_settings['wqep_userdata'])!==false) {

					foreach ($this->included_user_identity_keys as $key) {

						$csv.='"'.$this->escape($customer->{$key}).'"'.$sep;
					
					}
					

				}

				if(array_search('billing', $this->export_settings['wqep_userdata'])!==false) {

					foreach ($this->included_billing_information_keys as $key) {

						$csv.='"'.$this->escape($customer->{$key}).'"'.$sep;
					
					}

				}

				if(array_search('shipping', $this->export_settings['wqep_userdata'])!==false) {

					foreach ($this->included_shipping_information_keys as $key) {

						$csv.='"'.$this->escape($customer->{$key}).'"'.$sep;
					
					}

				}
			

				if(array_search('sales', $this->export_settings['wqep_userdata'])!==false) {

					$csv.='"'.$this->escape($customer->wqep_nb_order).'"'.$sep;
					$csv.='"'.$this->escape($customer->wqep_total_orderered).'"'.$sep;

				}

				$csv = rtrim($csv, $sep);
				$csv.=$lb;
			}

		}
		
		$this->exported_data = $csv;
		
		return $export_csv;
    
    } // exportCustomers
	
	/*
     * Product informations
     */
	private function infosProduit($order, $item, $sep)
	{
		$csv = '';
		
		if(isset($item)) //product exists
		{
			$product = $order->get_product_from_item($item);
			if($product instanceof WC_Product) //product is a product
			{
			    // default product informations
                foreach ($this->included_order_default_product_keys as $pdt_key)
				{
				    if($pdt_key == 'sku')
				        $value = $product->get_sku();
				    if($pdt_key == 'name')
				        $value = $product->get_title();
				    if($pdt_key == 'quantity')
				        $value = $item['qty'];
				    if($pdt_key == 'line_price_without_taxes')
				        $value = apply_filters('qwep_line_price_without_taxes_format', $order->get_line_total($item, false));
				    if($pdt_key == 'line_price_with_taxes')
				        $value = apply_filters('qwep_line_total_format', $order->get_line_total($item, true));
				    
				    $csv.='"'.$this->escape($value).'"'.$sep;
				}
				
				// custom product informations		
				foreach ($this->included_order_product_keys as $pdt_key)
				{
					$pm = '';
					
					if(strpos($pdt_key, '|') !== false)
					{
						$data = explode('|', $pdt_key);
						$array_key = $data[1];
						$pdt_key = $data[0];
					}
					
					if($product instanceof WC_Product_Variation)
					{
						$pm = get_post_meta($product->variation_id, $pdt_key, true);
					}
					if(!$pm)
						$pm = get_post_meta($product->id, $pdt_key, true);
				
					if(!$pm)
						$pm = $this->getItemMeta(str_replace('attribute_', '', $pdt_key), $item);
					
					if(is_array($pm) and isset($array_key))
					{
						if(isset($pm[$array_key]))
						{
							$pm = $pm[$array_key];
						}
		
					} 
					
					if(!$pm)
					{
						if(substr($pdt_key, 0, 12) == '_custom_key_')
        				{
                            $pm=apply_filters('wqep_product'.$pdt_key, '', $order, $item);
        				}
					}
					
					
					$csv.='"'.$this->escape($pm).'"'.$sep;
				}
				
				foreach ($this->included_order_product_taxonomies as $taxo_key)
				{
					$pm = wp_get_post_terms($product->id, $taxo_key, array('fields'=>'names'));
					
					if(is_array($pm))
						$csv.='"'.$this->escape(implode(', ', $pm)).'"'.$sep;
					else
						$csv.='""'.$sep;
				}
			}
			else //product does not exists anymore
			{
                //default product key
                foreach ($this->included_order_default_product_keys as $pdt_key)
                {
                    if($pdt_key == 'sku')
				        $value = __('Deleted Item', 'wqep');
				    if($pdt_key == 'name')
				        $value = $item['name'];
				    if($pdt_key == 'quantity')
				        $value = $item['qty'];
				    if($pdt_key == 'line_price_without_taxes')
				        $value = apply_filters('qwep_line_price_without_taxes_format', $order->get_line_total($item, false));
				    if($pdt_key == 'line_price_with_taxes')
				        $value = apply_filters('qwep_line_total_format', $order->get_line_total($item, true));
				        
                    $csv.='"'.$this->escape($value).'"'.$sep;
                }

				// custom product informations => empty
				foreach ($this->included_order_product_keys as $pdt_key)
					$csv.='""'.$sep;
					
				// custom taxo informations => empty
				foreach ($this->included_order_product_taxonomies as $pdt_key)
					$csv.='""'.$sep;
			}
		}
		else //empty line
		{
			// default product informations => empty
			foreach ($this->included_order_default_product_keys as $pdt_key)
				$csv.='""'.$sep;
			
			// custom product informations => empty
			foreach ($this->included_order_product_keys as $pdt_key)
				$csv.='""'.$sep;
		}
		
		return $csv;
	} //infosProduit


	public function export()
	{
		if( $_SERVER['REQUEST_METHOD']!=='POST' or !isset($_POST['wqep_action']) or ($_POST['wqep_action']!=='tocsv') or !current_user_can( apply_filters('wqep_caps','administrator')))
	  		return;

		$this->export_settings = $_POST;

		if($this->get_data()) {
			$filename = $this->export_settings['wqep_type'] .'-'. date( 'Y-m-d_H-i-s' ) . '.csv';
	  
			header( 'Content-Encoding: '. get_option( 'blog_charset' ));
			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			
			if($this->export_settings['wqep_exportformat']=='utf16')
			{
				header ( 'Content-Type: application/vnd.ms-excel');
				print chr(255) . chr(254) . mb_convert_encoding(html_entity_decode($this->exported_data, ENT_QUOTES, get_option( 'blog_charset' )), 'UTF-16LE', get_option( 'blog_charset' ));
				die;
			}
			elseif($this->export_settings['wqep_exportformat']=='gbk')
			{
				header ( 'Content-Type: application/vnd.ms-excel');
				die(iconv("UTF-8","gbk//TRANSLIT",$this->exported_data));
			}
			else
			{
				header( 'Content-Type: text/csv; charset=' . get_option( 'blog_charset' ), true );
				die($this->exported_data);
			}
		
		}
		
	} //export
	
	private function getMaxPack($post_orders)
	{
		$max = 0;
		foreach($post_orders as $post)
		{
			$packs = get_post_meta($post->ID, '_wcms_packages', true);
			
			$nb = count($packs);
			if($nb>$max)
				$max=$nb;
		}
		
		return $nb;
	}
	
	/**
	 * Add wqep_total_orderered and wqep_nb_order to each user
	 *
	 * @param array $orders : orders to consider to get the max items
	 * @return int number of maximium items
	 */
	private function getMaxItems($post_orders)
	{	
		global $wpdb;
		$ids = array();
		foreach($post_orders as $post)
		{
			$ids[] = $post->ID;
		}
		
		if(count($post_orders)>0)
		{
    		$query = '
    			SELECT MAX(total) as total FROM
    			
    			(SELECT count(*) as total
    				FROM '.$wpdb->prefix.'woocommerce_order_items
    			WHERE 
    				order_item_type = "line_item"
    				AND order_id in ('.implode(', ', $ids).')
    			GROUP BY 
    				order_id
    			ORDER BY total desc) as tmp
    		';
    		
    		$res = $wpdb->get_var($query);
    
    		if($res)
    			return $res;
    		else
    			return 0;
        }
		else
            return 0;
	}
	
	
	public function getItemMeta($key, $item)
	{
		if(isset($item[$key]))
			return $item[$key];
		else
			return '';
	}
	
	
	private function getMaxCoupons($post_orders)
	{	
		global $wpdb;
		$ids = array();
		foreach($post_orders as $post)
		{
			$ids[] = $post->ID;
		}
		
		if(count($post_orders)>0)
		{
    		$query = '
    			SELECT MAX(total) as total FROM
    			
    			(SELECT count(*) as total
    				FROM '.$wpdb->prefix.'woocommerce_order_items
    			WHERE 
    				order_item_type = "coupon"
    				AND order_id in ('.implode(', ', $ids).')
    			GROUP BY 
    				order_id
    			ORDER BY total desc) as tmp
    		';
    		
    		$results = $wpdb->get_results($query);
    
    		if(isset($results[0]))
    			return $results[0]->total;
    		else
    			return 0;
		}
		else
		  return 0;
	}
	
	
	private function set_time_limit($int)
	{
		$safe_mode = ini_get('safe_mode');
		if(!$safe_mode or $safe_mode == 'Off' or $safe_mode == 'off' or $safe_mode == 'OFF')
		{
			@set_time_limit($int);
		}
	}
	
	private function getCouponAmount($order_id, $coupon)
	{
		global $wpdb;
		
		$query = '
			SELECT meta_value
				FROM '.$wpdb->prefix.'woocommerce_order_items oi
				LEFT JOIN '.$wpdb->prefix.'woocommerce_order_itemmeta oim
					ON oi.order_item_id = oim.order_item_id
			WHERE 
				order_item_type = "coupon"
				AND order_id ='.$order_id.'
				AND order_item_name="%s"
				AND meta_key="discount_amount"
		';
		
		$results = $wpdb->get_results($wpdb->prepare($query, $coupon));

		if(isset($results[0]))
			return round($results[0]->meta_value, 2);
		else
			return 0;
	}
	
	/*
	*	Return an array width the slugs of the taxes used by the set of orders
	*/
	protected function getDifferentTaxes($post_orders)
	{
		global $wpdb;
		$ids = array();
		foreach($post_orders as $post)
		{
			$ids[] = $post->ID;
		}
		
		$query = '
			SELECT order_item_name, meta_value
				FROM '.$wpdb->prefix.'woocommerce_order_items oi, '.$wpdb->prefix.'woocommerce_order_itemmeta oim
			WHERE 
			    oi.order_item_id=oim.order_item_id
				AND oi.order_item_type = "tax"
				AND oi.order_item_name != ""
				AND oi.order_id in ('.implode(', ', $ids).')
				AND meta_key="label"
			GROUP BY 
				oi.order_id, oi.order_item_name
			
		';
		
		$results = $wpdb->get_results($query);

		$tab = array();
		foreach($results as $res)
		{
		   $tab[$res->order_item_name] = $res->meta_value;
		}
		
		return $tab;
	}
	
	/*
	*	Return an the amount of a specific taxe and a specific order
	*/
	protected function getSumTaxes($order_id, $taxslug)
	{
	   global $wpdb;
	   $query = '
			SELECT sum(meta_value) as meta_value
				FROM '.$wpdb->prefix.'woocommerce_order_items oi, '.$wpdb->prefix.'woocommerce_order_itemmeta oim
			WHERE 
			    oi.order_item_id=oim.order_item_id
				AND oi.order_item_type = "tax"
				AND oi.order_id = '.$order_id.'
				AND (meta_key="tax_amount" or meta_key="shipping_tax_amount")
				AND order_item_name= "'.$taxslug.'"
			
			
		';
		//echo $query;exit;
		$results = $wpdb->get_results($query);

		if(isset($results[0]))
			return round($results[0]->meta_value, 2);
		else
			return 0;
	}
	
	/**
	 * Add wqep_total_orderered and wqep_nb_order to each user
	 *
	 * @param array $customers array of customers to calculate activity
	 * @return array $customers with wqep_total_orderered and wqep_nb_order added
	 */
	private function addUserActivity($customers)
	{
		global $wpdb;
		
		$ids = array();
		$tmp = array();
		
		if(count($customers))
		{
            foreach($customers as $customer) {
			    $ids[] = $customer->data->ID;
		    }
		
    		$status = apply_filters('wqep_status_for_user_activity_filter', $this->status_for_user_activity);
    		
    		if(count($status)==0)
    			$status = array('completed');
    		
    		
    		
    		if(function_exists('wc_get_order_statuses'))
    		{
    			foreach($status as $i=>$s)
    			{
    				$status[$i] = 'wc-'.$s;
    			}
    			$status = implode('", "', $status);
    			
				$query = '
					SELECT 
						user.meta_value AS user_id, 
						sum(montant.meta_value) AS wqep_total_orderered, 
						count(*) AS wqep_nb_order 
					FROM '.$wpdb->prefix.'posts 
	
					LEFT JOIN '.$wpdb->prefix.'postmeta montant 
						ON id=montant.post_ID 
						AND montant.meta_key="_order_total"
					LEFT JOIN '.$wpdb->prefix.'postmeta user
						ON id=user.post_ID 
						AND user.meta_key="_customer_user" 
		
					
					WHERE post_type="shop_order" 
						AND user.meta_value in ('.implode(',', $ids).') 
						AND post_status in ("'.$status.'")
					GROUP BY user.meta_value
				';
				
				
    		}
    		else
    		{
    			$status = implode('", "', $status);
    			
					$query = '
					SELECT 
						user.meta_value AS user_id, 
						sum(montant.meta_value) AS wqep_total_orderered, 
						count(*) AS wqep_nb_order 
					FROM '.$wpdb->prefix.'posts 
					LEFT JOIN '.$wpdb->prefix.'postmeta montant 
						ON id=montant.post_ID 
						AND montant.meta_key="_order_total"
					LEFT JOIN '.$wpdb->prefix.'postmeta user
						ON id=user.post_ID 
						AND user.meta_key="_customer_user" 
					
					LEFT JOIN
						'.$wpdb->prefix.'term_relationships wtr
						ON user.post_ID = wtr.object_id
					LEFT JOIN '.$wpdb->prefix.'term_taxonomy wtt
						ON wtt.term_taxonomy_id = wtr.term_taxonomy_id
						AND wtt.taxonomy = "shop_order_status"
					INNER JOIN '.$wpdb->prefix.'terms wt
						ON wt.term_id = wtt.term_id
						AND wt.slug in ("'.$status.'")	
					
					WHERE post_type="shop_order" 
						AND user.meta_value in ('.implode(',', $ids).') 
					GROUP BY user.meta_value
				';
    		
    		}
    		
    		$results = $wpdb->get_results($query);
    		foreach($results as $res) {
    			$tmp[$res->user_id] = $res;
    		}
    		
    		foreach($customers as $i => $customer) {
    			if(isset($tmp[$customer->data->ID]))
    			{
    				$customers[$i]->wqep_total_orderered = $tmp[$customer->data->ID]->wqep_total_orderered;
    				$customers[$i]->wqep_nb_order = $tmp[$customer->data->ID]->wqep_nb_order;
    			}
    		}
        }
		
		return $customers;
	} //addUserActivity

	public function escape($str)
	{
		$str = str_replace('"', '""',$str);
		return $str;
	}

	public function expand_escape($string)
	{
		return preg_replace_callback(
			'/\\\([nrtvf]|[0-7]{1,3}|[0-9A-Fa-f]{1,2})?/',
			create_function(
				'$matches',
				'return ($matches[0] == "\\\\") ? "" : eval( sprintf(\'return "%s";\', $matches[0]) );'
			),
			$string
		);
	}

    /**
     * Desactivition plugin
     *
     * 
     * @since 2.0
     * 
     * @access public
     * @return void
     */
    public static function deactivation()
    {
		// All crons
		$crontab = get_option('qwep_scheduled_options');
		
		if(!empty($crontab)){
			foreach ($crontab as $cid => $settings) {
				// Remove the cron
				wp_clear_scheduled_hook('qwep_do_cron_hook',array($cid));
			}
		}	
		
		delete_option('qwep_scheduled_options');

    } //deactivation

} //WooCommerce_Quick_Export_Plugin

include('automated.php');


} //if
	
	
function WooCommerce_Simple_Quick_Export_Plugin_Loader() {
	if( class_exists('Woocommerce') ){
		if(is_admin()){
			$WooCommerce_Quick_Export_Plugin = new WooCommerce_Quick_Export_Plugin();
		}

		$WooCommerce_Quick_Export_Automated_Class = new WooCommerce_Quick_Export_Automated_Class();
	}
}

add_action( 'plugins_loaded' , 'WooCommerce_Simple_Quick_Export_Plugin_Loader');