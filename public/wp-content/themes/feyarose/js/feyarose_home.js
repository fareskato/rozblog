/**
 * Created by Florian on 4/29/2015.
 */
(function($) {
    $.browser.device = (/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()));
    $(document).ready(function () {
        if($.browser.device == false) {
            if($('#skrollr-body').length > 0) {
                var s = skrollr.init({
                    smoothScrolling: true
                });
            }
        } else {
            $('body').find('.skrollable-between').each(function () {
                var $this = $(this);
                $this.attr('style', $this.data('0'));
            });
        }

    });
})(jQuery);