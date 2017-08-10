<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WooCommerce_Quick_Export_Automated_Class
 *
 * @since 2.0
 */

if ( ! class_exists( 'WooCommerce_Quick_Export_Automated_Class' ) ) {

class WooCommerce_Quick_Export_Automated_Class extends WooCommerce_Quick_Export_Plugin
{

	function __construct()
	{	
		$this->hooks();
		add_action( 'qwep_do_cron_hook',array(&$this,'qwep_do_cron') );
		
		
	} //__construct
	
	public function hooks()
	{	
		add_filter( 'woocommerce_reports_charts' , array($this, 'tab' ));
		add_action( 'admin_init', array( &$this,'options_init') );
		add_option( 'qwep_scheduled_options', '', '', 'no' );
		add_filter( 'cron_schedules', array( &$this,'cron_add_weekly') );	
		
		//add_action( 'init', array( &$this,'test') );
	} //hooks


	public function test()
	{
		$this->qwep_do_cron('55057154237fb');
	}

	public function options_init()
	{
		register_setting( 'qwep_scheduled_options_group', 'qwep_scheduled_options', array( &$this, 'options_validate' ) );
	} // options_init

	public function options_validate($options)
	{	

		if($_SERVER['REQUEST_METHOD'] == 'POST') {

			// Sanitize Email
            $recipienta = explode(',', $options['email_settings']['email_to']);

			$options['email_settings']['email_to'] = array();

			if(!empty($recipienta)){
				foreach ($recipienta as $email) {
					if(is_email($email))
					$options['email_settings']['email_to'][] = $email;
				}
			}

			// Dealing interval
			$arecurrence = wp_get_schedules();
			$postinterval = $options['cron_settings']['interval'];

			$options['cron_settings']['interval'] = $arecurrence[$postinterval]['interval'];
			$options['cron_settings']['display'] = $arecurrence[$postinterval]['display'];

			$options['cron_settings']['from_date'] = date("Y-m-d H:m:s", current_time('timestamp')-$options['cron_settings']['interval']);

			// Saving event
			$eventid = uniqid();
			$current = get_option('qwep_scheduled_options');
			$next = $current;
			$next[$eventid] = $options; 	

			if(!empty($options['email_settings']['email_to'])) {
				// Setting Cron
				wp_schedule_event( time() , $postinterval , 'qwep_do_cron_hook', array( $eventid ) );
				return $next;
			}
			else {
				return $current;
			}
		}
		else{
			return $options;
		}

	} //options_validate
	
	
	public function tab( $charts )
	{
	    $charts['wqep-scheduled-export'] = array(
			'title'  => __( 'Scheduled Export', 'wqep' ),
			'charts' => array(
				'overview' => array(
					
					'title'       => __( 'Quick Export Scheduled module', 'wqep' ),
					'description' => __( 'The automatic export module will export all orders created between to execution of the task. The first task will be triggered once you published it including all orders between now and the chosen interval.', 'wqep' ),
					'hide_title'  => true,
					'function'    => array($this, 'panel')
				),
			)
		);

		return $charts;
	} //tab


	public function panel()
	{
		// Handle delete
		if (isset($_GET['cid']) and wp_verify_nonce($_GET['_wpnonce'], 'trash-scheduled-export_'.$_GET['cid']) )
            $this->delete_scheduled_export($_GET['cid']);
	

		if( count( $this->errors ) > 0 ) {
			echo '<div class="error"><ul>'; 
			foreach( $this->errors as $error) {
				echo '<li>'.$error->get_error_message().'</li>';
			}
			echo '</ul></div>'; 
		}
		
		?>

		<div id="poststuff">

		<?php $options = get_option('qwep_scheduled_options'); ?>
		
		<h2><?php echo __('Active scheluded export','wqep'); ?></h2>

		<table class="wp-list-table widefat fixed scheduled-export" cellspacing="0">
		<thead>
		<tr>
			<th scope="col" id="scheduled-id" class="manage-column column-scheduled-id">
				<?php _e('Scheduled id','wqep');?>
			</th>
			<th scope="col" id="scheduled-recurrence" class="manage-column column-scheduled-recurrence">
				<?php _e('Recurrence','wqep');?>
			</th>
			<th scope="col" id="scheduled-recipients" class="manage-column column-scheduled-recipients" style="">
				<?php _e('Recipients','wqep');?>
			</th>
			<th scope="col" id="scheduled-next" class="manage-column column-scheduled-next">
				<?php _e('Next event','wqep');?>
			</th>
			<th scope="col" id="scheduled-actions" class="manage-column column-scheduled-actions" style="">
				<?php _e('Actions','wqep');?>
			</th>
		</tr>
		</thead>

		<tfoot>
			<tr>
			<th scope="col" id="scheduled-id" class="manage-column column-scheduled-id">
				<?php _e('Scheduled id','wqep');?>
			</th>
			<th scope="col" id="scheduled-recurrence" class="manage-column column-scheduled-recurrence">
				<?php _e('Recurrence','wqep');?>
			</th>
			<th scope="col" id="scheduled-recipients" class="manage-column column-scheduled-recipients">
				<?php _e('Recipients','wqep');?>
			</th>
			<th scope="col" id="scheduled-next" class="manage-column column-scheduled-next">
				<?php _e('Next event (GMT Time)','wqep');?>
			</th>
			<th scope="col" id="scheduled-actions" class="manage-column column-scheduled-actions">
				<?php _e('Actions','wqep');?>
			</th>
		</tr>


		</tfoot>

		<tbody id="scheduled-list">
			
			<?php if(empty($options)) { ?>
			<tr class="no-items">
				<td colspan="5"><?php _e('No scheduled export yet','wqep'); ?></td>
			</tr>
			<?php } else {

    			// Delete permalink
    			if(substr($this->woocommerce_version, 0, 3) == '2.0') {
                    $taburl = admin_url( 'admin.php?page=woocommerce_reports&tab=wqep-scheduled-export' );
                } else {
                    $taburl = admin_url( 'admin.php?page=wc-reports&tab=wqep-scheduled-export' );
                }
    
    			foreach ($options as $id => $settings) {
    				echo '<tr>';
    				echo '<td>'.$id.'</td>';
    				echo '<td>'.(isset($settings['cron_settings']['display'])?$settings['cron_settings']['display']:'').'</td>';
    				echo '<td>'.(isset($settings['email_settings']['email_to'])?implode(', ', $settings['email_settings']['email_to']):'').'</td>';
    				echo '<td>';
    				
    				printf(__('<code>%s</code>'), date_i18n(get_option( 'date_format' ).' '.get_option( 'time_format' ), wp_next_scheduled( 'qwep_do_cron_hook' ,array( $id ) ), false));	
    			
    				echo '</td>';
    				echo '<td><a href="'.wp_nonce_url( add_query_arg( 'cid', $id, $taburl ) ,'trash-scheduled-export_'.$id ).'">'.__('Delete permanently','wqep').'</a></td>';
    				echo '</tr>';
    			}
			}
			?>
		</tbody>

		</table>
		
		<h2><?php echo __('Create new export','wqep'); ?></h2>

		<form id="wqep-form" method="post" action="options.php">

			<?php settings_fields('qwep_scheduled_options_group'); ?>
			<?php $arecurrence = wp_get_schedules(); ?>

			<div class="postbox">
				<h3><span><?php _e('Recurrence', 'wqep');?></span></h3>
				<div class="inside">
				<table class="form-table" id="wqep-form-table-recurrence-options">
					<tr valign="top">
						<th scope="row"><label><?php _e('Recurrence', 'wqep');?></label></th>
						<td>
						<select required="required" name="qwep_scheduled_options[cron_settings][interval]">
							<option value="" selected="selected"><?php echo __('Choose','wqep'); ?></option>
							<?php 
								foreach ($arecurrence as $recurrence => $details) {
									echo '<option value="'.$recurrence.'">'.__($details['display']).'</option>';
								}
							?>
						</select>
						</td>
					</tr>
				</table>
				</div>
			</div>

			<div class="postbox">
				<h3><span><?php _e('Email options', 'wqep');?></span></h3>
				<div class="inside">
				<table class="form-table" id="wqep-form-table-email-options">
					<tr valign="top">
						<th scope="row"><?php _e('Email recipient(s)', 'wqep');?></th>
						<td><input required="required" type="text" class="text" name="qwep_scheduled_options[email_settings][email_to]" value="" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Email subject', 'wqep');?></th>
						<td><input required="required" type="text" class="text" name="qwep_scheduled_options[email_settings][email_subject]" value="" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Email message', 'wqep');?></th>
						<td><textarea required="required" class="large-text code" name="qwep_scheduled_options[email_settings][email_message]"></textarea></td>
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
						<td><input type="text" class="small-text" name="qwep_scheduled_options[export_settings][wqep_separator]" value="," /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Line Breaks', 'wqep');?></th>
						<td>
							<input type="text" class="small-text" name="qwep_scheduled_options[export_settings][wqep_linebreak]" value="\r\n" />
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row"><?php _e('Export Format', 'wqep');?></th>
						<td>
						<select name="qwep_scheduled_options[export_settings][wqep_exportformat]" id="wqep_exportformat">
								<option value="utf8" <?php if(get_locale() != 'zh_CN') echo 'selected="selected"'; ?> ><?php _e('Default (utf-8)', 'wqep'); ?></option>
								<option value="utf16" ><?php _e('Better Excel Support (utf-16)', 'wqep'); ?></option>
								<option value="gbk" <?php if(get_locale() == 'zh_CN') echo 'selected="selected"'; ?> ><?php _e('Chinese Excel Support (gbk)', 'wqep'); ?></option>
						</select>
						</td>
					<tr<
				</tr>
				
				</table>

				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save') ?>" />
				</p>

				</div>
			</div>
		</form>
		</div><!-- poststuff -->

		<?php 
	} //panel


	public function export()
	{
        if( $this->get_data() ) {
            $filename = $this->export_settings['wqep_type'] .'-'. date( 'Y-m-d_H-i-s' ) . '.csv';
			$ct = 'text/csv';
			$data = $this->exported_data;
			
			if($this->export_settings['wqep_exportformat']=='utf16')
			{
				$ct = 'application/vnd.ms-excel';
				$data = chr(255) . chr(254) . mb_convert_encoding(html_entity_decode($this->exported_data, ENT_QUOTES, get_option( 'blog_charset' )), 'UTF-16LE', get_option( 'blog_charset' ));
				
			}
			elseif($this->export_settings['wqep_exportformat']=='gbk')
			{
				$ct = 'application/vnd.ms-excel';
				$data = iconv("UTF-8","gbk//TRANSLIT",$this->exported_data);
			}
			else
			{
				//utf8
			}
			
			
            // sending email
            $this->mail(
    		  	$this->export_settings['wqep_scheduled_mailto'],
    		  	$this->export_settings['wqep_scheduled_subject'],
    		  	$this->export_settings['wqep_scheduled_message'],
    		  	'',
                array( array($data,  $filename , 'base64', $ct) ) 
            );
        }
    } //export


	public function qwep_do_cron($args)
	{
		$docron = true;
		
		if(apply_filters('wqep_do_cron', $docron))
		{
			// Now
			$now = current_time('mysql');

			// Available order status
			$os = array();

			if(!function_exists('wc_get_order_statuses'))
			{
				$shop_order_status = get_terms( 'shop_order_status', 'orderby=id&hide_empty=1' );
				
				foreach ($shop_order_status as $status): 
					$os[] = $status->term_id;
				endforeach;
			}
			else
			{
				$shop_order_status = wc_get_order_statuses();
				foreach($shop_order_status as $i=>$s)
				{
					$os[] = $i;
				}
			}
			
			

			// Cron specific data
			$crontab = get_option('qwep_scheduled_options');

			$cron = $crontab[$args];

			$data = array(
				'wqep_type' => 'orders', 
				'wqep_status' => $os,
				'wqep_action_type' => 'tocsv',
				'wqep_action' => 'scheduled',
				'wqep_scheduled_mailto' => $cron['email_settings']['email_to'],
				'wqep_scheduled_subject' => $cron['email_settings']['email_subject'],
				'wqep_scheduled_message' => $cron['email_settings']['email_message'],
				'wqep_start_date' => $cron['cron_settings']['from_date'],
				'wqep_end_date' => $now,
				'wqep_separator' => $cron['export_settings']['wqep_separator'],
				'wqep_linebreak' => $cron['export_settings']['wqep_linebreak'],
				'wqep_exportformat' => $cron['export_settings']['wqep_exportformat'],
			);
	
			$this->init_class_vars();

			$this->export_settings = apply_filters('qwep_automated_settings', $data);
			$this->export();

			// update cron
			$cron['cron_settings']['from_date'] = $now;
			$crontab[$args] = $cron;
			update_option('qwep_scheduled_options', $crontab );
		}
	} //qwep_do_cron


	public function cron_add_weekly( $schedules )
	{
	 	// Adds once weekly to the existing schedules.
	 	$schedules['weekly'] = array(
	 		'interval' => 604800,
	 		'display' => __('Once Weekly','wqep')
	 	);
	 	return $schedules;
	 	
 	} //cron_add_weekly


	public function delete_scheduled_export($cid)
	{
		// Remove the option
		$crontab = get_option('qwep_scheduled_options');
		unset($crontab[$cid]);
		update_option('qwep_scheduled_options', $crontab );
		
		// Remove the cron
		wp_clear_scheduled_hook('qwep_do_cron_hook',array($cid));
		
	} //delete_scheduled_export


	public function mail( $to, $subject, $message, $headers = '', $attachments = array() )
	{
    	// Compact the input, apply the filters, and extract them back out
    	extract( apply_filters( 'wp_mail', compact( 'to', 'subject', 'message', 'headers', 'attachments' ) ) );
    
    	if ( !is_array($attachments) )
    		$attachments = explode( "\n", str_replace( "\r\n", "\n", $attachments ) );
    
    
    	global $phpmailer;
    
    	// (Re)create it, if it's gone missing
    	if ( !is_object( $phpmailer ) || !is_a( $phpmailer, 'PHPMailer' ) ) {
    		require_once ABSPATH . WPINC . '/class-phpmailer.php';
    		require_once ABSPATH . WPINC . '/class-smtp.php';
    		$phpmailer = new PHPMailer( true );
    	}
    
    	// Headers
    	if ( empty( $headers ) ) {
    		$headers = array();
    	} else {
    		if ( !is_array( $headers ) ) {
    			// Explode the headers out, so this function can take both
    			// string headers and an array of headers.
    			$tempheaders = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
    		} else {
    			$tempheaders = $headers;
    		}
    		$headers = array();
    		$cc = array();
    		$bcc = array();
    
    		// If it's actually got contents
    		if ( !empty( $tempheaders ) ) {
    			// Iterate through the raw headers
    			foreach ( (array) $tempheaders as $header ) {
    				if ( strpos($header, ':') === false ) {
    					if ( false !== stripos( $header, 'boundary=' ) ) {
    						$parts = preg_split('/boundary=/i', trim( $header ) );
    						$boundary = trim( str_replace( array( "'", '"' ), '', $parts[1] ) );
    					}
    					continue;
    				}
    				// Explode them out
    				list( $name, $content ) = explode( ':', trim( $header ), 2 );
    
    				// Cleanup crew
    				$name    = trim( $name    );
    				$content = trim( $content );
    
    				switch ( strtolower( $name ) ) {
    					// Mainly for legacy -- process a From: header if it's there
    					case 'from':
    						if ( strpos($content, '<' ) !== false ) {
    							// So... making my life hard again?
    							$from_name = substr( $content, 0, strpos( $content, '<' ) - 1 );
    							$from_name = str_replace( '"', '', $from_name );
    							$from_name = trim( $from_name );
    
    							$from_email = substr( $content, strpos( $content, '<' ) + 1 );
    							$from_email = str_replace( '>', '', $from_email );
    							$from_email = trim( $from_email );
    						} else {
    							$from_email = trim( $content );
    						}
    						break;
    					case 'content-type':
    						if ( strpos( $content, ';' ) !== false ) {
    							list( $type, $charset ) = explode( ';', $content );
    							$content_type = trim( $type );
    							if ( false !== stripos( $charset, 'charset=' ) ) {
    								$charset = trim( str_replace( array( 'charset=', '"' ), '', $charset ) );
    							} elseif ( false !== stripos( $charset, 'boundary=' ) ) {
    								$boundary = trim( str_replace( array( 'BOUNDARY=', 'boundary=', '"' ), '', $charset ) );
    								$charset = '';
    							}
    						} else {
    							$content_type = trim( $content );
    						}
    						break;
    					case 'cc':
    						$cc = array_merge( (array) $cc, explode( ',', $content ) );
    						break;
    					case 'bcc':
    						$bcc = array_merge( (array) $bcc, explode( ',', $content ) );
    						break;
    					default:
    						// Add it to our grand headers array
    						$headers[trim( $name )] = trim( $content );
    						break;
    				}
    			}
    		}
    	}
    
    	// Empty out the values that may be set
    	$phpmailer->ClearAllRecipients();
    	$phpmailer->ClearAttachments();
    	$phpmailer->ClearCustomHeaders();
    	$phpmailer->ClearReplyTos();
    
    	// From email and name
    	// If we don't have a name from the input headers
    	if ( !isset( $from_name ) )
    		$from_name = 'WordPress';
    
    	/* If we don't have an email from the input headers default to wordpress@$sitename
    	 * Some hosts will block outgoing mail from this address if it doesn't exist but
    	 * there's no easy alternative. Defaulting to admin_email might appear to be another
    	 * option but some hosts may refuse to relay mail from an unknown domain. See
    	 * http://trac.wordpress.org/ticket/5007.
    	 */
    
    	if ( !isset( $from_email ) ) {
    		// Get the site domain and get rid of www.
    		$sitename = strtolower( $_SERVER['SERVER_NAME'] );
    		if ( substr( $sitename, 0, 4 ) == 'www.' ) {
    			$sitename = substr( $sitename, 4 );
    		}
    
    		$from_email = 'wordpress@' . $sitename;
    	}
    
    	// Plugin authors can override the potentially troublesome default
    	$phpmailer->From     = apply_filters( 'wp_mail_from'     , $from_email );
    	$phpmailer->FromName = apply_filters( 'wp_mail_from_name', $from_name  );
    
    	// Set destination addresses
    	if ( !is_array( $to ) )
    		$to = explode( ',', $to );
    
    	foreach ( (array) $to as $recipient ) {
    		try {
    			// Break $recipient into name and address parts if in the format "Foo <bar@baz.com>"
    			$recipient_name = '';
    			if( preg_match( '/(.*)<(.+)>/', $recipient, $matches ) ) {
    				if ( count( $matches ) == 3 ) {
    					$recipient_name = $matches[1];
    					$recipient = $matches[2];
    				}
    			}
    			$phpmailer->AddAddress( $recipient, $recipient_name);
    		} catch ( phpmailerException $e ) {
    			continue;
    		}
    	}
    
    	// Set mail's subject and body
    	$phpmailer->Subject = $subject;
    	$phpmailer->Body    = $message;
    
    	// Add any CC and BCC recipients
    	if ( !empty( $cc ) ) {
    		foreach ( (array) $cc as $recipient ) {
    			try {
    				// Break $recipient into name and address parts if in the format "Foo <bar@baz.com>"
    				$recipient_name = '';
    				if( preg_match( '/(.*)<(.+)>/', $recipient, $matches ) ) {
    					if ( count( $matches ) == 3 ) {
    						$recipient_name = $matches[1];
    						$recipient = $matches[2];
    					}
    				}
    				$phpmailer->AddCc( $recipient, $recipient_name );
    			} catch ( phpmailerException $e ) {
    				continue;
    			}
    		}
    	}
    
    	if ( !empty( $bcc ) ) {
    		foreach ( (array) $bcc as $recipient) {
    			try {
    				// Break $recipient into name and address parts if in the format "Foo <bar@baz.com>"
    				$recipient_name = '';
    				if( preg_match( '/(.*)<(.+)>/', $recipient, $matches ) ) {
    					if ( count( $matches ) == 3 ) {
    						$recipient_name = $matches[1];
    						$recipient = $matches[2];
    					}
    				}
    				$phpmailer->AddBcc( $recipient, $recipient_name );
    			} catch ( phpmailerException $e ) {
    				continue;
    			}
    		}
    	}
    
    	// Set to use PHP's mail()
    	$phpmailer->IsMail();
    
    	// Set Content-Type and charset
    	// If we don't have a content-type from the input headers
    	if ( !isset( $content_type ) )
    		$content_type = 'text/plain';
    
    	$content_type = apply_filters( 'wp_mail_content_type', $content_type );
    
    	$phpmailer->ContentType = $content_type;
    
    	// Set whether it's plaintext, depending on $content_type
    	if ( 'text/html' == $content_type )
    		$phpmailer->IsHTML( true );
    
    	// If we don't have a charset from the input headers
    	if ( !isset( $charset ) )
    		$charset = get_bloginfo( 'charset' );
    
    	// Set the content-type and charset
    	$phpmailer->CharSet = apply_filters( 'wp_mail_charset', $charset );
    
    	// Set custom headers
    	if ( !empty( $headers ) ) {
    		foreach( (array) $headers as $name => $content ) {
    			$phpmailer->AddCustomHeader( sprintf( '%1$s: %2$s', $name, $content ) );
    		}
    
    		if ( false !== stripos( $content_type, 'multipart' ) && ! empty($boundary) )
    			$phpmailer->AddCustomHeader( sprintf( "Content-Type: %s;\n\t boundary=\"%s\"", $content_type, $boundary ) );
    	}
    
    	if ( !empty( $attachments ) ) {
    		foreach ( $attachments as $attachment ) {
    			
    			try {
    				$phpmailer->AddStringAttachment($attachment[0],$attachment[1],$attachment[2],$attachment[3]);
    			} catch ( phpmailerException $e ) {
    				continue;
    			}
    		}
    	}
    
    	do_action_ref_array( 'phpmailer_init', array( &$phpmailer ) );
    
    	// Send!
    	try {
    		return $phpmailer->Send();
    	} catch ( phpmailerException $e ) {
    		return false;
    	}
	} //mail

} //WooCommerce_Quick_Export_Automated_Class

} //if





 

 

