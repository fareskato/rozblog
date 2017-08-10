/**
 * Created by Florian on 4/15/2015.
 */
var intervalHashtag = null;

function handleInputMultiplier (element, isObject) {
    var images_src = '//'+location.hostname+'/wp-content/themes/feyarose/images/';
    // Quantity buttons
    var $elt;

    (isObject != undefined) ? $elt = element : $elt = jQuery(element);
    if(!$elt.hasClass('multiprocessed')) {
        $elt.addClass('multiprocessed');

        $elt.addClass('buttons_added').append('<input type="button" value="+" class="feyarose-plus" />').append("<div class='col-sm-2 hidden-xs' style='float: right !important;'><img src='"+images_src+"rose-qty-r.png' /></div>").prepend('<input type="button" value="-" class="feyarose-minus" />').prepend("<div class='col-sm-2 hidden-xs'><img src='"+images_src+"rose-qty-l.png' style='width: 80%;margin-top: 10%;'/></div>");

        $elt.on('click', '.feyarose-plus, .feyarose-minus', function () {
            // Get values

            var $qty = jQuery(this).closest('.quantity').find('.addon'),
                currentVal = parseFloat($qty.val()),
                max = parseFloat($qty.attr('max')),
                min = parseFloat($qty.attr('min')),
                step = $qty.attr('step');

            // Format values
            if (!currentVal || currentVal === '' || currentVal === 'NaN') currentVal = 0;
            if (max === '' || max === 'NaN') max = '';
            if (min === '' || min === 'NaN') min = 0;
            if (step === 'any' || step === '' || step === undefined || parseFloat(step) === 'NaN') step = 1;

            // Change the value
            if (jQuery(this).is('.feyarose-plus')) {

                if (max && ( max == currentVal || currentVal > max )) {
                    $qty.val(max);
                } else {
                    $qty.val(currentVal + parseFloat(step));
                }

            } else {
                if (min && ( min == currentVal || currentVal < min )) {
                    $qty.val(min);
                } else if (currentVal > 0) {
                    $qty.val(currentVal - parseFloat(step));
                }

            }

            // Trigger change event
            $qty.trigger('change');
        });
    }


}
(function($) {
    // Remove the # from the hash, as different browsers may or may not include it
    var hash = location.hash.replace('#', '');
    if (hash != '') {
        intervalHashtag = setInterval(function(){
            // Clear the hash in the URL
            // location.hash = '';   // delete front "//" if you want to change the address bar
            var $target = $('#' + hash);
            if($target.length > 0) {
                $('html, body').animate({scrollTop: $target.offset().top - 160},10);
                clearInterval(intervalHashtag);
            }

        }, 100);
    }






    $(document).ready(function () {


        if($('.post-share-buttons').length > 0) {
            var $listShare = $('.post-share-buttons');
            $listShare.find('a').on('click', function (evt) {
                evt.preventDefault();
                var $this = $(this);
                window.open($this.attr('href'),$this.html(),"menubar=no, status=no, scrollbars=no, menubar=no, width=500, height=500");
            });
        }

        //comments links
        $('.single-post').find('.comments-link').prop('href', '#comments-anchor').on('click', function (evt){
            evt.preventDefault();
            var off = jQuery("#comments-anchor").offset();
            jQuery(document).scrollTop(off.top - 150);
        });

        //General anchor links
        $('.link-anchor').each(function () {
            var $this = $(this);
            var lelien = $this.attr('href');

            if (lelien !== undefined) {
                //if (lelien.indexOf('#') === 0) {
                    $this.on('click', function (evt) {
                        var $this = $(this);
                        evt.preventDefault();
                        var lAncre = $this.attr('href').split('#')[1];
                        var off = jQuery('#'+lAncre).offset();
                        jQuery(document).scrollTop(off.top - 160);
                    });
                //}
            }


        });


        $('.single-post').find('.entry-content').find('a').each(function () {
            var $this = $(this);
            var lelien = $this.attr('href');
            if(lelien !== undefined) {
                if( lelien.indexOf('#') === 0 ) {
                    $this.on('click', function (evt){
                        var $this = $(this);
                        evt.preventDefault();
                        var off = jQuery($this.attr('href')).offset();
                        jQuery(document).scrollTop(off.top - 160);
                    });
                }
            }


        });

        $(window).scroll(function () {
            var stop = jQuery(document).scrollTop();
            var laClasse = 'scroll-fixed-menu';
            if(stop > 150) {
                if(!jQuery('body').hasClass(laClasse)) {
                    jQuery('body').addClass(laClasse);
                }
            } else {
                if(jQuery('body').hasClass(laClasse)) {
                    jQuery('body').removeClass(laClasse);
                }
            }

        });
        /**
         * Set Modal height(equals to window height)
         * */
        var modalHeight = window.innerHeight - 50;
      jQuery('#RoseModal').find('.modal-body').css('height',modalHeight);

      /* Checkout type */
        jQuery('select[name="paymentType"] option:contains("С банковской карты")').prop('selected',true);

    /**
     * Additional checkout fields
     * */
        $('#additional_field_154_field').find('#additional_field_154').val('');
        $('#billing_phone_field').find('#billing_phone').val('');
        var lang = $('html').attr('lang');
        //console.log(lang);
        if(lang == 'ru-RU'){
            $('#additional_field_270_field').find('label').contents().first().replaceWith('Временной интервал доставки');
            $('#additional_field_98_field').find('label').contents().first().replaceWith('Дата доставки');
            $('#additional_field_655_field').find('label').contents().first().replaceWith('Улица');
            $('#additional_field_926_field').find('label').contents().first().replaceWith('Дом');
            $('#additional_field_533_field').find('label').contents().first().replaceWith('Квартира');
            $('#additional_field_394_field').find('label').contents().first().replaceWith('Город');
            $('#additional_field_249_field').find('label').contents().first().replaceWith('Домофон/код');
            $('#additional_field_596_field').find('label').contents().first().replaceWith('Этаж');
            $('#additional_field_593_field').find('label').contents().first().replaceWith('Метро');
            $('#additional_field_30_field').find('label').contents().first().replaceWith('ФИО');
            $('#additional_field_154_field').find('label').contents().first().replaceWith('Номер телефона получателя');
            $('#additional_field_804_field').find('label').contents().first().replaceWith('Комментарии');

        }

        if(lang == 'en-US' ){
            $('#wc_checkout_add_ons_5_field label').first().text('Delivery area');
            $('#wc_checkout_add_ons_5_field label.checkbox').first().text('Within the Ring Road in the order of 12 roses (Free)');
            $('#wc_checkout_add_ons_5_field label.checkbox:eq(1)').text('For Moscow within Moscow (except Zelenograd administrative and Trinity AO) (1000rub.)');
            $('#wc_checkout_add_ons_5_field label.checkbox:eq(2)').text('Zelenograd AO; Troesky (1500 rubles).');
        }

		jQuery('input[name="payment_method"]').live('change',function(){
			jQuery('input[name="payment_method"]').each(function(){
			    var $sub = jQuery(this).siblings('span').find('span');
				if ( jQuery(this).is(':checked') ) {
					$sub.show();
				} else {
					$sub.hide();
				}
			});
		});

        jQuery('.delivery_option_trigger, .payment-button').live('click',function(){
			$(this).siblings('input').click();
		});

       /**
        * CHECKOUT PAGE (Mobile)
        * */
        var checkoutWindowWidth =window.innerWidth;
        if(checkoutWindowWidth <= 425) {


            /****** display login form ******/
            var checkoutLoginForm =  jQuery('.woocommerce-checkout').find('#primary').find('.login');
            checkoutLoginForm.hide();
            var checkout_checkBoxWrapper = jQuery('.checkout-login-form-trigger');
            checkout_checkBoxWrapper.on('click',function(){
                checkout_checkBoxWrapper.find('.form-trigger-arrow').toggleClass('rotate-up');
                checkoutLoginForm.slideToggle();
            });
            /****** wrapping shipping fields with container the toggle via checkout ******/
            jQuery('.woocommerce_checkout_shipping-wrapper').find('#additional_field_270_field,#additional_field_98_field, #additional_field_655_field, #additional_field_926_field, #additional_field_533_field, #additional_field_394_field').wrapAll('<div id="shipping_delivery_info">');
            jQuery('.woocommerce_checkout_shipping-wrapper-recipient-data').find('#additional_field_270_field,#additional_field_98_field, #additional_field_655_field, #additional_field_926_field, #additional_field_533_field, #additional_field_394_field').wrapAll('<div id="shipping_recipient _info">');
            var recipientData = jQuery('#shipping_delivery_info').nextAll();
            jQuery('.woocommerce_checkout_shipping-wrapper-recipient-data').find('h3').after(recipientData);
            var deliveryTrigger = jQuery('.checkout-shipping-trigger-wrapper');
            deliveryTrigger.on('click',function(){
               jQuery('#shipping_delivery_info').slideToggle();
                deliveryTrigger.find('.shipping-trigger-arrow').toggleClass('rotate-down');
            });

            /****** Billing trigger stuff ******/
            jQuery('.woocommerce-billing-fields').find('h3').prepend('<span class="billing-trigger"><span class="glyphicon glyphicon-chevron-up billing-trigger-arrow" aria-hidden="true"></span></span>').nextAll().wrapAll('<div id="billing_content">');
            jQuery('.woocommerce-billing-fields').find('h3').on('click',function(){
                jQuery('#billing_content').slideToggle(300);
                jQuery('.billing-trigger-arrow').toggleClass('rotate-down');
            });

            /****** Shipping(Recipient) trigger stuff ******/
            var recipientHeader = jQuery('.woocommerce_checkout_shipping-wrapper-recipient-data').find('h3');
            recipientHeader.prepend('<span class="recipient-trigger"><span class="glyphicon glyphicon-chevron-up recipient-trigger-arrow" aria-hidden="true"></span></span>').nextAll().wrapAll('<div id="recipient_content">');
            jQuery(recipientHeader).on('click',function(){
                jQuery('#recipient_content').slideToggle(300);
                jQuery('.recipient-trigger-arrow').toggleClass('rotate-down');
            });

            /****** Moscow delivery trigger stuff ******/
            var deliveryRegion = jQuery('#wc_checkout_add_ons');
            var deliveryRegionTrigger = deliveryRegion.find('label:first-child');
            deliveryRegionTrigger.prepend('<span class="delivery-region-trigger"><span class="glyphicon glyphicon-chevron-up delivery-region-trigger-arrow" aria-hidden="true"></span></span>').nextAll().wrapAll('<div id="delivery_region_content">');
            deliveryRegionTrigger.on('click',function () {
                 jQuery('#delivery_region_content').slideToggle(300);
                jQuery('.delivery-region-trigger-arrow').toggleClass('rotate-down');
            });
            /****** Moscow delivery options trigger stuff(for each option) ******/
            // Add trigger span
            jQuery('#delivery_region_content').find('.input-checkbox').after('<span class="delivery_option_trigger"></span>');
            // Wrap each trigger and input and label with custom wrapper
            var totalChilds =  jQuery('#delivery_region_content').children();
            for(var i =0, all = totalChilds.length; i < all; i+=4 ){
                totalChilds.slice(i, i+4).wrapAll('<div class="delivery_option_wrapper"></div>');
            }
            // change the background and display the OK symbol on the selected option
            jQuery('.delivery_option_wrapper').find('.input-checkbox').first().prop('checked', true).parent().addClass('delivery_option_bg');
            jQuery('.delivery_option_wrapper').find('.delivery_option_trigger').prepend('<span class="glyphicon glyphicon-ok delivery_fired_option" aria-hidden="true"></span>');
            jQuery('.delivery_fired_option:gt(0)').hide();
            jQuery('#delivery_region_content input').on('change', function() {
                jQuery(this).parent().addClass('delivery_option_bg').find('.delivery_fired_option').show();
                jQuery(this).parent().siblings().removeClass('delivery_option_bg').find('.delivery_fired_option').hide();
            });
            // add color to the price of the delivery option(checkout)
            var deliveryOptionLabels = jQuery('.delivery_option_wrapper').find('label');
            deliveryOptionLabels.each(function(){
                var $this = jQuery(this);
                $this.html($this.html().replace(/(\S+)\s*$/, '<div class="delivery-option-price">$1</div>'));
                jQuery('.delivery-option-price').text(function(_, text){
                    return text.replace(/\(|\)/g, '');
                });
            });
            jQuery(document).ajaxComplete(function(){
                jQuery('#order_review').find('#payment').find('p label').on('click',function(){
                    jQuery(this).prev().toggle();
                });
				jQuery('input[name="payment_method"]').change();
                // jQuery('#payment').find('#payment_method_cheque').parent().css('background','#900');
            });




            /******  Ship to address (switch between this address and another address)  ******/
            var anotherBillingFields = jQuery('#billing_address_1_field, #billing_city_field, #billing_phone_field');
            var anotherShipTo = jQuery('.ship-to-wrapper .input-checkbox').not('#ship-to-this-address-checkbox');
            jQuery('#ship-to-this-address-checkbox').prop('checked',true);
            jQuery(anotherShipTo).prop('checked',false);
            // Display shipping values in billing fields by default.
            function getDeliverAddress(){
                var addresses = ['#additional_field_655', '#additional_field_926', '#additional_field_533'];
                jQuery('#shipping_delivery_info').on('keyup',addresses,function(){
                    if(typeof(jQuery('#additional_field_655').val() !== undefined) && jQuery('#additional_field_655').val().length > 1){
                         var streetValue = jQuery('#additional_field_655').val();
                    }
                    if(typeof(jQuery('#additional_field_926').val() !== undefined) && jQuery('#additional_field_926').val().length > 1){
                         var homeValue = jQuery('#additional_field_926').val();
                    }
                    if(typeof(jQuery('#additional_field_533').val() !== undefined) && jQuery('#additional_field_533').val().length > 1){
                         var apartmentValue = jQuery('#additional_field_533').val();
                    }
                     var totalAddress = streetValue + ' / ' +  homeValue + ' / ' + apartmentValue;
                    jQuery('#another-billing-fields #billing_address_1').val(totalAddress);
                });
                jQuery('#additional_field_394').val('Москва').prop("readonly", true);
                jQuery('#billing_city').val('Москва').prop("readonly", true);
                /*
                jQuery('#additional_field_394').keyup(function() {
                    var cityValue = jQuery(this).val();
                    if(cityValue.length > 1){
                        jQuery('#another-billing-fields #billing_city').val(cityValue);
                    }
                });
                */
                jQuery('#additional_field_154').keyup(function() {
                    var phoneValue = jQuery(this).val();
                    jQuery('#another-billing-fields #billing_phone').val(phoneValue);
                });
            }
            getDeliverAddress();
            // Switch between ship to this address and ship to another address.
            jQuery('#another-billing-fields').prepend(anotherBillingFields).slideUp();
            jQuery('#ship-to-this-address-checkbox').change(function () {
                if (this.checked) {
                    jQuery(anotherShipTo).prop('checked', false);
                    jQuery('.to-another-address-trigger .glyphicon-ok').hide();
                    jQuery('.to-this-address-trigger .glyphicon-ok').show();
                    jQuery('#another-shipping-fields').slideUp();
                    jQuery('#another-billing-fields').prepend(anotherBillingFields).slideUp();
                }
            });
            jQuery(anotherShipTo).change(function () {
                if (this.checked) {
                    jQuery('#ship-to-this-address-checkbox').prop('checked', false);
                    jQuery('.to-another-address-trigger .glyphicon-ok').show();
                    jQuery('.to-this-address-trigger .glyphicon-ok').hide();
                    jQuery('#another-billing-fields').prepend(anotherBillingFields).slideDown();
                }
            });



        } // End window width

        /****** scroll to choose bouquet OR to create your bouquet  ******/
        if(window.innerWidth < 767){
            jQuery('.two-titles').find('#choose-bouquet').on('click',function(ev){
                jQuery('html, body').animate({
                    scrollTop: jQuery("#bouquets").offset().top - (jQuery("#bouquets").height() * 3)
                },'slow');
                return false;
                ev.preventDefault();
            });
            jQuery('.two-titles').find('#create-bouquet').on('click',function(ev){
                jQuery('html, body').animate({
                    scrollTop: jQuery("#roses").offset().top - (jQuery("#roses").height() * 3)
                },'slow');
                return false;
                ev.preventDefault();
            });
        }
        if(window.innerWidth  > 767 && window.innerWidth < 1151 ){
            jQuery('.two-titles').find('#choose-bouquet').on('click',function(ev){
                jQuery('html, body').animate({
                    scrollTop: jQuery("#bouquets").offset().top - jQuery("#bouquets").height()
                },'slow');
                return false;
                ev.preventDefault();
            });
            jQuery('.two-titles').find('#create-bouquet').on('click',function(ev){
                jQuery('html, body').animate({
                    scrollTop: jQuery("#roses").offset().top - jQuery("#roses").height()
                },'slow');
                return false;
                ev.preventDefault();
            });
        }

        jQuery(document).ajaxComplete(function(){
            if(lang == 'ru-RU'){
                var chkError = jQuery('.woocommerce-checkout').find('.woocommerce-error li');
                chkError.each(function(i, ele){
                    if(jQuery(ele).contents().last().text() != 'Вам необходимо принять наши условия и правила.'){
                        jQuery(ele).contents().last().replaceWith(' обязательное поле.');
                    }

                    var errText = jQuery(ele).contents().first().text();
                    switch(errText){
                        case 'Delivery date':
                            jQuery(ele).contents().first().text('Дата доставки');
                            break;
                        case 'Street':
                            jQuery(ele).contents().first().text('Улица');
                            break;
                        case 'House':
                            jQuery(ele).contents().first().text('Дом');
                            break;
                        case 'Apartment':
                            jQuery(ele).contents().first().text('Квартира');
                            break;
                        case 'Full Name':
                            jQuery(ele).contents().first().text('ФИО');
                            break;
                        case 'First Name':
                            jQuery(ele).contents().first().text('Имя');
                            break;
                        case 'Phone':
                            jQuery(ele).contents().first().text('Телефон');
                            break;
                        case 'Receipient phone number':
                            jQuery(ele).contents().first().text('Номер телефона получателя');
                            break;
                    }
                })
            }


        });


        $('.owl-carousel').owlCarousel({
            loop:true,
            margin:5,
            nav:false,
            autoplay:true,
            autoplayTimeout:5000,
            smartSpeed:1000,
            autoplayHoverPause:true,
            responsive:{
                0:{
                    items:2
                },
                600:{
                    items:4
                },
                1000:{
                    items:8
                }
            }
        });



    });
})(jQuery);



/*! jQuery Mobile v1.3.2 | Copyright 2010, 2013 jQuery Foundation, Inc. | jquery.org/license */
(function(e,t,n){typeof define=="function"&&define.amd?define(["jquery"],function(r){return n(r,e,t),r.mobile}):n(e.jQuery,e,t)})(this,document,function(e,t,n,r){(function(e,t,n,r){function x(e){while(e&&typeof e.originalEvent!="undefined")e=e.originalEvent;return e}function T(t,n){var i=t.type,s,o,a,l,c,h,p,d,v;t=e.Event(t),t.type=n,s=t.originalEvent,o=e.event.props,i.search(/^(mouse|click)/)>-1&&(o=f);if(s)for(p=o.length,l;p;)l=o[--p],t[l]=s[l];i.search(/mouse(down|up)|click/)>-1&&!t.which&&(t.which=1);if(i.search(/^touch/)!==-1){a=x(s),i=a.touches,c=a.changedTouches,h=i&&i.length?i[0]:c&&c.length?c[0]:r;if(h)for(d=0,v=u.length;d<v;d++)l=u[d],t[l]=h[l]}return t}function N(t){var n={},r,s;while(t){r=e.data(t,i);for(s in r)r[s]&&(n[s]=n.hasVirtualBinding=!0);t=t.parentNode}return n}function C(t,n){var r;while(t){r=e.data(t,i);if(r&&(!n||r[n]))return t;t=t.parentNode}return null}function k(){g=!1}function L(){g=!0}function A(){E=0,v.length=0,m=!1,L()}function O(){k()}function M(){_(),c=setTimeout(function(){c=0,A()},e.vmouse.resetTimerDuration)}function _(){c&&(clearTimeout(c),c=0)}function D(t,n,r){var i;if(r&&r[t]||!r&&C(n.target,t))i=T(n,t),e(n.target).trigger(i);return i}function P(t){var n=e.data(t.target,s);if(!m&&(!E||E!==n)){var r=D("v"+t.type,t);r&&(r.isDefaultPrevented()&&t.preventDefault(),r.isPropagationStopped()&&t.stopPropagation(),r.isImmediatePropagationStopped()&&t.stopImmediatePropagation())}}function H(t){var n=x(t).touches,r,i;if(n&&n.length===1){r=t.target,i=N(r);if(i.hasVirtualBinding){E=w++,e.data(r,s,E),_(),O(),d=!1;var o=x(t).touches[0];h=o.pageX,p=o.pageY,D("vmouseover",t,i),D("vmousedown",t,i)}}}function B(e){if(g)return;d||D("vmousecancel",e,N(e.target)),d=!0,M()}function j(t){if(g)return;var n=x(t).touches[0],r=d,i=e.vmouse.moveDistanceThreshold,s=N(t.target);d=d||Math.abs(n.pageX-h)>i||Math.abs(n.pageY-p)>i,d&&!r&&D("vmousecancel",t,s),D("vmousemove",t,s),M()}function F(e){if(g)return;L();var t=N(e.target),n;D("vmouseup",e,t);if(!d){var r=D("vclick",e,t);r&&r.isDefaultPrevented()&&(n=x(e).changedTouches[0],v.push({touchID:E,x:n.clientX,y:n.clientY}),m=!0)}D("vmouseout",e,t),d=!1,M()}function I(t){var n=e.data(t,i),r;if(n)for(r in n)if(n[r])return!0;return!1}function q(){}function R(t){var n=t.substr(1);return{setup:function(r,s){I(this)||e.data(this,i,{});var o=e.data(this,i);o[t]=!0,l[t]=(l[t]||0)+1,l[t]===1&&b.bind(n,P),e(this).bind(n,q),y&&(l.touchstart=(l.touchstart||0)+1,l.touchstart===1&&b.bind("touchstart",H).bind("touchend",F).bind("touchmove",j).bind("scroll",B))},teardown:function(r,s){--l[t],l[t]||b.unbind(n,P),y&&(--l.touchstart,l.touchstart||b.unbind("touchstart",H).unbind("touchmove",j).unbind("touchend",F).unbind("scroll",B));var o=e(this),u=e.data(this,i);u&&(u[t]=!1),o.unbind(n,q),I(this)||o.removeData(i)}}}var i="virtualMouseBindings",s="virtualTouchID",o="vmouseover vmousedown vmousemove vmouseup vclick vmouseout vmousecancel".split(" "),u="clientX clientY pageX pageY screenX screenY".split(" "),a=e.event.mouseHooks?e.event.mouseHooks.props:[],f=e.event.props.concat(a),l={},c=0,h=0,p=0,d=!1,v=[],m=!1,g=!1,y="addEventListener"in n,b=e(n),w=1,E=0,S;e.vmouse={moveDistanceThreshold:10,clickDistanceThreshold:10,resetTimerDuration:1500};for(var U=0;U<o.length;U++)e.event.special[o[U]]=R(o[U]);y&&n.addEventListener("click",function(t){var n=v.length,r=t.target,i,o,u,a,f,l;if(n){i=t.clientX,o=t.clientY,S=e.vmouse.clickDistanceThreshold,u=r;while(u){for(a=0;a<n;a++){f=v[a],l=0;if(u===r&&Math.abs(f.x-i)<S&&Math.abs(f.y-o)<S||e.data(u,s)===f.touchID){t.preventDefault(),t.stopPropagation();return}}u=u.parentNode}}},!0)})(e,t,n),function(e){e.mobile={}}(e),function(e,t){var r={touch:"ontouchend"in n};e.mobile.support=e.mobile.support||{},e.extend(e.support,r),e.extend(e.mobile.support,r)}(e),function(e,t,r){function l(t,n,r){var i=r.type;r.type=n,e.event.dispatch.call(t,r),r.type=i}var i=e(n);e.each("touchstart touchmove touchend tap taphold swipe swipeleft swiperight scrollstart scrollstop".split(" "),function(t,n){e.fn[n]=function(e){return e?this.bind(n,e):this.trigger(n)},e.attrFn&&(e.attrFn[n]=!0)});var s=e.mobile.support.touch,o="touchmove scroll",u=s?"touchstart":"mousedown",a=s?"touchend":"mouseup",f=s?"touchmove":"mousemove";e.event.special.scrollstart={enabled:!0,setup:function(){function s(e,n){r=n,l(t,r?"scrollstart":"scrollstop",e)}var t=this,n=e(t),r,i;n.bind(o,function(t){if(!e.event.special.scrollstart.enabled)return;r||s(t,!0),clearTimeout(i),i=setTimeout(function(){s(t,!1)},50)})}},e.event.special.tap={tapholdThreshold:750,setup:function(){var t=this,n=e(t);n.bind("vmousedown",function(r){function a(){clearTimeout(u)}function f(){a(),n.unbind("vclick",c).unbind("vmouseup",a),i.unbind("vmousecancel",f)}function c(e){f(),s===e.target&&l(t,"tap",e)}if(r.which&&r.which!==1)return!1;var s=r.target,o=r.originalEvent,u;n.bind("vmouseup",a).bind("vclick",c),i.bind("vmousecancel",f),u=setTimeout(function(){l(t,"taphold",e.Event("taphold",{target:s}))},e.event.special.tap.tapholdThreshold)})}},e.event.special.swipe={scrollSupressionThreshold:30,durationThreshold:1e3,horizontalDistanceThreshold:30,verticalDistanceThreshold:75,start:function(t){var n=t.originalEvent.touches?t.originalEvent.touches[0]:t;return{time:(new Date).getTime(),coords:[n.pageX,n.pageY],origin:e(t.target)}},stop:function(e){var t=e.originalEvent.touches?e.originalEvent.touches[0]:e;return{time:(new Date).getTime(),coords:[t.pageX,t.pageY]}},handleSwipe:function(t,n){n.time-t.time<e.event.special.swipe.durationThreshold&&Math.abs(t.coords[0]-n.coords[0])>e.event.special.swipe.horizontalDistanceThreshold&&Math.abs(t.coords[1]-n.coords[1])<e.event.special.swipe.verticalDistanceThreshold&&t.origin.trigger("swipe").trigger(t.coords[0]>n.coords[0]?"swipeleft":"swiperight")},setup:function(){var t=this,n=e(t);n.bind(u,function(t){function o(t){if(!i)return;s=e.event.special.swipe.stop(t),Math.abs(i.coords[0]-s.coords[0])>e.event.special.swipe.scrollSupressionThreshold&&t.preventDefault()}var i=e.event.special.swipe.start(t),s;n.bind(f,o).one(a,function(){n.unbind(f,o),i&&s&&e.event.special.swipe.handleSwipe(i,s),i=s=r})})}},e.each({scrollstop:"scrollstart",taphold:"tap",swipeleft:"swipe",swiperight:"swipe"},function(t,n){e.event.special[t]={setup:function(){e(this).bind(n,e.noop)}}})}(e,this)});