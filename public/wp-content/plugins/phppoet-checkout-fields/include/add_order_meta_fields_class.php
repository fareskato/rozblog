<?php
class pcfme_add_order_meta_class {
     
	 
	 private $billing_settings_key = 'pcfme_billing_settings';
	 private $shipping_settings_key = 'pcfme_shipping_settings';
	 private $additional_settings_key = 'pcfme_additional_settings';
     
	 public function __construct() {
	 
	      
	      add_filter('woocommerce_checkout_update_order_meta', array( &$this, 'update_order_meta' ) );
	      add_filter('woocommerce_admin_order_data_after_billing_address', array( &$this, 'data_after_billing_address' ) );
	      add_filter('woocommerce_email_order_meta', array( &$this, 'woocommerce_custom_new_order_templace' )  );
	      
	 }
	 
	 
	 	 public function update_order_meta($order_id) {
		   
		   $billing_fields      = (array) get_option( $this->billing_settings_key );
		   $shipping_fields     = (array) get_option( $this->shipping_settings_key );
		   $additional_fields   = (array) get_option( $this->additional_settings_key );
	       
		   
		   
		     foreach ($billing_fields as $billingkey=>$billing_field) {
			   
				   if ((isset($billing_field['orderedition'])) || (isset($billing_field['emailfields']))) {
				     if ( ! empty( $_POST[$billingkey] ) ) {
						 
						if (is_array($_POST[$billingkey]))  {
							$billingkeyvalue = implode(',', $_POST[$billingkey]);
						} else {
							$billingkeyvalue = $_POST[$billingkey];
						}
						 
                        update_post_meta( $order_id, $billingkey, sanitize_text_field( $billingkeyvalue ) );
                       } 
				   }
				
			 }
		   
		   
		   
		   
		     foreach ($shipping_fields as $shippingkey=>$shipping_field) {
			    
				   if ((isset($shipping_field['orderedition'])) || (isset($shipping_field['emailfields']))) {
				     if ( ! empty( $_POST[$shippingkey] ) ) {
						 
						if (is_array($_POST[$shippingkey]))  {
							$shippingkeyvalue = implode(',', $_POST[$shippingkey]);
						} else {
							$shippingkeyvalue = $_POST[$shippingkey];
						}
						
                        update_post_meta( $order_id, $shippingkey, sanitize_text_field( $shippingkeyvalue ) );
                       } 
				   }
				
			 }
		   

		   foreach ($additional_fields as $additionalkey=>$additional_field) {
		   	    if ((isset($additional_field['orderedition'])) || (isset($additional_field['emailfields']))) {
				     if ( ! empty( $_POST[$additionalkey] ) ) {
						 
						if (is_array($_POST[$additionalkey]))  {
							$additionalkeyvalue = implode(',', $_POST[$additionalkey]);
						} else {
							$additionalkeyvalue = $_POST[$additionalkey];
						}
						
                        update_post_meta( $order_id, $additionalkey, sanitize_text_field( $additionalkeyvalue ) );
                       } 
				   }
		   }
		   
		   
	       
	 }
	 
	 	 public function data_after_billing_address($order)  {
	       global $woocommerce;
	      
		   
		   
		   $billing_fields      = (array) get_option( $this->billing_settings_key );
		   $shipping_fields     = (array) get_option( $this->shipping_settings_key );
           $additional_fields   = (array) get_option( $this->additional_settings_key );
		   
		   
		  
		     foreach ($billing_fields as $billingkey=>$billing_field) {
			    
				  if (isset($billing_field['orderedition'])) {
					 
					 $billingkeyvalue = get_post_meta( $order->id, $billingkey, true );
				     if ( ! empty( $billingkeyvalue ) ) {
						 echo '<p><strong>'.__(''.$billing_field['label'].'').':</strong> ' . $billingkeyvalue . '</p>';
                     }						 
					 
				   }
				
			 }
		   
		   
		   
		     foreach ($shipping_fields as $shippingkey=>$shipping_field) {
			    
				   if (isset($shipping_field['orderedition'])) {
					  
					 $shippingkeyvalue = get_post_meta( $order->id, $shippingkey, true );
					 
					  if ( ! empty( $shippingkeyvalue ) ) {
						  echo '<p><strong>'.__(''.$shipping_field['label'].'').':</strong> ' . $shippingkeyvalue . '</p>';
					  }
				     
				   }
				
			 }
		   

		    foreach ($additional_fields as $additionalkey=>$additional_field) {
		   	    if (isset($additional_field['orderedition'])) {
					
					$additionalkeyvalue = get_post_meta( $order->id, $additionalkey, true );
				    
					if ( ! empty( $additionalkeyvalue ) ) {
					   echo '<p><strong>'.__(''.$additional_field['label'].'').':</strong> ' . $additionalkeyvalue . '</p>';
					}
					
					
                 }
		   }
	       
	 }
	 
	 public function woocommerce_custom_new_order_templace ($order) {
          
		   $billing_fields                = (array) get_option( $this->billing_settings_key );
		   $shipping_fields               = (array) get_option( $this->shipping_settings_key );
		   $additional_fields             = (array) get_option( $this->additional_settings_key );
		   
	
		   
		     foreach ($billing_fields as $billingkey=>$billing_field) {
			    
				   if (isset($billing_field['emailfields'])) {
					  
					  
						   $billingkeyvalue = get_post_meta( $order->id, $billingkey, true );
					  
				        if ( ! empty( $billingkeyvalue ) ) {
				           echo '<br /><strong>'.$billing_field['label'].'</strong> : '.$billingkeyvalue.'<br />';
						}	
				      
                    }
				
			 }
		   
		   
		   
		   
		     foreach ($shipping_fields as $shippingkey=>$shipping_field) {
			    
				   if (isset($shipping_field['emailfields'])) {
					  
					   
						   $shippingkeyvalue = get_post_meta( $order->id, $shippingkey, true );
					  
				        if ( ! empty( $shippingkeyvalue ) ) {
				           echo '<br /><strong>'.$shipping_field['label'].'</strong> : '.$shippingkeyvalue.'<br />';
					    }
                    }  					
				      
                    
				
			 }
		   

		   foreach ($additional_fields as $additionalkey=>$additional_field) {
              if (isset($additional_field['emailfields'])) {
					  
					        $additionalkeyvalue = get_post_meta( $order->id, $additionalkey, true );
					
				        if ( ! empty( $additionalkeyvalue ) ) {
				            echo '<br /><strong>'.$additional_field['label'].'</strong> : '.$additionalkeyvalue.'<br />';
						}	
				      
                    }

		   }
	 }
	 
}

new pcfme_add_order_meta_class();
?>