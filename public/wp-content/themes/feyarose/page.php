<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package feyarose
 */

get_header(); ?>
<!-- FARES: display the title immediately under the header -->
<div class="panel single-page-title <?php echo (is_checkout() || is_cart()) ? 'hidden-xs' : '';  ?>">
    <h1><?php the_title(); ?></h1>
</div>
<div class="container <?php echo (is_checkout() || is_cart()) ? 'checkout-container' : '';  ?>">
    <?php while (have_posts()) : the_post(); ?>
        <div class="row">
            <div class="col col-lg-12 <?php echo (is_checkout() || is_cart())?  'checkout-panel-wrapper': '';  ?>">
                <div class="panel panel-default offset2 span8">
                    <!-- FARES: Check if the current page is woocommerce page -->
                    <?php
                    if (is_checkout() || is_cart()):  ?>
                        <div class="panel-body cart-panel-body">
                            <hr class="rose-holder"/>
                            <div class="cart-panel-body-left">
								<span>
									<?php echo __('Cart', 'woocommerce');  //the_title(); ?>
                                    <span class="hidden-sm hidden-md hidden-lg">&gt;</span>
                                    <br class="hidden-xs">
									<img src="<?php bloginfo('template_directory'); ?>/images/sliced-images/small-rose.png"
                                        alt="rose-romance"/>
								</span>
                            </div>
                            <div class="cart-panel-body-right">
								<span>
                                    <?php echo __('Shipping and payment', 'feyarose'); ?>
                                    <span class="hidden-sm hidden-md hidden-lg">&gt;</span>
                                    <br class="hidden-xs">
									<span class="pink-opacity">
										<img src="<?php bloginfo('template_directory'); ?>/images/sliced-images/rose-pink.png"
                                            alt="rose-pink" class="full-opacity"/></span>
								</span>
                            </div>

                        </div>
                    <?php endif;?>


                </div>
            </div
        </div>

        <div class="row">
            <div class="col col-lg-12 ">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div id="primary" class="content-area">
                            <main id="main" class="site-main" role="main">

                                <?php get_template_part('content', 'page'); ?>
                                <?php the_post_navigation(); ?>

                            </main>
                            <!-- #main -->
                        </div>
                        <!-- #primary -->
                    </div>
                </div>
            </div>
        </div>

    <?php endwhile; // end of the loop. ?>
    <?php get_sidebar(); ?>
</div>
</div><!--EXTRA END OF DIV -->

<?php get_footer(); ?>
