<?php

	
class pcfme_add_settings_page_class {
	
	
	
	private $billing_settings_key       = 'pcfme_billing_settings';
	private $shipping_settings_key      = 'pcfme_shipping_settings';
	private $additional_settings_key    = 'pcfme_additional_settings';
	private $extra_settings_key         = 'pcfme_extra_settings';
	private $pcfme_plugin_options       = 'pcfme_plugin_options';
    private $pcfme_pcfme_plugin_settings_tabs = array();	
	
	
	public function __construct() {
	    
		
		
	    add_action( 'init', array( &$this, 'load_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_billing_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_shipping_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_additional_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_extra_settings' ) );
		add_action( 'admin_menu', array( &$this, 'add_admin_menus' ) ,100);
		add_action( 'admin_enqueue_scripts', array(&$this, 'pcfme_register_admin_scripts'));
		add_action( 'wp_ajax_restore_billing_fields', array( &$this, 'restore_billing_fields' ) );
		add_action( 'wp_ajax_restore_shipping_fields', array( &$this, 'restore_shipping_fields' ) );
		
	}
	
	
	public function restore_billing_fields() {
	   delete_option( $this->billing_settings_key );
	   die();
	}
	
	public function restore_shipping_fields() {
	   delete_option( $this->shipping_settings_key );
	   die();
	}

	
	
	
	
	
	public function load_settings() {
		$this->billing_settings = (array) get_option( $this->billing_settings_key );
		
		$this->billing_settings = array_merge( array(
		), $this->billing_settings );
		
		$this->shipping_settings = (array) get_option( $this->shipping_settings_key );
		
		$this->shipping_settings = array_merge( array(
		), $this->shipping_settings );

		$this->additional_settings = (array) get_option( $this->additional_settings_key );
		
		$this->additional_settings = array_merge( array(
		), $this->additional_settings );
		
		$this->extra_settings = (array) get_option( $this->extra_settings_key );
		
		$this->extra_settings = array_merge( array(
		), $this->extra_settings );
		
		
	}
	
	

	
	/*
	 * registers admin scripts via admin enqueue scripts
	 */
	public function pcfme_register_admin_scripts($hook) {
	        global $billing_pcfmesettings_page;
			
			if( $hook != $billing_pcfmesettings_page ) 
		     return;
 
		 wp_enqueue_script( 'jquery-1.8.3', ''.pcfme_PLUGIN_URL.'assets/js/jquery-1.8.3.js');
		 wp_enqueue_script( 'pcfmeadmin', ''.pcfme_PLUGIN_URL.'assets/js/pcfmeadmin.js' );
		 wp_enqueue_script( 'chosen.jquery', ''.pcfme_PLUGIN_URL.'assets/js/chosen.jquery.js' );
		 
		 wp_enqueue_script( 'bootstrap-min', ''.pcfme_PLUGIN_URL.'assets/js/bootstrap-min.js' );
		 wp_enqueue_script( 'jquery-ui-sortable');
		 
         
		 wp_enqueue_style( 'pcfmeadmin', ''.pcfme_PLUGIN_URL.'assets/css/pcfmeadmin.css' );
		 wp_enqueue_style ( 'bootstrap',''.pcfme_PLUGIN_URL.'assets/css/bootstrap.css');
		 wp_enqueue_style( 'chosen.min', ''.pcfme_PLUGIN_URL.'assets/css/chosen.min.css' );

		 wp_enqueue_script( 'jquery-ui-datepicker' );
		 wp_enqueue_script( 'pcfme-frontend1', ''.pcfme_PLUGIN_URL.'assets/js/frontend1.js' );
		 wp_enqueue_style( 'jquery-ui', ''.pcfme_PLUGIN_URL.'assets/css/jquery-ui.css' );
		 wp_enqueue_style( 'pcfme-frontend', ''.pcfme_PLUGIN_URL.'assets/css/frontend.css' );
		
		 
		 $translation_array = array( 
		     'removealert' => __( 'Are you sure you want to delete?' ,'pcfme'),
		     'checkoutfieldtext' => __( 'billing_field_' ,'pcfme'),
		     'checkoutfieldtext2' => __( 'shipping_field_' ,'pcfme'),
		     'checkoutfieldtext3' => __( 'additional_field_' ,'pcfme'),
		     'checkoutfieldtext4' => __( 'billing field ' ,'pcfme'),
		     'checkoutfieldtext5' => __( 'shipping field ' ,'pcfme'),
		     'checkoutfieldtext6' => __( 'additional field ' ,'pcfme'),
		     'placeholder' => __( 'Search and Select ' ,'pcfme'),
		     'restorealert' => __( 'Restoring Default fields will undo all your Changes. Are you sure you want to do this ?' ,'pcfme')
		 );
                 wp_localize_script( 'pcfmeadmin', 'pcfmeadmin', $translation_array );
	}
	
	
	public function register_billing_settings() {
		$this->pcfme_plugin_settings_tabs[$this->billing_settings_key] = 'Billing Fields';
		
		register_setting( $this->billing_settings_key, $this->billing_settings_key );
		add_settings_section( 'pcfme_section_billing', '', '', $this->billing_settings_key );
		add_settings_field( 'pcfme_billing_option', '', array( &$this, 'pcfme_field_billing_option' ), $this->billing_settings_key, 'pcfme_section_billing' );
	}
	
	public function register_shipping_settings() {
		$this->pcfme_plugin_settings_tabs[$this->shipping_settings_key] = 'Shipping Fields';
		
		register_setting( $this->shipping_settings_key, $this->shipping_settings_key );
		add_settings_section( 'pcfme_section_shipping', '', '', $this->shipping_settings_key );
		add_settings_field( 'pcfme_shipping_option', '', array( &$this, 'pcfme_field_shipping_option' ), $this->shipping_settings_key, 'pcfme_section_shipping' );
	}


	public function register_additional_settings() {
		$this->pcfme_plugin_settings_tabs[$this->additional_settings_key] = 'Additional Fields';
		
		register_setting( $this->additional_settings_key, $this->additional_settings_key );
		add_settings_section( 'pcfme_section_additional', '', '', $this->additional_settings_key );
		add_settings_field( 'pcfme_additional_option', '', array( &$this, 'pcfme_field_additional_option' ), $this->additional_settings_key, 'pcfme_section_additional' );
	}
	
	public function register_extra_settings() {
		$this->pcfme_plugin_settings_tabs[$this->extra_settings_key] = 'Settings';
		
		register_setting( $this->extra_settings_key, $this->extra_settings_key );
		add_settings_section( 'pcfme_section_extra', '', '', $this->extra_settings_key );
		add_settings_field( 'pcfme_extra_option', '', array( &$this, 'pcfme_field_extra_option' ), $this->extra_settings_key, 'pcfme_section_extra' );
	}
	


	

	
	
	public function pcfme_field_billing_option() {
	    
		include ('forms/pcfme_admin_billing_fields_form.php');
  
		
	}
	
	public function pcfme_field_shipping_option() { 
	     
       include ('forms/pcfme_admin_shipping_fields_form.php');
		 
		 
	 }

	 public function pcfme_field_additional_option() { 
	     
       include ('forms/pcfme_admin_additional_fields_form.php');
		 
		 
	 }
	 
	 public function pcfme_field_extra_option() { 
	     
       include ('forms/pcfme_admin_extra_fields_form.php');
		 
		 
	 }
	
	
	
	

	public function add_admin_menus() {
	   global $billing_pcfmesettings_page;
	    $billing_pcfmesettings_page = add_submenu_page( 'woocommerce', __('Checkout fields'), __('Checkout fields'), 'manage_woocommerce', $this->pcfme_plugin_options, array(&$this, 'plugin_options_page'));
	}
	
	
	public function plugin_options_page() {
	    global $woocommerce;
		$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->billing_settings_key;
		global $billing_fields;
		$billing_fields = '';
		?>
		<div class="wrap">
		    <?php $this->plugin_options_tabs(); ?>
		
			<form method="post" class="<?php echo $tab; ?>" action="options.php">
				<?php wp_nonce_field( 'update-options' ); ?>
				<?php settings_fields( $tab ); ?>
				<?php do_settings_sections( $tab ); ?>
				
				
				<br />
				<center><input type="submit" name="submit" id="submit" class="btn btn-success" value="<?php _e('Save Changes','pcfme'); ?>"></center>
				
			    
			</form>
			<br />
			
		</div>
		<div id="responsediv" style="display:none; font-size:20px; background-color:#ffffe0; border-color: #e6db55;  border-width: 1px; border-style: solid; width :auto; height:30px;">
		</div>
		<?php
	}
    public function display_field_label($key,$field) {
		    if (isset($field['label'])) { 
			         $label = $field['label'];
			    }  else {
					switch ($key) {
                        case "billing_address_1":
						case "shipping_address_1":
                          $label = __('Address','pcfme');
                        break;
                        case "billing_address_2":
						case "shipping_address_2":
                          $label = "";
                        break;
                        
						case "billing_city":
						case "shipping_city":
                          $label = __('Town / City','pcfme');
                        break;
						
						case "billing_state":
						case "shipping_state":
                          $label = __('State / County','pcfme');
                        break;
						
						case "billing_postcode":
						case "shipping_postcode":
                          $label = __('Postcode / Zip','pcfme');
                        break;

                        default:
                          $label = $key;
                        }
				}
			return $label;
	}

	public function display_visual_preview($key,$field) { 
     global $woocommerce;
     
		?>
	 
	  <td width="30%">

	    <label class="">
		  <?php

          $label = $this->display_field_label($key,$field);

          switch ($key) {
              case "additional_field_270":
                  $label = __('Time delivery interval','pcfme');
                  break;
              case "additional_field_98":
                  $label = __('Delivery date','pcfme');
                  break;
              case "additional_field_655":
                  $label = __('Street','pcfme');
                  break;
              case "additional_field_926":
                  $label = __('House','pcfme');
                  break;
              case "additional_field_533":
                  $label = __('Apartment','pcfme');
                  break;
              case "additional_field_394":
                  $label = __('City','pcfme');
                  break;
              case "additional_field_30":
                  $label = __('Full Name','pcfme');
                  break;
              case "additional_field_154":
                  $label = __('Receipient phone number','pcfme');
                  break;
          }

          echo $label;

		  if (isset($field['required']) && ($field['required'] == 1)) { ?>
          &nbsp;&nbsp;<span class="pcmfe-required" title="required">*</span>
          <?php } ?>
	    </label>
        
      </td>
	  <td width="30%">
	  	<?php pcmfe_admin_form_field($key,$field); ?>
	  </td>
	 
	<?php }
	
	
	public function show_fields_form($key,$field,$noticerowno,$slug,$required_slugs,$core_fields,$country_fields,$address2_field) { ?>
	      <?php
		    
               if (isset($field['width'])) {
                 $fieldwidth= $field['width'];
               	 
               } elseif (isset($field['class'])) {
                  
               	
               	  foreach($field['class'] as $class) {
               	  	if (isset($class)) {
                    switch($class) {
                    	case "form-row-first":
                          $fieldwidth='form-row-first';
						break;

                    	case "form-row-last":
                          $fieldwidth='form-row-last';
						break;

                    	default:
                    	 $fieldwidth='form-row-wide';

                    }
                }
               	  } 
               }
	    $extrasettings=$this->extra_settings;
		
		if (isset($extrasettings['include_variation'])) {
			$post_type_array = array('product', 'product_variation');
		} else {
			$post_type_array = array('product');
		}
		
        $postargs = array(
	      'posts_per_page'           => -1,
	      'post_type'                => $post_type_array,
		  'fields'                   => array('ID', 'post_title')
	     );
     	 
        $catargs = array(
	      'orderby'                  => 'name',
	      'taxonomy'                 => 'product_cat',
	      'hide_empty'               => 0
	     );
		 
		$posts_array          = get_posts( $postargs );   
		$categories           = get_categories( $catargs );  

        if (!empty($field['products'])) {
		       $chosenproducts = implode(',', $field['products']); 
		    } else { 
			   $chosenproducts=''; 
			}
		if (!empty($field['category'])) {
		       $chosencategories = implode(',', $field['category']); 
		    } else { 
			   $chosencategories=''; 
			}
			 
        switch($slug) {
		
		  case "pcfme_billing_settings":
		    $headlingtext=''.__('billing_field_','pcfme').''.$noticerowno.'';
		    break;
	
          case "pcfme_shipping_settings":
		    $headlingtext=''.__('shipping_field_','pcfme').''.$noticerowno.'';
		    break;

		   case "pcfme_additional_settings":
		     $headlingtext=''.__('additional_field_','pcfme').''.$noticerowno.'';
		     break;
		
		
	       } ?>   

       
	   <div class="panel-group panel panel-default pcfme_list_item <?php if (isset($field['hide']) && ($field['hide'] == 1)) { echo "pcfme-hidden-row";} ?>" id="pcfme_list_items_<?php echo $noticerowno; ?>" style="width:90%; display:block;">
           <div class="panel-heading"> 
		
	     <table class="heading-table">
			<tr>
			    <td width="20%">
			     <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#pcfme<?php echo $noticerowno; ?>">
                  <span class="glyphicon glyphicon-edit pull-left"></span>
			     </a>
			    </td>
			    
				<?php $this->display_visual_preview($key,$field); ?>
			 
			    <td width="20%">
		         <?php if (!preg_match('/\b'.$key.'\b/', $core_fields )) { ?>    
		           <span class="glyphicon glyphicon-remove-circle pull-right "></span>
		         <?php } ?>
			    </td>
                </tr>
		  </table>
           </div>
           <div id="pcfme<?php echo $noticerowno; ?>" class="panel-collapse collapse">

		     <table class="table"> 
			 
			 <?php if (preg_match('/\b'.$key.'\b/', $core_fields )) { ?>    
		       <tr>
                <td><label for="<?php echo $key; ?>_hide"><?php  _e('Hide/Remove This Core Field','pcfme'); ?></label></td>
                <td><input type="checkbox" name="<?php echo $slug; ?>[<?php echo $key; ?>][hide]" <?php if (isset($field['hide']) && ($field['hide'] == 1)) { echo "checked";} ?> value="1"></td>
			   </tr>
		     <?php }  ?>

		     <tr>
			   <td><label for="<?php echo $key; ?>_type"><?php _e('Field Key','pcfme'); ?></label></td>
			   <td><span claas="wsp_field_key"><?php echo $key; ?></span></td>
		     </tr> 

			 <?php if (!preg_match('/\b'.$key.'\b/', $country_fields )) { ?>   
		       <tr>
	           <td><label for="<?php echo $key; ?>_type"><?php _e('Field Type','pcfme'); ?></label></td>
		       <td>
		          <select class="checkout_field_type" name="<?php echo $slug; ?>[<?php echo $key; ?>][type]" >
			        <option value="text" <?php if (isset($field['type']) && ($field['type'] == "text")) { echo "selected";} ?> ><?php _e('Text','pcfme'); ?></option>
			        <option value="textarea" <?php if (isset($field['type']) && ($field['type'] == "textarea")) { echo "selected";} ?>><?php _e('Textarea','pcfme'); ?></option>
			        <option value="password" <?php if (isset($field['type']) && ($field['type'] == "password")) { echo "selected";} ?>><?php _e('Password','pcfme'); ?></option>
			        <option value="checkbox" <?php if (isset($field['type']) && ($field['type'] == "checkbox")) { echo "selected";} ?>><?php _e('Checkbox','pcfme'); ?></option>
			        <option value="pcfmeselect" <?php if (isset($field['type']) && ($field['type'] == "pcfmeselect")) { echo "selected";} ?>><?php _e('Select','pcfme'); ?></option>
					<option value="multiselect" <?php if (isset($field['type']) && ($field['type'] == "multiselect")) { echo "selected";} ?>><?php _e('multiselect','pcfme'); ?></option>
			        <option value="radio" <?php if (isset($field['type']) && ($field['type'] == "radio")) { echo "selected";} ?>><?php _e('Radio Select','pcfme'); ?></option>
			        <option value="datepicker" <?php if (isset($field['type']) && ($field['type'] == "datepicker")) { echo "selected";} ?>><?php _e('Date Picker','pcfme'); ?></option>
			       </select>
		       </td>
	           </tr>
               <?php }  ?>
               
               <?php if (!preg_match('/\b'.$key.'\b/', $address2_field )) { ?>
			   <tr>
                <td><label for="<?php echo $key; ?>_label"><?php  _e('Label','pcfme'); ?></label></td>
	            <td><input type="text" name="<?php echo $slug; ?>[<?php echo $key; ?>][label]" value="<?php if (isset($field['label']) && ($field['label'] != '')) { echo $field['label']; } else { echo $headlingtext;} ?>" size="35"></td>
               </tr>
			   <?php }  ?>
			
			   
			   
			   <tr>
	           <td><label for="<?php echo $key; ?>_width"><?php _e('Width','pcfme'); ?></label></td>
		       <td>
		       <select class="checkout_field_width" name="<?php echo $slug; ?>[<?php echo $key; ?>][width]" >
			    
				<option value="form-row-wide" <?php if (isset($fieldwidth) && ($fieldwidth == "form-row-wide" )) { echo 'selected'; } ?>><?php _e('Full Width','pcfme'); ?></option>
			    <option value="form-row-first" <?php if (isset($fieldwidth) && ($fieldwidth == "form-row-first" )) { echo 'selected'; } ?>><?php _e('First Half','pcfme'); ?></option>
			    <option value="form-row-last" <?php if (isset($fieldwidth) && ($fieldwidth == "form-row-last" )) { echo 'selected'; } ?>><?php _e('Second Half','pcfme'); ?></option>
				
				
			   </select>
		       </td>
	           </tr>
			   
			   <?php if (!preg_match('/\b'.$key.'\b/', $required_slugs )) { ?>
		       <tr>
                <td><label for="<?php echo $key; ?>_required"><?php  _e('Required','pcfme'); ?></label></td>
                <td><input type="checkbox" name="<?php echo $slug; ?>[<?php echo $key; ?>][required]" <?php if (isset($field['required']) && ($field['required'] == 1)) { echo "checked";} ?> value="1"></td>
			   </tr>
			   <?php } ?>
			   
			   <tr>
                <td><label for="<?php echo $key; ?>_clear"><?php  _e('Clearfix','pcfme'); ?></label></td>
                <td><input type="checkbox" name="<?php echo $slug; ?>[<?php echo $key; ?>][clear]" <?php if (isset($field['clear']) && ($field['clear'] == 1)) { echo "checked";} ?> value="1"></td>
			   </tr>
			   
			   
			   <tr>
                <td><label for="<?php echo $key; ?>_label"><?php  _e('Placeholder ','pcfme'); ?></label></td>
	            <td><input type="text" name="<?php echo $slug; ?>[<?php echo $key; ?>][placeholder]" value="<?php if (isset($field['placeholder'])) { echo $field['placeholder']; } ?>" size="35"></td>
               </tr>
			   
			   
			   <tr class="add-field-options" style="">
	           <td>
		         <label for="<?php echo $key; ?>_options"><?php _e('Options','pcfme'); ?></label>
		       </td>
		       <td>
		       <input type="text" name="<?php echo $slug; ?>[<?php echo $key; ?>][options]" placeholder="<?php _e('Separated by comma. For Example: option1,option2','pcfme'); ?>" value="<?php if (isset($field['options'])) { echo $field['options']; } ?>" size="35">
		       </td>
	           </tr>
			   
		
			   
			   
			   <?php 
			   $validatearray='';
			   if (isset($field['validate'])) {
			        foreach ($field['validate'] as $z=>$value) {
			          $validatearray.=''.$value.',';
			        } 
			       
				   $validatearray=substr_replace($validatearray, "", -1);
			   }
			  
			   
			   ?>
			   <tr>
                <td><label><?php  _e('Visibility','pcfme'); ?></label></td>
	            <td>
		           <select class="checkout_field_visibility" name="<?php echo $slug; ?>[<?php echo $key; ?>][visibility]" >
		             <option value="always-visible" <?php if (isset($field['visibility']) && ($field['visibility'] == "always-visible" )) { echo 'selected'; } ?>><?php _e('Always Visibile','pcfme'); ?></option>
					 <option value="product-specific" <?php if (isset($field['visibility']) && ($field['visibility'] == "product-specific" )) { echo 'selected'; } ?>><?php _e('Conditional - Product Specific','pcfme'); ?></option>
					 <option value="category-specific" <?php if (isset($field['visibility']) && ($field['visibility'] == "category-specific" )) { echo 'selected'; } ?>><?php _e('Conditional - Category Specific','pcfme'); ?></option>
			       </select>
		        </td>
	           </tr>
			   
			  <tr class="checkout_field_products_tr" style="<?php if (isset($field['visibility']) && ($field['visibility'] == "product-specific" )) { echo "display:;"; } else { echo 'display:none;'; } ?>">
			   <td>
                 <label><?php echo __('Select Products','pcfme'); ?></label>
	           </td>
			   <td>
			    <select class="checkout_field_products" data-placeholder="<?php _e('Choose Products','pcfme'); ?>" name="<?php echo $slug; ?>[<?php echo $key; ?>][products][]" multiple  style="width:600px">
                 <?php foreach ($posts_array as $post) { ?>
				 <option value="<?php echo $post->ID; ?>" <?php if (preg_match('/\b'.$post->ID.'\b/', $chosenproducts )) { echo 'selected';}?>>#<?php echo $post->ID; ?>- <?php echo $post->post_title; ?></option>
				 <?php } ?>
                </select>
               </td>
			   </tr>
			   <tr class="checkout_field_category_tr" style="<?php if (isset($field['visibility']) && ($field['visibility'] == "category-specific" )) { echo "display:;"; } else { echo 'display:none;'; } ?>" >
			    <td>
                 <label for="notice_category"><span class="pcfmeformfield"><?php echo __('Select Categories','pcfme'); ?></span></label>
	            </td>
			    <td>
			    <select class="checkout_field_category" data-placeholder="<?php _e('Choose Categories','pcfme'); ?>" name="<?php echo $slug; ?>[<?php echo $key; ?>][category][]"  multiple style="width:600px">
                 <?php foreach ($categories as $category) { ?>
				 <option value="<?php echo $category->term_id; ?>" <?php if (preg_match('/\b'.$category->term_id.'\b/', $chosencategories )) { echo 'selected';}?>>#<?php echo $category->term_id; ?>- <?php echo $category->name; ?></option>
				 <?php } ?>
                 </select>
                </td>
			    </tr>
			   
			   <tr>
                <td><label for="<?php echo $key; ?>_label"><?php  _e('Validate','pcfme'); ?></label></td>
	            <td>
		           <select name="<?php echo $slug; ?>[<?php echo $key; ?>][validate][]" class="row-validate-multiselect" multiple>
			         <option value="state" <?php if (preg_match('/\bstate\b/', $validatearray )) { echo 'selected'; } ?>><?php _e('state','pcfme'); ?></option>
			         <option value="postcode" <?php if (preg_match('/\bpostcode\b/', $validatearray )) { echo 'selected'; } ?>><?php _e('postcode','pcfme'); ?></option>
			         <option value="email" <?php if (preg_match('/\bemail\b/', $validatearray )) { echo 'selected'; } ?>><?php _e('email','pcfme'); ?></option>
			         <option value="phone" <?php if (preg_match('/\bphone\b/', $validatearray )) { echo 'selected'; } ?>><?php _e('phone','pcfme'); ?></option>
			       </select>
		        </td>
	           </tr>
			   
			   <tr>
			     <td width="40%"><label for="<?php echo $key; ?>_clear"><?php  _e('Chose Options','pcfme'); ?></label></td>
			     <td  width="60%">
			      <table>
			       
			   
			        <tr class="disable_datepicker_tr" style="<?php if (isset($field['type']) && ($field['type'] == "datepicker")) { echo "display:;";} else { echo "display:none;"; } ?>">
                     <td><input class="checkout_field_disable_past_dates" type="checkbox" name="<?php echo $slug; ?>[<?php echo $key; ?>][disable_past]" <?php if (isset($field['disable_past']) && ($field['disable_past'] == 1)) { echo "checked";} ?> value="1"></td>
			         <td><label ><?php  _e('Disable Past Date Selection In Datepicker','pcfme'); ?></label></td>
					</tr>
					
					<tr>
			        <td><input type="checkbox" name="<?php echo $slug; ?>[<?php echo $key; ?>][orderedition]" <?php if (isset($field['orderedition']) && ($field['orderedition'] == 1)) { echo "checked";} ?> value="1"></td>
                    <td><label for="<?php echo $key; ?>_clear"><?php  _e('Show field detail on order edition page','pcfme'); ?></label></td>
                    </tr>
					
					<tr>
			        <td><input type="checkbox" name="<?php echo $slug; ?>[<?php echo $key; ?>][emailfields]" <?php if (isset($field['emailfields']) && ($field['emailfields'] == 1)) { echo "checked";} ?> value="1"></td>
                    <td><label><?php  _e('Show field detail on woocommerce order email','pcfme'); ?></label></td>
                    </tr>
			      
			        </table>
				   </td>
				 </tr>
			   </table>

             </div>
			
          </div>
	<?php }
	
	
	

	

	public function plugin_options_tabs() {
		$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->billing_settings_key;
        echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->pcfme_plugin_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->pcfme_plugin_options . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';	
		}
		echo '</h2>';
	}
	
	public function show_new_form() {
		
        $extrasettings=$this->extra_settings;
		
		if (isset($extrasettings['include_variation'])) {
			$post_type_array = array('product', 'product_variation');
		} else {
			$post_type_array = array('product');
		}
		
	     $postargs = array(
	      'posts_per_page'           => -1,
	      'post_type'                => $post_type_array,
		  'fields'                   => array('ID', 'post_title')
	     );
     	 
        $catargs = array(
	      'orderby'                  => 'name',
	      'taxonomy'                 => 'product_cat',
	      'hide_empty'               => 0
	     );
		 
		$posts_array          = get_posts( $postargs );   
		$categories           = get_categories( $catargs ); 
		
	     include ('forms/pcfme_show_new_form.php');
    }
	

}




new pcfme_add_settings_page_class();

?>