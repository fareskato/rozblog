<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package feyarose
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
    <link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/images/favicon.png"/>
    <?php wp_head(); ?>

	<script type="text/javascript">
		if ( window.screen.width <= 768) {
			document.write('<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">');
		} else {
			document.write('<meta id="viewport" name="viewport" content="width=1024">');
		}
	</script>

    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
            n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
            document,'script','https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '335733539884273'); // Insert your pixel ID here.
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
                   src="https://www.facebook.com/tr?id=335733539884273&ev=PageView&noscript=1"
            /></noscript>
    <!-- DO NOT MODIFY -->
    <!-- End Facebook Pixel Code -->
</head>

<!--
---------------------------------------
  Bootstrap Modal popup
---------------------------------------
-->
<body <?php body_class();?>>
<?php
$blog_page_id = icl_object_id(46, 'page', true, ICL_LANGUAGE_CODE);
$site_link=(is_woocommerce() || is_cart() || is_checkout() || is_home()) ? get_permalink($blog_page_id) : get_home_url();

?>

<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-5DW57B"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-5DW57B');</script>
<!-- End Google Tag Manager -->
<!--
---------------------------------------
  Feyarose Product(bouquet) Details Modal
---------------------------------------
-->
<body <?php body_class(); ?>>
<div class="RoseProductmodal col-lg-12" id="RoseModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
    <div class="modal-dialog " role="document" >
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <!-- Bouquet details go here! -->
            </div>
            <div class="modal-footer hidden-xs">

            </div>
        </div>
    </div>
</div>
<!--  End Feyarose Product(bouquet) Details Modal  -->
<div class="container feyarose-header ">
    <div class="header-item-wrapper container">
        <div class="header-item-wrapper-center col-lg-12">
            <!--  Check if the page is exists  -->
            <?php

            ?>
            <?php
            $feyarose_settings = unserialize(get_option('feyarose_settings'));
            $popup_id = $feyarose_settings['popup_id'];

            if (!isset($_COOKIE['feyarose_welcome'])):
                try {
                    //setcookie("feyarose_welcome", true, time() + 12 * 60 * 60);
                } catch (Exception $e) {

                }

                $popup_page_id = icl_object_id($popup_id, 'page', true, ICL_LANGUAGE_CODE);
                if (get_post($popup_page_id)) : ?>

<?php
                    // Check if the page status is publish
                    if (get_post_status($popup_page_id) == 'publish') :
                        ?>
                        <!-- Bootstrap Modal -->
                        <div class="modal fade feyarose-welcome-popup" id="myModal"  >
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
                                        if (has_post_thumbnail()) :
                                            echo get_the_post_thumbnail($popup_page_id);
                                        endif;
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
                    <?php endif; ?>
                <?php else:
                    setcookie("feyarose", false, (time() + 12 * 60 * 60) * (-1));
                endif; ?>
            <?php endif; ?> <!-- End of cookie -->
            <!-- End bootstrap modal popup -->
            <div class=" col-xs-9 col-xs-offset-0 col-lg-2 col-lg-offset-5 col-sm-10 col-sm-offset-0 logo-head" style="padding-top: 15px"></div>
            <div class=" lang col-xs-3  col-sm-2 col-sm-offset-0 col-lg-2 col-lg-offset-3">
                <?php language_selector_flags(); ?>
            </div>
        </div>
    </div>
<?php
  //  $quality_target_id = icl_object_id(16372, 'post', true, ICL_LANGUAGE_CODE);
?>
    <div class="container four-items-wrapper">
        <div class="four-items-wrapper-center col-xs-12">
            <div class="col-sm-6 col-xs-6 col-lg-5 col-md-8">
                <div class=" col-xs-12 col-lg-6 col-sm-6 col-md-4  delivery-menu-header">
                    <h1 class="site-title col-xs-pull-6">
                        <a class="navbar-brand" href="<?php echo get_home_url(); ?>"
                           rel="home"><?php bloginfo('name'); ?>
                        </a>
                        <a href="#" onclick="jQuery('#myModalFooterQuality').modal('show');" class="quality"><img src="<?php bloginfo('template_directory'); ?>/images/qualite.png" alt=""></a>
                    </h1>
                </div>
                <div class="textwidget-container col-lg-6 col-sm-5 col-sm-offset-1 col-xs-12 col-md-4 col-md-offset-0 ">
                <span class="header-phone hidden-xs">
                    <img src=<?php bloginfo('template_directory'); ?>/images/Telephone2.png alt="logo"/>
                </span>
                    <?php dynamic_sidebar('header-contacts'); ?>
                </div>
            </div>
            <div class="col-lg-5 col-lg-offset-2 col-sm-6 col-xs-6 col-md-4"  style="padding-left: 0" >

                <div class="delivery-part col-lg-4 col-md-4 hidden-xs hidden-sm">
                <span class="gift" style="display: inline-block;margin-left: 30%;cursor:pointer;" onclick="jQuery('#myModalFooterDelivery').modal('show');">
                    <img src=<?php bloginfo('template_directory'); ?>/images/gift.png alt="logo"/>
                </span>
                    <p class="delivery-text" onclick="jQuery('#myModalFooterDelivery').modal('show');" style="cursor:pointer;"><?php _e('Free Delivery', 'woothemes'); ?>*</p>
                </div>
                <div class="col-lg-4 col-xs-6  col-sm-6  woo-user-icon" style="padding-left: 0">
                    <?php if (is_user_logged_in()) { ?>
                        <a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>"
                           title="<?php _e('My Account', 'woothemes'); ?>"><span class="header-user-icon">
                    <img src=<?php bloginfo('template_directory') ?>/images/user.png alt="user"/>
                </span><span class="account-text" style="font-size: 13px;"><?php _e('My Account', 'woothemes'); ?></span></a>
                    <?php } else { ?>
                        <a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>"
                           title="<?php _e('Login / Register', 'woothemes'); ?>"><span class="header-user-icon">
                    <img src=<?php bloginfo('template_directory') ?>/images/user.png alt="user"/>
                </span><span class="account-text hidden-sm hidden-md" style="font-size: 13px;"><?php _e('Login / Register', 'woothemes'); ?><span class="account-text"></a>
                    <?php } ?>
                </div>
                <div class=" col-lg-4 col-xs-6  col-sm-6 woo-cart-wrapper">
                    <a class="cart-contents" id="cart-element" href="<?php echo WC()->cart->get_cart_url(); ?>"
                       title="<?php _e('My Cart'); ?>">
                        <span class="shopping-cart-icon">
                            <img src=<?php bloginfo('template_directory') ?>/images/cart.png alt="cart"/>
                        </span>
                        <span class="cart-items-num"><?php //echo sprintf(_n('%d ', '%d ', WC()->cart->cart_contents_count), WC()->cart->cart_contents_count); ?></span><span class="cart-icon-text hidden-sm hidden-md"><?php _e('My Cart', 'woothemes') ?></span>
                        <?php dynamic_sidebar('top-menu-left'); ?>
                    </a>

                </div>
            </div>

        </div>
    </div>
    <div class="container masthead-wrapper">
        <header id="masthead" class="site-header col-lg-12" role="banner">
            <nav class="navbar navbar-default feyarose-navbar-header">
                
                <!--                <div class="container-fluid feyarose-navbar-container">-->
                <div class="col-lg-5  col-xs-12 col-sm-7 nav-sm-center col-md-5">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse" style="float: left !important;">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>

                    <?php wp_nav_menu(array('theme_location' => 'top-right-menu', 'menu_class' => 'nav navbar-nav  feyarose-mainmenu', 'container' => 'div', 'container_class' => 'navbar-collapse collapse')); ?>
                </div>
                <div class="site-logo hidden-xs hidden-sm col-md-2 col-lg-2 ">
                    <a href="<?php echo $site_link; ?>"><img name=image src=<?php bloginfo('template_directory'); ?>/images/toshop.png></a>
                </div>

                <div class="col-lg-5 col-sm-5 col-md-5 col-lg-offset-0 hidden-xs link-to-magazine" style="line-height: 5em">
                    <!--  buy bouquet mix -->
                                             <!-- 12610 || on the dev server 12827  -->
                    <?php
                    $shop_link = get_home_url();
                    $is_shop_page = false;
                    $current_page = get_queried_object();
                    if(is_object($current_page) && property_exists($current_page, 'ID')) {
                        if($current_page->ID == icl_object_id(12610, 'page', true, ICL_LANGUAGE_CODE)) {
                            $is_shop_page = true;
                            $shop_link = '';
                        }
                    }

                    ?>
                    <?php //(icl_link_to_element(12610, 'page', strtoupper(__('buy bouquet mix','woothemes')))); ?>
                    <a href="<?php echo $shop_link.'#bouquets'; ?>" <?php echo ($is_shop_page == true) ? 'class="link-anchor"' : ''; ?>><?php echo strtoupper(__('buy bouquet mix','woothemes')); ?></a>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="<?php echo $shop_link.'#roses'; ?>" <?php echo ($is_shop_page == true) ? 'class="link-anchor"' : ''; ?>><?php echo strtoupper(__('compose your bouquet','woothemes')); ?></a>


                </div>
            </nav>

        </header>
        <!-- #masthead -->
    </div>
</div>


<script type="text/javascript">
    //<![CDATA[
    jQuery(document).ready(function () {
        function setCookie(c_name,value)
        {
            document.cookie=c_name + "=" + value + '; path=/';
        }
        function getCookie(c_name)
        {
            var c_value = document.cookie;
            var c_start = c_value.indexOf(" " + c_name + "=");
            if (c_start == -1) {
                c_start = c_value.indexOf(c_name + "=");
            }
            if (c_start == -1) {
                c_value = null;
            } else {
                c_start = c_value.indexOf("=", c_start) + 1;
                var c_end = c_value.indexOf(";", c_start);
                if (c_end == -1) {
                    c_end = c_value.length;
                }
                c_value = unescape(c_value.substring(c_start,c_end));
            }
            return c_value;
        }
        setCookie('count_wp', parseInt(getCookie('count_wp')) ? parseInt(getCookie('count_wp'))+1 : 1)
//        console.log(jQuery.cookie("count_wp"));

        if(jQuery.cookie("count_wp") > 1)
        {
            jQuery("#myModal").addClass('hideModal');
        }



    });
    //]]>
</script>