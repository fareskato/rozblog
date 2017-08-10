           <div class="panel-group panel panel-default checkoutfield pcfme_list_item" style="width:90%; display:none;">
           <div class="panel-heading">

           <table class="heading-table">
			<tr>
			    <td width="20%">
			     <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="">
                  <span class="glyphicon glyphicon-edit pull-left"></span>
			     </a>
			    </td>
			    
				<td width="30%">
	              <label  class="new-field-label"></label>
        
                </td>
	            <td width="30%">
	  	          <input type="text" placeholder="<?php _e('Placeholder Text','pcfme'); ?>">
	            </td>
			 
			    <td width="20%">
		          <span class="glyphicon glyphicon-remove-circle pull-right "></span>
		         </td>
                </tr>
		    </table>

           </div>
           <div id="" class="panel-collapse collapse">
			  <table class="table"> 
			   
			 
			   
			   <tr>
	           <td><label><?php _e('Field Type','pcfme'); ?></label></td>
		       <td>
		          <select class="checkout_field_type" name="" >
			        <option value="text"  ><?php _e('Text','pcfme'); ?></option>
			        <option value="textarea" ><?php _e('Textarea','pcfme'); ?></option>
			        <option value="password" ><?php _e('Password','pcfme'); ?></option>
			        <option value="checkbox" ><?php _e('Checkbox','pcfme'); ?></option>
			        <option value="pcfmeselect" ><?php _e('Select','pcfme'); ?></option>
			        <option value="multiselect"><?php _e('Multi Select','pcfme'); ?></option>
			        <option value="radio" ><?php _e('Radio Select','pcfme'); ?></option>
			        <option value="datepicker" ><?php _e('Date Picker','pcfme'); ?></option>
					
			       </select>
		       </td>
	           </tr>
			   <tr>
                <td><label><?php  _e('Label','pcfme'); ?></label></td>
	            <td><input type="text" class="checkout_field_label" name="" value="" size="35"></td>
               </tr>
			   
			
			   
			   <tr>
	           <td><label><?php _e('Class','pcfme'); ?></label></td>
		       <td>
		       <select class="checkout_field_width" name="">
			    
				<option value="form-row-wide" ><?php _e('Full Width','pcfme'); ?></option>
			    <option value="form-row-first" ><?php _e('First Half','pcfme'); ?></option>
			    <option value="form-row-last" ><?php _e('Second Half','pcfme'); ?></option>
				
				
			     
			   </select>
		       </td>
	           </tr>
			   
			   
		       <tr>
                <td><label ><?php  _e('Required','pcfme'); ?></label></td>
                <td><input class="checkout_field_required" type="checkbox" name=""  value="1"></td>
			   </tr>
			   
			   
			   <tr>
                <td><label><?php  _e('Clearfix','pcfme'); ?></label></td>
                <td><input class="checkout_field_clear" type="checkbox" name="" value="1"></td>
			   </tr>
			   
			   
			   <tr>
                <td><label><?php  _e('Placeholder ','pcfme'); ?></label></td>
	            <td><input type="text" class="checkout_field_placeholder" name="" value="" size="35"></td>
               </tr>
			   
			   
			   <tr class="add-field-options" style="">
	           <td>
		         <label><?php _e('Options','pcfme'); ?></label>
		       </td>
		       <td>
		       <input type="text" class="checkout_field_options" name="" placeholder="<?php _e('Separated by comma. For Example: option1,option2','pcfme'); ?>" value="" size="35">
		       </td>
	           </tr>
			   
			 
			   
			   <tr>
                <td><label><?php  _e('Visibility','pcfme'); ?></label></td>
	            <td>
		           <select class="checkout_field_visibility" name="" >
		             <option value="always-visible"><?php _e('Always Visibile','pcfme'); ?></option>
					 <option value="product-specific"><?php _e('Conditional - Product Specific','pcfme'); ?></option>
					 <option value="category-specific"><?php _e('Conditional - Category Specific','pcfme'); ?></option>
			       </select>
		        </td>
	           </tr>
			   
			   <tr class="checkout_field_products_tr" style="display:none;">
			    <td>
                 <label><?php echo __('Select Products','pcfme'); ?></label>
	            </td>
			    <td>
			     <select class="checkout_field_products" data-placeholder="<?php _e('Choose Products','pcfme'); ?>" name="" multiple  style="width:600px">
                  <?php foreach ($posts_array as $post) { ?>
				  <option value="<?php echo $post->ID; ?>">#<?php echo $post->ID; ?>- <?php echo $post->post_title; ?></option>
				  <?php } ?>
                 </select>
                </td>
			   </tr>
			   <tr class="checkout_field_category_tr" style="display:none;" >
			    <td>
                 <label for="notice_category"><?php echo __('Select Categories','pcfme'); ?></label>
	            </td>
			    <td>
			     <select class="checkout_field_category" data-placeholder="<?php _e('Choose Categories','pcfme'); ?>" name=""  multiple style="width:600px">
                  <?php foreach ($categories as $category) { ?>
				  <option value="<?php echo $category->term_id; ?>">#<?php echo $category->term_id; ?>- <?php echo $category->name; ?></option>
				  <?php } ?>
                 </select>
                </td>
			    </tr>
			   
			   <tr>
                <td><label><?php  _e('Validate','pcfme'); ?></label></td>
	            <td>
		           <select name="" class="checkout_field_validate" multiple>
			         <option value="state" ><?php _e('state','pcfme'); ?></option>
			         <option value="postcode" ><?php _e('postcode','pcfme'); ?></option>
			         <option value="email" ><?php _e('email','pcfme'); ?></option>
			         <option value="phone" ><?php _e('phone','pcfme'); ?></option>
			       </select>
		       </td>
	 
               </tr>
			      <tr>
			     <td width="40%"><label for="<?php echo $key; ?>_clear"><?php  _e('Chose Options','pcfme'); ?></label></td>
			     <td  width="60%">
			      <table>
			        
			   
			        <tr class="disable_datepicker_tr" style="display:none;">
                        <td><input class="checkout_field_disable_past_dates" type="checkbox" name=""  value="1"></td>
						<td><label ><?php  _e('Disable Past Date Selection In Datepicker','pcfme'); ?></label></td>
			        </tr>
					
					<tr>
                       <td><input class="checkout_field_orderedition" type="checkbox" name=""  value="1"></td>
					   <td><label ><?php  _e('Show field detail on order edition page','pcfme'); ?></label></td>
			        </tr>
					
					<tr>
                       <td><input class="checkout_field_emailfields" type="checkbox" name=""  value="1"></td>
					   <td><label ><?php  _e('Show field detail on woocommerce order emails','pcfme'); ?></label></td>
			        </tr>
			      
			        </table>
				   </td>
				 </tr>
			 
			   
			   </table>
		   </div>
        </div>