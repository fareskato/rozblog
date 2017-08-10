jQuery(document).ready(function($) {
    $(".single_add_to_cart_button, .add_to_cart_button").data('loading-text',"<i class='fa fa-circle-o-notch fa-spin'></i> " + $('.adding-msg').html() );

	$(".single_add_to_cart_button, .add_to_cart_button").on("click", function() {
		
		if($(this).hasClass('product_type_variable')) return true;
		$(this).button('loading');
		if(parseInt(jQuery.data(document.body, "processing")) == 1) return false;
		jQuery.data(document.body, "processing", 1);
		
		var context = this;
		var product_id_Val;
		var quantity_Val;
		var variation_data = '';

        displayLoading($(context).parent().parent().parent());
		if ($(".variations_form", $(context).parent()).length) {
			
			variation_data = "variation_id="+$("input[name='variation_id']").val();
					
			var attrs = {};
			var variation_form = $('.variations_form');
			
			if($(variation_form).find('select[name^=attribute]').length) {
				$(variation_form).find('select[name^=attribute]').each(function() {
					var name = $(this).attr("name");
					var value = $(this).val();
					attrs[name] = value;
				});
			} else {
				$(variation_form).find('input[name^=attribute]').each(function() {
					attrs[$(this).attr("name")] = $(this).val();
				});
			}
			
			for(var entry in attrs) {
				variation_data += "&attribute["+entry+"]="+attrs[entry];
			}
		}
		
		if($(this).hasClass('single_add_to_cart_button')) {
			product_id_Val = $("input[name='add-to-cart']", $(this).parent()).val();
			quantity_Val = $("input[name='quantity']").val();
		} else {
			product_id_Val = $(this).attr('data-product_id');
			quantity_Val = $(this).attr('data-quantity');
			variation_data = '';
		}
		
		if(typeof quantity_Val == 'undefined') {
			quantity_Val = 1;
		}

		if($(context).hasClass('single_add_to_cart_button') && typeof use_product_grabber_2 != 'undefined' && use_product_grabber_2 == 1) 
		{
			var form = $(this).closest('form');
            //disable the parent

			$.ajax( {
				type: "POST",
				url: form.attr( 'action' ),
				data: form.serialize(),
				success: function( response ) 
				{
					if($(response).find('.woocommerce-error').length > 0) 
					{
						alert($(response).find('.woocommerce-error').text().trim());
						// Add to cart after alert message
						if($(context).hasClass('single_add_to_cart_button'))
						{
							$(context).button('reset');
							$(context).parent().parent().parent().find('.img-flytocart').css({'display': 'inline-block'});
							LetsFly($(context).parent().parent().parent().parent().find('.img-flytocart'), context);
							hideLoading($(context).parent().parent().parent());
							$(context).parent().parent().parent().find('.img-flytocart').removeAttr('style');
						} else {
							LetsFly($(context).parents('li').find('img'), context);
						}
						jQuery.data(document.body, "processing", 0);
					} 
					else 
					{
						if($(context).hasClass('single_add_to_cart_button')) 
						{
                            $(context).button('reset');
                            $(context).parent().parent().parent().find('.img-flytocart').css({'display': 'inline-block'});
                            LetsFly($(context).parent().parent().parent().parent().find('.img-flytocart'), context);
                            hideLoading($(context).parent().parent().parent());
                            $(context).parent().parent().parent().find('.img-flytocart').removeAttr('style');
						} else {
							LetsFly($(context).parents('li').find('img'), context);
						}
						jQuery.data(document.body, "processing", 0);
					}
				}
			} );
		} 
		else 
		{

			$.post(site_url+ "/wp-admin/admin-ajax.php", 
				"action=orak_add_to_cart&product_id="+product_id_Val+"&quantity="+quantity_Val+"&"+variation_data
			
			).done(function( data ) {
				if($(context).hasClass('single_add_to_cart_button')) 
				{
                    $(context).button('reset');
                    //hideLoading($(context).parent().parent().parent());
                    $(context).parent().parent().parent().find('.img-flytocart').css({'display': 'inline-block'});
                    LetsFly($(context).parent().parent().parent().find('.img-flytocart'), context);
                    /*
					if(typeof use_default_gallery_image != 'undefined' && use_default_gallery_image == 1) {

						LetsFly($(gallery_image_selector), context);
					} else {

						LetsFly($('.attachment-shop_single'), context);
					}
					*/
				} else {

					LetsFly($(context).parents('li').find('img'), context);
				}
			});
		}	
		return false;
	});
	
	function LetsFly(imageToDrag, context)
	{

		var scrollMillis = flying_speed;
		var scrollTop = $("html, body").scrollTop();
		
		if(scrollTop > 500 && scrollTop < 1000) {
			scrollMillis = scrollTop;
		} else {
			scrollMillis = parseInt(flying_speed)-(parseInt(flying_speed)*0.2);
		}
		
		if(typeof scroll_on_add != 'undefined' && scroll_on_add == 1) {
			$('html, body').animate({
				scrollTop: 0
			}, scrollMillis);
		}
		
		var defEq = parseInt(element_num)-1;
		
		var cart;
		var imgtodrag = imageToDrag;
		
		if(typeof use_default_selector != 'undefined' && use_default_selector == 1) {
			cart = $('a[href="'+cart_url+'"]:eq('+defEq+')');
		} else {
			cart = $(custom_selector+':eq('+defEq+')');
		}
		
		if ($(imgtodrag).length > 0)
		{
			var imgclone = imgtodrag.clone()
				.offset({
				top: imgtodrag.offset().top,
				left: imgtodrag.offset().left
			}).css({
				'opacity': parseInt(flying_img_opacity)/100,
				'position': 'absolute',
				'height': $(imgtodrag).css('height'),
				'width': $(imgtodrag).css('width'),
				'z-index': '999999'
			}).appendTo($('body')).animate({
				'top': cart.offset().top + parseInt(arrive_offset_y),
				'left': cart.offset().left + parseInt(arrive_offset_x),
				'width': 75,
				'height': 75
			}, {
                    'duration': parseInt(flying_speed)-(parseInt(flying_speed)*0.2),
                    'start': function ()  {
                        //if(imgtodrag.parent().parent().parent().hasClass('wpb_wl_quick_view_content')) {
                            $('#RoseModal').modal('hide');
                        //}
                    }
                });

			if(typeof use_woocommerce_widget != 'undefined' && use_woocommerce_widget == 1) {
				$supports_html5_storage = ( 'sessionStorage' in window && window['sessionStorage'] !== null );

				$fragment_refresh = {
					url: woocommerce_params.ajax_url,
					type: 'POST',
					data: { action: 'woocommerce_get_refreshed_fragments' },
					success: function( data ) {
						if ( data && data.fragments ) {

							$.each( data.fragments, function( key, value ) {
								$(key).replaceWith(value);
							});

							if ( $supports_html5_storage ) {
								sessionStorage.setItem( "wc_fragments", JSON.stringify( data.fragments ) );
								sessionStorage.setItem( "wc_cart_hash", data.cart_hash );
							}

							$('body').trigger( 'wc_fragments_refreshed' );
						}
					}
				};

				$.ajax($fragment_refresh);
			}
            var product_id = $("input[name='add-to-cart']", $(context).parent()).val();
            $.post(site_url+ "/wp-admin/admin-ajax.php",
                "action=feyarose_orders_get_nb_cart_items&product_id="+product_id

            ).done(function( data ) {
                    $('#twelve-roses').html(data['msg_roses']);
                    if(data['msg_roses'] == '') {
                        $('#twelve-roses').parent().hide();
                    } else {
                        $('#twelve-roses').addClass('woocommerce-success').parent().show();
                    }

                    $('#cart-element').find('.cart-items-num').html(data['nb_items']);
                    $confirm_modal = $(data['html_continue']);
                    $('body').find('#modalCart').remove();
                    if(imgtodrag.parent().parent().parent().hasClass('wpb_wl_quick_view_content')) {
                        $('body').append($confirm_modal);
                        var imgclone = imgtodrag.clone();
                        $confirm_modal.find('.img-added').html(imgclone[0]);
                        $confirm_modal.modal();
                    }
                    /*
                     $('#cart-element').find('.cart-items-num').html(data);
                     */
                });
			imgclone.animate({
				'width': 0,
				'height': 0
			}, function() {
				$(this).detach();
			});
		}
		else {
			// console.log(imgtodrag);
		}
	}
});
function displayLoading(element){
    element.addClass('adding-to-cart');
    jQuery("form.cart :input").prop('readonly', true);
    jQuery('form.cart button').prop('disabled', true);
    //element.append('<div class="loading-addtocart"></div>');
}
function hideLoading(element) {
    element.removeClass('adding-to-cart');
    jQuery("form.cart :input").prop('readonly', false);
    jQuery('form.cart button').prop('disabled', false);
    //element.find('.loading-addtocart').remove();
}