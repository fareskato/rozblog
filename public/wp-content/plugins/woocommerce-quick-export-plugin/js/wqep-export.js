/**
 * Main JS file for WooCommerce Quick Export Plugin
 */

(function ($) {
    "use strict";

    $(document).ready(function(){
    

		$('#qwep_order_meta a').on('click', function(){
			
			var data = {
				'action': 'qwep_keys',
				'type': 'qwep_order_meta'
			};
			
			$('#qwep_order_meta').html('Loading...');
			
			$.post(ajaxurl, data, function(response) {
				if(response == '' || response.indexOf('Fatal error') >= 0)
				{
					var data2 = {
						'action': 'qwep_keys_query',
						'type': 'qwep_order_meta'
					};
			
					$.post(ajaxurl, data2, function(response) {
						$('#qwep_order_meta').html(response);
					});
				}
				else
				{
					$('#qwep_order_meta').html(response);
				}
			});
			
			return false;
		});
	
		$('#qwep_post_meta a').on('click', function(){
		
			var data = {
				'action': 'qwep_keys',
				'type': 'qwep_post_meta'
			};
			
			$('#qwep_post_meta').html('Loading...');
			
			$.post(ajaxurl, data, function(response) {
				$('#qwep_post_meta').html(response);
			});
			
			return false;
	
		});
	
		$('#qwep_taxo a').on('click', function(){
		
			var data = {
				'action': 'qwep_keys',
				'type': 'qwep_taxo'
			};
			
			$('#qwep_taxo').html('Loading...');
			
			$.post(ajaxurl, data, function(response) {
				$('#qwep_taxo').html(response);
			});
			
			return false;
	
		});
			

        $('.custom_date').datepicker({

				dateFormat : "yy-mm-dd",
				maxDate: 0

		});


		$('#wqep-td-command-status').hide();
		$('#wqep-td-command-product-display').hide();
		$('#wqep-td-coupon-description').hide();

		$('#wqep-entity').on('change',function(e){

			 var optionSelected = $("option:selected", this);
    		 var valueSelected = this.value;
    		 if(valueSelected == 'orders'){
    		 	$('#wqep-td-command-status').show();
    		 	$('#wqep-td-user-infos').hide();
    		 	$('#wqep-td-command-product-display').show();
    		 	$('#wqep-td-coupon-description').hide();
    		 }
             else if(valueSelected == 'coupons'){
                $('#wqep-td-command-status').show();
                $('#wqep-td-user-infos').hide();
                $('#wqep-td-command-product-display').hide();
                $('#wqep-td-coupon-description').show();
             }
    		 else{
    		 	$('#wqep-td-command-status').hide();	
    		 	$('#wqep-td-user-infos').show();
    		 	$('#wqep-td-command-product-display').hide();
    		 	$('#wqep-td-coupon-description').hide();
    		 }

		});

    });

}(jQuery));