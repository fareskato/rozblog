/**
 *
 * Created by Florian on 5/13/2015.
 */
var disabled_dates = [];
var obj_init = {};
var postcard_inputs = [];
var selected_postcard = '';
var error_inputs = [];
var root_url = (location.hostname.indexOf('devaltimarussia') !== -1) ? '/feyarose' : '';
(function($) {

    function updateMessageCard() {
        for(var i=0;i<postcard_inputs.length;i++) {
            var $input = $('#'+postcard_inputs[i]);
            $input.parent().removeClass('postcard-selected');
            if($input.is(':checked')) {
                selected_postcard = $input.attr('id');
                $input.parent().addClass('postcard-selected');
            }
        }
        var $messageTextField = $('.postcard-message');

        if(selected_postcard !== 'postcard-without' && selected_postcard !== '') {
            $messageTextField.show();
        } else {
            $messageTextField.hide().find('textarea').val('');
        }




    }

    $(document).ready(function () {
        error_inputs = [];
        var data = {};
        data['action'] = 'FeyaroseOrders_Init';
        jQuery( ".product-addon").find('#deliverydate').prop('disabled', true);
        jQuery('#additional_field_154').attr('type', 'tel');

        $('#additional_field_154').intlTelInput({
            onlyCountries: ["ru"],
            utilsScript: root_url + "/wp-content/plugins/feyarose-orders/phone-js/lib/libphonenumber/build/utils.js" // just for formatting/placeholders etc
        });

        jQuery('#billing_phone').attr('type', 'tel');

        $('#billing_phone').intlTelInput({
            onlyCountries: ["ru"],
            utilsScript: root_url + "/wp-content/plugins/feyarose-orders/phone-js/lib/libphonenumber/build/utils.js" // just for formatting/placeholders etc
        });
        //jQuery('#additional_field_686').mask("+7 (999) 999-99-99").attr("value", "+7");
        //jQuery('#billing_phone').mask("+7 (999) 999-99-99").attr("value", "+7");
        $('#billing_phone').attr('placeholder', $('#billing_phone').attr('placeholder') + ': +7 xxx xxx xx xx');
        $('#billing_phone').val('+7 ');
        $('#additional_field_154').attr('placeholder', $('#additional_field_154').attr('placeholder') + ': +7 xxx xxx xx xx');
        $('#additional_field_154').val('+7 ');


        $.ajax({
            dataType: "json",
            url: root_url+'/wp-admin/admin-ajax.php',
            data: data,
            success: function (init) {
                obj_init = init;
                disabled_dates = obj_init['disabled_dates'];
                var d = new Date();
                var n = d.getHours();
                var dateMin = d.getDate();
                if(d.getHours() >= 18 && d.getMinutes() > 0) {
                    dateMin = dateMin + 2;
                } else {
                    dateMin = dateMin + 1;
                }

                jQuery( "#checkout").find('#additional_field_98').prop('disabled', false);
                jQuery( "#checkout").find('#additional_field_98').prop('readonly', true);
                jQuery( "#checkout").find('#additional_field_98').datepicker( {
                    minDate: new Date(d.getFullYear(), d.getMonth(), dateMin),
                    dateFormat: 'yy-mm-dd',
                    maxDate: "+"+obj_init['delay_max']+"d",
                    showOn: "both",
                    buttonImage: obj_init['site_url'] + "/wp-content/plugins/feyarose-orders/calendar.png",
                    buttonImageOnly: true,
                    buttonText: "Select date",
                    beforeShowDay: function(date){
                        var string = jQuery.datepicker.formatDate('yy-mm-dd', date);
                        return [ disabled_dates.indexOf(string) == -1 ]
                    }
                }); // end datepicker
            }
        });  // end ajax
        /*
        Init delivery form
        * */
        //new select for St Pet and Moscow
        var select_town_ru = '<select id="select-town"><option   value="Москва">Москва</option><option value="Санкт-Петербург">Санкт-Петербург</option></select>';
        var select_town_en = '<select id="select-town"><option   value="Moscow">Moscow</option><option value="Saint-Petersburg">Saint-Petersburg</option></select>';
        // switch between the option according to city name
        var mos = $("#wc_checkout_add_ons").find('div').first();
        var san_peter = $("#wc_checkout_add_ons").find('div').first().next();
        $(mos).find('input:radio:first').prop('checked',true);
        $("#additional_field_394").live('change',function(e){
           var current = $('#additional_field_394 option:selected').val();
            //$('#select-town option[name='+current+']').attr("selected", true);
            if(current == "Москва" || current == "Moscow"){

                $(mos).show().removeClass('col-lg-6').addClass('col-lg-12');
                $(san_peter).hide();
                $(mos).find('input:radio:first').prop('checked',true);
                $(san_peter).find("input:radio").removeAttr('checked');
            }
            if(current == "Санкт-Петербург" || current == "Saint-Petersburg"){
                $(san_peter).show().removeClass('col-lg-6').addClass('col-lg-12');
                $(mos).hide();
                //$(san_peter).find('input:radio:first').prop('checked',true);
                $(mos).find("input:radio").removeAttr('checked');
            }
        }).change();
        var placeholders = $('.form-row input:text');
        $(placeholders).each(function(){
            if($(this).attr('placeholder') == 'ФИО'){
               $(this).parent().before("<h3>Получатель</h3>");
            }
        })
        $('input#additional_field_394:lang(ru)').val('Москва').attr('readonly', true);
        $('input#additional_field_394:lang(en)').val('Moscow').attr('readonly', true);
        $('input#deliverytown:lang(ru)').before(select_town_ru);
        $('input#deliverytown:lang(en)').before(select_town_en);
        $('#select-town').on('change',function (evt) {
            $('input#deliverytown').val($(this).val());
        });
        $('input#deliverytown').hide();
        /*
        * Init postcards
        * */
        $('div.postcard').find('.form-row').each(function () {
            var $p = $(this);
            var $label = $p.find('label');
            var $input = $p.find('input');

            postcard_inputs.push($input.attr('id'));
            //$label.css({'background-image':'/wp-content/themes/feyarose/images/message-cards/' + $input.attr('id').replace('postcard-','')+'.jpg'});
            $label.on('click', function () {
                updateMessageCard();
            });
            $input.on('click', function () {
                updateMessageCard();
            });
        });
        updateMessageCard();
        /**Init Error handling */
        $('form.cart').submit(function (evt) {
            $(this).find('.errormessage').remove();
            var fields_required = [];
            fields_required.push($(this).find('#deliverystreet'));
            fields_required.push($(this).find('.deliverydate').find('input[type="text"]'));
            fields_required.push($(this).find('.deliveryperiod').find('select.addon'));
            fields_required.push($(this).find('#deliverybuilding'));
            fields_required.push($(this).find('#recipientname'));
            //fields_required.push($(this).find('#recipientphone'));

            for(var i=0;i<error_inputs.length;i++) {
                var $field = error_inputs[i];
                $field.removeClass('warning');
            }
            error_inputs = [];

            for(var i=0;i<fields_required.length;i++) {
                var $field = fields_required[i];
                if($field.length > 0) {
                    //console.log($field.attr('id') + ', ' + $field.val());
                    if ($field.val() == '') {
                        $field.addClass('warning');
                        error_inputs.push($field);
                    }
                }

            }
            /*
            if(!$('#recipientphone').intlTelInput("isValidNumber")) {
                $('#recipientphone').addClass('warning');
                error_inputs.push($(this).find('#recipientphone'));
            }
            */
            window.setTimeout(function () {
                jQuery('.single_add_to_cart_button').removeAttr('disabled');
            }, 500);

            if(error_inputs.length > 0) {
                $('.single_add_to_cart_button').removeAttr('disabled');
                //display error message
                var errormsg = '<div class="errormessage">' +
                    '<ul class="woocommerce-error"><li class="msg-error">Пожалуйста, заполните все поля, выделенные красным.</li></ul>' +
                    '</div>';
                var errormsgEn = '<div class="errormessage">' +
                    '<ul class="woocommerce-error"><li class="msg-error">Please complete all fields marked red.</li></ul>' +
                    '</div>';
                var off = jQuery(this).offset();
                jQuery(document).scrollTop(off.top - 160);
                $(this).find('div.deliverydate:lang(en)').before(errormsgEn);
                $(this).find('div.deliverydate:lang(ru)').before(errormsg);

                return false;
            }

            return true;
        });

    });  // end ready
})(jQuery);

