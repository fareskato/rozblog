/**
 * navigation.js
 *
 * Handles toggling the navigation menu for small screens.
 */

function handleResize() {
    if (window.innerWidth < 767) {

        if (jQuery('#title-fares').length == 0) {
            jQuery('.feyarose-navbar-container ').after("<div class='col-xs-12 hide-in-mob'><a class='on-line-mob' id='title-fares'>Купить букет</a></div>");
            // match the logo href attr
            var link = jQuery('.feyarose-navbar-shop').children('a').attr('href');
            // add it to the html a tag
            jQuery('.on-line-mob').attr("href",link);
            //jQuery('#title-fares').remove();
        }
    } else {
        jQuery('#title-fares').remove();
    }
}

function handleRTitle() {
    if (window.innerWidth < 767) {
        jQuery('a.navbar-brand').hide();
        if (jQuery('.title-small-center').length == 0) {
            jQuery('.rozblog-up').after("<a href='/' class='title-small-center'></a>");
        }
    } else {
        jQuery('.title-small-center').remove();
        jQuery('a.navbar-brand').show();
    }
}


var cartHoverInterval;

/* jQuery('.rozblog-up').after("<h1 class='title-small-center'>fares</h1>");*/

(function () {
    var container, button, menu;

    container = document.getElementById('site-navigation');
    if (!container) {
        return;
    }

    button = container.getElementsByTagName('button')[0];
    if ('undefined' === typeof button) {
        return;
    }

    menu = container.getElementsByTagName('ul')[0];

    // Hide menu toggle button if menu is empty and return early.
    if ('undefined' === typeof menu) {
        button.style.display = 'none';
        return;
    }

    menu.setAttribute('aria-expanded', 'false');

    if (-1 === menu.className.indexOf('nav-menu')) {
        menu.className += ' nav-menu';
    }

    button.onclick = function () {
        if (-1 !== container.className.indexOf('toggled')) {
            container.className = container.className.replace(' toggled', '');
            button.setAttribute('aria-expanded', 'false');
            menu.setAttribute('aria-expanded', 'false');
        } else {
            container.className += ' toggled';
            button.setAttribute('aria-expanded', 'true');
            menu.setAttribute('aria-expanded', 'true');
        }
    };


})();

/*
 |--------------------------------------
 | Fares Scripts:
 |--------------------------------------
 */

jQuery(document).ready(function () {
    //force updating of the cart

    jQuery.post(site_url+ "/wp-admin/admin-ajax.php",
        "action=feyarose_orders_get_nb_cart_items"

    ).done(function( data ) {
            jQuery('#cart-element').find('.cart-items-num').html(data['nb_items']);
        });
    /*
     |--------------------------------------   
     | Dispalying Search form:
     |--------------------------------------
     */
    if(jQuery('.feyarose-header-searchicon').length > 0) {
        jQuery('.feyarose-header-searchicon').on('click', function (event) {
            event.preventDefault();
            jQuery('.search-submit').css({
                "float": "right",
                "border-radius": "12px",
                "background": "#fff",
                "color": "#000",
                "border": "solid 1px #000",
                "font-size": "12px"
            });
            jQuery('.feyarose-header-searchform').css({"opacity": 0.5, "z-index": 9999}).slideToggle(300, function () {
                jQuery(this).animate({
                    opacity: 1
                }, 300)
            });
        });
    }


    /*
     |---------------------------------------------------------------------------
     | Adding logo after nav in small screens (refer to the top of the document)
     |---------------------------------------------------------------------------
     */
    jQuery(window).on('resize', function (evt) {
        handleResize();
        handleRTitle()
    });
    handleResize();
    handleRTitle()

    /*
     |---------------------------------------------------------------------------
     |  WooCommerce Coupon : (refer to cart.php)
     |---------------------------------------------------------------------------
     */
    jQuery(".add-coupon").addClass('buyIcon');
    jQuery(".coupon input[value='Применить купон ']").mouseenter(function () {
        jQuery('.add-coupon').addClass('violet');
    });
    jQuery(".coupon input[value='Применить купон ']").mouseleave(function () {
        jQuery('.add-coupon').removeClass('violet');
    });
    jQuery(".add_to").addClass('add-white');
    jQuery(".back-to-product a").mouseenter(function () {
        jQuery('.add_to').addClass('add-violet');
    });
    jQuery(".back-to-product a").mouseleave(function () {
        jQuery(this).css({"text-decoration": "none"});
        jQuery('.add_to').removeClass('add-violet');
    });
    /*
     |---------------------------------------------------------------------------
     |  WooCommerce product : (refer to product-thumbnails.php)
     |---------------------------------------------------------------------------
     */
    jQuery(".thumbnail-wrapper:last").css({
        "text-align": "right"
    });


    // Display required text after input fields on comment form
    jQuery("#commentform label:lang(en)").append("<span class='after-label'>(required)</span>");
    jQuery("#commentform label:lang(ru)").append("<span class='after-label'>(обязательно)</span>");



    /*
     |---------------------------------------------------------------------------
     |  Display the popup bootstrap modal (refer to header.php)
     |---------------------------------------------------------------------------
     */
    jQuery('#myModal').modal('show');

    /*
    |---------------------------------------
    |  add class when checkbox product types
    |----------------------------------------
    */
    if(jQuery(".product-addon.prod-types").length > 0) {
        jQuery(".product-addon.prod-types p input:checked").parent().addClass('postcard-selected');
        jQuery(".product-addon.prod-types p label").on('click',function(){
            jQuery(this).addClass('postcard-selected');
            jQuery(".product-addon.prod-types p label.postcard-selected").not(this).removeClass("postcard-selected");
        });
    }

    //======================================================================


    //var root_url = (location.hostname);
    //jQuery( "#wc_checkout_add_ons").find('#wc_checkout_add_ons_7').prop('readonly', true);
    //jQuery( "#wc_checkout_add_ons").find('#wc_checkout_add_ons_7').attr('placeholder','Select date');
    //jQuery( "#wc_checkout_add_ons").find('#wc_checkout_add_ons_7').datepicker( {});

//    ==============================================================

    var theCarousel = jQuery('#theCarousel');
        theCarousel.carousel({
        interval: 10000
    });
	jQuery('#theCarousel').swiperight(function() {
		jQuery(this).carousel('prev');
	});
	jQuery('#theCarousel').swipeleft(function() {
		jQuery(this).carousel('next');
	});
    if(theCarousel.length > 0) {
        theCarousel.find('.bouquets-carousel-inner .item').each(function(){
            var next = jQuery(this).next();
            if (!next.length) {
                next = jQuery(this).siblings(':first');
            }
            next.children(':first-child').clone().appendTo(jQuery(this));
            if (next.next().length>0) {
                next.next().children(':first-child').clone().appendTo(jQuery(this));
            }
            else {
                jQuery(this).siblings(':first').children(':first-child').clone().appendTo(jQuery(this));
            }
        });

        theCarousel.find('.bouquets-carousel-inner .item').each(function(){
            jQuery(this).find('.one-bouquet:eq(1)').addClass('center');
            jQuery(this).find('.one-bouquet').not(':eq(1)').addClass('edge');
        });

    }

    /*
     |-------------------------------------------------------------
     |  Center the bouquets carousel controller vertically
     |-------------------------------------------------------------
     */
    if (window.innerWidth < 768) {
        var carouselCenterImage = jQuery('#theCarousel .carousel-inner .item.active .center figure img').height();
        var carouselCenterImageHeight = (carouselCenterImage / 2) + 16;
        var bouquetCarouselControllers = jQuery('#theCarousel').find('.carousel-control');
        for(var i=0; i < bouquetCarouselControllers.length; i++){
            jQuery(bouquetCarouselControllers[i]).css('top', carouselCenterImageHeight + 'px')
        }
    }

    //jQuery('.RoseProductmodal').css('display','none');


    /* switch the logo button between blog and magazine pages */
   var pageBody = jQuery('body');
    var link_to_shop = jQuery('.link-to-magazine a').attr('href');
    var link_to_blog = jQuery('.navbar-brand').attr('href');
    //jQuery('.site-logo a').attr('href',link_to_shop);
    if(pageBody.hasClass('woocommerce-page')){
        //jQuery('.site-logo a').attr('href',link_to_blog);
        jQuery('.site-logo img').attr('src','//'+location.hostname+'/wp-content/themes/feyarose/images/toblog.png');
    }
    // phone number auto complete




    var id = jQuery(".rose-list-add-to-cart").attr('id');
    jQuery(this).find('.single_add_to_cart_button').attr('id',id);


    // display add roses message
    jQuery('.twelve-roses-message').addClass('woocommerce-message woocommerce-success');
    // Remove the woocommerce-success class if there is no notices(if there are more than 12 roses in the cart)
    if(jQuery('.twelve-roses-message').text().length == 0){
        jQuery('.twelve-roses-message.woocommerce-success').removeClass('woocommerce-success');
    }

    /*Hovering the cart widget*/

    if(jQuery('.widget_shopping_cart_content').length > 0) {
     jQuery('#cart-element, .widget_shopping_cart_content').on('mouseenter', function (evt) {
     if(jQuery(window).width() > 960) {
     jQuery(this).addClass('minicart-open');
     jQuery('.widget_shopping_cart_content').show();
     }

     });
     jQuery('#cart-element, .widget_shopping_cart_content').on('mouseleave', function (evt){
     jQuery('#cart-element, .widget_shopping_cart_content').removeClass('minicart-open');
     cartHoverInterval = setInterval(function () {
     if(!jQuery('#cart-element').hasClass('minicart-open') && !jQuery('.widget_shopping_cart_content').hasClass('minicart-open')) {
     jQuery('.widget_shopping_cart_content').hide();
     clearInterval(cartHoverInterval);
     }
     }, 700);
     });
    }

    /**
     *  Header scroll effect
     * */
    if (window.innerWidth < 768) {
        jQuery(window).scroll(function () {
            var scroll = jQuery(document).scrollTop();
            var fourItemsWrapper = jQuery('.four-items-wrapper');
            if (scroll >= 40) {
                fourItemsWrapper.find('.navbar-brand').addClass('small-scroll-logo');
                fourItemsWrapper.find('.woo-cart-wrapper').addClass('fixed-cart').removeClass('col-xs-6').addClass('col-xs-3');
                fourItemsWrapper.find('.delivery-menu-header').addClass('fixed-cart').css('border-bottom','solid 2px #ddd');
                jQuery('.feyarose-navbar-header').find('.navbar-collapse').addClass('collapse-top');
                fourItemsWrapper.find('.woo-user-icon, .textwidget-container').hide();
            } else {
                fourItemsWrapper.find('.navbar-brand').removeClass('small-scroll-logo');
                fourItemsWrapper.find('.woo-cart-wrapper').removeClass('fixed-cart').removeClass('col-xs-3').addClass('col-xs-6');
                fourItemsWrapper.find('.delivery-menu-header').removeClass('fixed-cart').css('border-bottom','none');
                jQuery('.feyarose-navbar-header').find('.navbar-collapse').removeClass('collapse-top');
                fourItemsWrapper.find('.woo-user-icon, .textwidget-container').show();
            }
        });

        /* Scroll up when open the menu */
        jQuery('#masthead').find('.navbar-header').find('.navbar-toggle').on('click',function(){
            jQuery("html, body").animate({ scrollTop: 0 }, "slow");
        });
    }



    jQuery('.about_paypal:lang(ru)').html("Что такое PayPal?");
}); // end document ready




