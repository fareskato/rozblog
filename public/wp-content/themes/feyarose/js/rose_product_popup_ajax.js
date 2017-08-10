/**
 * Created by fares on 21.12.15.
 */
var currentPopupProduct = null;
var currentPopupProductParent = null;
var originalUrl = null;
// var bouquet_modal_body;
jQuery(document).ready(function () {

    originalUrl = window.document.location.href;
    jQuery('#RoseModal').css({"position":"absolute", "right":"0%","outline":"none","z-index":"1000","width":"100%","padding":"0"});

    jQuery('#RoseModal').on('hidden.bs.modal', function () {
        // do something…
        var off = jQuery('#bouquets').offset();
        jQuery(document).scrollTop(off.top - 160);
    })
    // This is temporary(Translate the text via wpml)
    jQuery('.order-from-magazine a:lang(ru)').text('Заказ');
    // add btn class to order button:
    jQuery('.order-from-magazine a').addClass('btn btn-primary btn-lg');
    jQuery('.one-bouquet  figure').on('click',function(ev){
        ev.preventDefault();
        var self = jQuery(this).find('img');
        //jQuery('.RoseProductmodal').show();
        jQuery('#theCarousel').carousel('pause');
        jQuery('#feyarose-products-carousel').carousel('pause');

        var productId = jQuery(self).data('id');
        var thumbnail_src  = jQuery(self).prop('src');
        var productPageUrl = jQuery(self).attr('href');
        var post_name = jQuery(self).data('post_name');
        var args = {
            //action : 'rose_ajax_popup',
            // send the variable to the action(functions.php).
            'ID': productId,
            'page_url': productPageUrl,
            'Post_Name': post_name
            //'bouquetImages':bouquetGallery
        };

        var bouquet_modal_body = jQuery('#RoseModal').modal().find('.modal-body');
        if(currentPopupProduct != null) {
            currentPopupProductParent.append(currentPopupProduct);
            currentPopupProduct.hide();
        }

        currentPopupProduct = jQuery('#wpb_wl_quick_view_'+productId);
        currentPopupProductParent = currentPopupProduct.parent();

        bouquet_modal_body.html(currentPopupProduct);
        currentPopupProduct.show();

        bouquet_modal_body.find('.gift-date').addClass('col-lg-7 col-xs-12');
        bouquet_modal_body.find('.wpb_wl_images').addClass('col-sm-5').next().addClass('col-sm-7');
        bouquet_modal_body.find('.wpb_wl_images').css('padding-left', '25px');
        bouquet_modal_body.find('.wpb_wl_images').find(".woocommerce-main-image").on("click",function(e){
            e.preventDefault();
        });
        /* Display the first three images from the bouquet gallery */
        bouquet_modal_body.find('.wpb_wl_images').find(".one-bouquet-gallery-image").hide();
        bouquet_modal_body.find('.wpb_wl_images').find(".one-bouquet-gallery-image:lt(3)").show();
        /* Change the thumbnail background image by clicking on the gallery image */
        bouquet_modal_body.find('.wpb_wl_images').find(".one-bouquet-gallery-image").on("click",function(){
            var gallery_image = jQuery(this).find('img');
            var gallery_image_src = gallery_image.attr("src");
            bouquet_modal_body.find('.wpb_wl_images').find(".woocommerce-main-image img").attr("src",gallery_image_src);
        });
        handleInputMultiplier(bouquet_modal_body.find('.feyarose-input-multiplier'));

        //one-bouquet-gallery-image
        var gift = bouquet_modal_body.find(".checkbox-add-card input");
        bouquet_modal_body.find(".product-addon.postcard p label input").prop('checked', false);
        //gift.prop('checked', false);

        bouquet_modal_body.find('.checkbox-add-card').prop('checked',false);
        bouquet_modal_body.find('.delivery-comment , #postcard-message, .postcard ,.gift-from ,.gift-to').hide();
        bouquet_modal_body.find('div.postcard').addClass('col-lg-5 col-xs-12').css("margin-top","-1em").find('p label').css({"width": "70px", "height": "70px"});
        bouquet_modal_body.find('div.delivery-comment,#postcard-message').addClass('col-lg-7 col-xs-10').css("margin-top",'-11.2em');



        jQuery('#checkbox-add-card').change(function() {
            if (this.checked) {
                bouquet_modal_body.find('.postcard ,#postcard-message,.delivery-comment,.gift-from ,.gift-to ').fadeIn(function(){
                    bouquet_modal_body.find(".postcard p label").on('click',function(){
                        jQuery(this).addClass('postcard-selected');
                        bouquet_modal_body.find(".postcard p label.postcard-selected").not(this).removeClass("postcard-selected");
                        //jQuery('.delivery-comment').fadeIn(400);
                    });

                });
            }else{
                bouquet_modal_body.find('.postcard ,#postcard-message,.delivery-comment,.gift-from ,.gift-to ').fadeOut();
            }
        });





        bouquet_modal_body.find('.postcard-without').off("click").on('click','h3.addon-name',function(e){

            bouquet_modal_body.find('.postcard ,#postcard-message,.delivery-comment,.gift-from ,.gift-to ').fadeToggle(function(){
                bouquet_modal_body.find(".postcard p label").on('click',function(){
                        jQuery(this).addClass('postcard-selected');
                    bouquet_modal_body.find(".postcard p label.postcard-selected").not(this).removeClass("postcard-selected");
                    //jQuery('.delivery-comment').fadeIn(400);
                });

            });
             jQuery(bouquet_modal_body).animate({ scrollTop: jQuery(bouquet_modal_body).top() });
        });

        // New Stuff






        if(gift.prop('checked') == true || gift.prop('checked') == 'checked') {
            bouquet_modal_body.find('.postcard ,#postcard-message,.delivery-comment,.gift-from ,.gift-to ').slideToggle();
        }

        //    Display bouquet description when click description checkbox(on the modal)
        var bouquetDescriptionTrigger = bouquet_modal_body;
        var bouquetDescription = bouquet_modal_body.find('.bouquet-popup-description p').text();
        var desc_checkBox = bouquet_modal_body.find('#bouquet-mobile-description');
        var mobileBouquetDescription = bouquet_modal_body.find('.bouquet-mobile-description-wrapper');
        mobileBouquetDescription.text(bouquetDescription).hide();
        bouquetDescriptionTrigger.off('click').on('click','.bouquet-mobile-description-container',function(){
            mobileBouquetDescription.slideToggle();
            jQuery(bouquet_modal_body).animate({ scrollTop: jQuery(bouquet_modal_body).top() });
        });







        //bouquet_modal_body.find(".wc-product-popup .product-addon-otkrytka-k-buketu p label").removeClass("selected-cart-item").find('input#checkbox-add-card').prop('checked', false);
        bouquet_modal_body.find(".wc-product-popup .product-addon-otkrytka-k-buketu p label").unbind('click').on('click',function(){
            // jQuery(this).addClass('selected-cart-item');
            // bouquet_modal_body.find(".postcard p label.postcard-selected").not(this).removeClass("selected-cart-item");
            // if(jQuery(this).parent().hasClass('postcard-without')){
            //     jQuery(this).removeClass('selected-cart-item');
            // }
            //jQuery(".wc-product-popup .product-addon-otkrytka-k-buketu p label").not(this).removeClass("selected-cart-item");
        });



        /**
         * Create bouquet carousel(thumbnail and gallery images) OR only thumbnail  (Mobile)
         * */
        if(window.innerWidth <= 767){
            jQuery('#mobile-bouquet-carousel').remove();
            var images_path = '//'+location.hostname+'/wp-content/themes/feyarose/images/';
            var bouquetGallery = undefined;
            if(bouquetImages[productId] !== undefined){
                bouquetGallery = bouquetImages[productId].images;
            }
            if( bouquetGallery !== undefined){
                if(jQuery(bouquet_modal_body).find('.mob-bouquet-carousel').length == 0 ){

                    var body = "<div id='mobile-bouquet-carousel' class='mob-bouquet-carousel carousel' data-ride='carousel'>";
                    body+= " <div class='carousel-inner role='listbox'>";
                    var bIndex = 0;
                    var nbImages = bouquetGallery.length;
                    for(var image = 0; image < nbImages; image++ ){
                        var classActive = (bIndex == 0) ? ' active ' : '';
                        body+= "<img src='"+bouquetGallery[image]+"' class='item "+classActive+"' style='width:100%'>";
                        bIndex = bIndex + 1;
                    }
                    body+= "</div>";
                    if(nbImages > 1) {
                        body+="<a class='left carousel-control' href='#mobile-bouquet-carousel' role='button' data-slide='prev'>"
                        body+="<span class='glyphicon glyphicon-chevron-left' aria-hidden='true'>"
                        body+="<img src='"+images_path+"slider-left-small.png'></span><span class='sr-only'>Previous</span></a>";
                        body+="<a class='right carousel-control' href='#mobile-bouquet-carousel' role='button' data-slide='next'>"
                        body+="<span class='glyphicon glyphicon-chevron-right' aria-hidden='true'>"
                        body+="<img src='"+images_path+"slider-right-small.png'></span><span class='sr-only'>Previous</span></a>";
                    }

                    body+= "</div>";
                    jQuery(bouquet_modal_body).find('.wpb_wl_product_title').after(body);
                    jQuery('#mobile-bouquet-carousel').carousel();
					jQuery("#mobile-bouquet-carousel").swiperight(function() {
						jQuery(this).carousel('prev');
					});
					jQuery("#mobile-bouquet-carousel").swipeleft(function() {
						jQuery(this).carousel('next');
					});
				}
            }else{
                if(jQuery(bouquet_modal_body).find('.mobile-bouquet-thumbnail').length == 0){
                    var bouquetImage  = "<div class='col-xs-12 mobile-bouquet-thumbnail'><img src='"+thumbnail_src+"' style='width:100%'></div>";
                    jQuery(bouquet_modal_body).find('.wpb_wl_product_title').after(bouquetImage);
                }
            }
        }



    });



});

