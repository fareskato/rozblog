
(function($) {
    $(document).ready(function () {
        var root_url = (location.hostname.indexOf('devaltimarussia') !== -1) ? '/feyarose' : '';

        $('#billing_phone').intlTelInput({
            onlyCountries: ["ru"],
            utilsScript: root_url + "/wp-content/plugins/feyarose-orders/phone-js/lib/libphonenumber/build/utils.js" // just for formatting/placeholders etc
        });
        //$('#billing_phone').attr('placeholder', '+7 xxx xxx xx xx');
        if($('#billing_phone').val() == '') {
            $('#billing_phone').val('+7 ');
        }


    });
})(jQuery);