<?php
   $extrasettings=$this->extra_settings;
?>
<table class="table">
	 
	
	  <tr>
                        <th scope="row"><?php _e('Envato Item Purchase Code','pcfme'); ?></th>
                        <td>
                            <input type="text" id="purchase_code" name="<?php echo $this->extra_settings_key; ?>[purchase_code]" class="purchase_code" value="<?php if (isset($extrasettings['purchase_code'])) { echo $extrasettings['purchase_code']; }  ?>" size="60"> 
							<a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Can-I-Find-my-Purchase-Code-"><?php _e('How to find it ?','pcfme'); ?></a>
                            <p><?php _e('Valid envato item purchase code is required to recieve automatic updates.','pcfme'); ?></p>
						</td>
      </tr>
	  
	  <tr>
                        <th scope="row"><?php _e('Include variations in chose products multiselect','pcfme'); ?></th>
                        <td>
                            <input type="checkbox" name="<?php echo $this->extra_settings_key; ?>[include_variation]" class="include_variation" <?php if (isset($extrasettings['include_variation'])) { echo "checked"; }  ?> value="1"> 
							
						</td>
      </tr>
	  
</table>