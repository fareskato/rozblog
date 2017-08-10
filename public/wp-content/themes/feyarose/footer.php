<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package feyarose
 */
?>

    <div class="container content-inner-footer">
<!--        <span class="feyarose-our-readers-title">--><?php //echo __("Photos of our readers",'feyarose'); ?><!--</span>-->
        <?php dynamic_sidebar( 'content-inner-footer' ); ?>
    </div>
<?php if(is_woocommerce() || is_cart() || is_checkout()) : ?>
<div class="row" style="background: #989191" id="anchor-footer">
    <div class="container">
        <div class="col-xs-12 services-header hidden-sm hidden-md hidden-lg">
            <h3><?php echo _e('Feya rozy is:', 'woothemes') ?></h3>
        </div>
        <div class="col-lg-12 col-xs-12 four-services">
            <div class="col-xs-12 col-sm-3 service-item ">
                <a class="col-xs-2 col-sm-12" href="#anchor-footer" onclick="jQuery('#myModalFooterPremium').modal('show');">
                    <img class="hidden-xs" name=image src=<?php bloginfo('template_directory'); ?>/images/premium.png>
                    <img class="hidden-sm hidden-md hidden-lg" name=image src=<?php bloginfo('template_directory'); ?>/images/premium-brown.png>
                </a>
                <p class="four-services-text col-xs-7 col-xs-offset-1 col-sm-12 col-sm-offset-0">
                    <span class="four-services-uppercase" style="text-transform: uppercase"><?php echo __('excellence', 'woothemes') ?></span><br/>
                    <?php _e('Premium quality<br/>products', 'woothemes') ?><br/>
                </p>
            </div>
            <div class="col-xs-12 col-sm-3 service-item">
                <a class="col-xs-2 col-sm-12 pull-right" href="#anchor-footer"  onclick="jQuery('#myModalFooterDelivery').modal('show');">
                    <img class="hidden-xs" name=image src=<?php bloginfo('template_directory'); ?>/images/delivery.png>
                    <img class="hidden-sm hidden-md hidden-lg" name=image src=<?php bloginfo('template_directory'); ?>/images/delivery-brown.png>
                </a>
                <p class="four-services-text col-xs-7 col-xs-offset-3 col-sm-12 col-sm-offset-0">
                    <span class="four-services-uppercase" style="text-transform: uppercase"><?php echo __('fast', 'woothemes') ?></span><br/>
                    <?php _e('Fast delivery<br/>courier', 'woothemes') ?><br/>
                </p>
            </div>
            <div class="col-xs-12 col-sm-3 service-item" onclick="jQuery('#myModalFooterSecurePayment').modal('show');">
                <a class="col-xs-2 col-sm-12" href="#anchor-footer">
                    <img class="hidden-xs" name=image src=<?php bloginfo('template_directory'); ?>/images/payment.png />
                    <img class="hidden-sm hidden-md hidden-lg" name=image src=<?php bloginfo('template_directory'); ?>/images/payment-brown.png>
                </a>
                <p class="four-services-text col-xs-7 col-xs-offset-1 col-sm-12 col-sm-offset-0">
                    <span class="four-services-uppercase" style="text-transform: uppercase"><?php echo __('secure', 'woothemes') ?></span><br/>
                    <?php _e('Payment online', 'woothemes') ?><br/>
                </p>
            </div>
            <div class="col-xs-12 col-sm-3 service-item">
                <a class="col-xs-2 col-sm-12 pull-right" href="#anchor-footer" onclick="jQuery('#myModalFooterHowWeWork').modal('show');">
                    <img class="hidden-xs" name=image src=<?php bloginfo('template_directory'); ?>/images/terms.png>
                    <img class="hidden-sm hidden-md hidden-lg" name=image src=<?php bloginfo('template_directory'); ?>/images/terms-brown.png>
                </a>
                <p class="four-services-text col-xs-7 col-xs-offset-3 col-sm-12 col-sm-offset-0">
                    <span class="four-services-uppercase" style="text-transform: uppercase">
                        <?php if(ICL_LANGUAGE_CODE == 'en') {
                            echo __('PRODUCER ', 'woothemes');
                        } else {
                            echo __('Manufacturer', 'woothemes');
                        }?>

                    </span><br/>
                    <?php if(ICL_LANGUAGE_CODE != 'en') {
                        _e('All gathered bouquets of roses companies "Rose Fairy"', 'woothemes');
                    } else {
                        _e('All bouquets are made of roses<br />by "Feya rozy"', 'woothemes');
                    } ?>
                    <br/>
                </p>


            </div>

        </div>
    </div>
</div>
<?php endif; ?>
    <div class="jumbotron feyarose-footer-columns">
        <div class="container">
            <!-- Site footer -->
            <footer class="footer" id="colophon" class="site-footer" role="contentinfo">
                <div class="row row-centered hidden-xs">
                    <div class="feyarose-col-footer-1-2 col-lg-6 col-centered content-centered col-sm-6 hidden-xs">
                        <?php dynamic_sidebar( 'footer-col-1-2' ); ?>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-centered">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6  categories-pages">
                                <?php dynamic_sidebar( 'footer-col-3' ); ?>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6  static-pages">
                                <?php dynamic_sidebar( 'footer-col-4' ); ?>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row row-centered">
                    <div class="col-lg-12 col-centered col-fixed content-centered " style="padding: 0">
                        &copy; <?php echo date('Y'); ?> RozBlog. <?php echo __("Website developed by",'feyarose'); ?> <a href="http://www.altima-agency.com/ru" target="_blank">Altima Russia</a>
                    </div>
                </div>
            </footer>
        </div>
    </div>
<?php
//Get the modal codes for the footer popup links of the shop part
$footer_popups = unserialize(get_option('feyarose_footer_popups'));
if(is_array($footer_popups) && (count($footer_popups) > 0) ) {
    foreach($footer_popups as $popupId => $postId) {

        ?>
        <!-- Bootstrap Modal Footer <?php echo $popupId; ?>-->
        <?php
        $post_id = icl_object_id($postId, 'page', true, ICL_LANGUAGE_CODE);
        $popup_page_id = $post_id;
        ?>
        <div class="modal fade" id="myModalFooter<?php echo $popupId; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="entry-title single-post-head popup-header">
                            <?php echo get_the_title($popup_page_id); ?>
                            <button type="button" class="close" data-dismiss="modal"
                                    aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        </h3>
                    </div>
                    <div class="modal-body">
                        <?php
                        if (has_post_thumbnail()) {
                            echo get_the_post_thumbnail($popup_page_id);
                        }
                        ?>
                        <!--  If the page upon  exists display the content   -->
                        <div class="modal-content-row">
                            <?php
                            $contentPage = get_post($popup_page_id);
                            $content = apply_filters('the_content', $contentPage->post_content);
                            echo $content;
                            ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal"><?php _e('Close', 'feyarose'); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- END Bootstrap Modal -->
        <?php
    }
}
?>
<!-- Yandex.Metrika counter --> <script type="text/javascript"> (function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter32492885 = new Ya.Metrika({ id:32492885, clickmap:true, trackLinks:true, accurateTrackBounce:true }); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = "https://mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks"); </script> <noscript><div><img src="https://mc.yandex.ru/watch/32492885" style="position:absolute; left:-9999px;" alt="" /></div></noscript> <!-- /Yandex.Metrika counter -->

<?php wp_footer(); ?>
<!-- W3TC-include-js-head -->
<!-- W3TC-include-css -->
</body>
</html>
