<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive.
 *
 * Override this template by copying it to yourtheme/woocommerce/archive-product.php
 *
 * @author        WooThemes
 * @package    WooCommerce/Templates
 * @version     2.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
get_header('shop'); ?>

<?php
/**
 * woocommerce_before_main_content hook
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 */
do_action('woocommerce_before_main_content');
?>
<?php if (apply_filters('woocommerce_show_page_title', true)) : ?>
    <h1 class="page-title"><?php //woocommerce_page_title(); ?></h1>
<?php endif; ?>
<?php //do_action('woocommerce_archive_description'); ?>
<?php
/* Display custom post type(roses) carousel */
$params = array(
    'post_type' => 'shop_carouse'
);
$the_query = new WP_Query($params);
?>
<?php if ($the_query->have_posts()) : ?>
<!-- Carousel start -->
<div id="feyarose-products-carousel" class="carousel slide" data-ride="carousel" data-interval="8000" >
    <!-- Indicators -->
    <ol class="carousel-indicators hidden-xs">
        <?php if (have_posts()) : while ($the_query->have_posts()): $the_query->the_post(); ?>
            <li data-target="#feyarose-products-carousel" data-slide-to="<?php echo $the_query->current_post; ?>"
                class="<?php if ($the_query->current_post == 0): ?>active<?php endif; ?>"></li>
        <?php endwhile; endif; ?>
    </ol>
    <?php rewind_posts(); ?>
    <!-- Wrapper for slides -->
    <div class="carousel-inner all-products-carousel-inner" role="listbox">
        <?php if (have_posts()) : ?>
            <?php woocommerce_product_subcategories(); ?>
            <?php while ($the_query->have_posts()): $the_query->the_post(); ?>
                <div class="item <?php if ($the_query->current_post == 0): ?>active<?php endif; ?>">
                    <?php
                    $thumbnail_id = get_post_thumbnail_id();
                    $thumbnail_url = wp_get_attachment_image_src($thumbnail_id, 'products-carousel', true);
                    $thumbnail_meta = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
                    ?>
                    <img src="<?php echo $thumbnail_url[0]; ?>" alt="<?php echo $thumbnail_meta; ?>" style="width: 100%;">

                    <div class="carousel-caption all-products-carousel-caption flex">
                        <div class="caption-table">
                            <div class="caption-table-center-wrapper">
                                <div class="caption-table-center">
                                    <h2><?php the_title(); ?></h2>
                                    <div class="allproducts-carousel-item-content">
                                        <?php echo get_the_content(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; endif; ?>
    </div>
    <!-- Controls -->
    <a class="left carousel-control" href="#feyarose-products-carousel" role="button" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="right carousel-control" href="#feyarose-products-carousel" role="button" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>
<?php endif; ?>
<!-- Carousel end -->
<?php if (have_posts()) : ?>
    <?php
    /**
     * woocommerce_before_shop_loop hook
     *
     * @hooked woocommerce_result_count - 20
     * @hooked woocommerce_catalog_ordering - 30
     */
    do_action( 'woocommerce_before_shop_loop' );
    ?>
<?php endif; ?>

<?php
/* get the magazine page */
     wc_get_template('magazine.php');
?>
<?php
/**
 * woocommerce_after_main_content hook
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action('woocommerce_after_main_content');
?>
<?php
/**
 * woocommerce_sidebar hook
 *
 * @hooked woocommerce_get_sidebar - 10
 */
do_action('woocommerce_sidebar');
?>

<?php get_footer('shop'); ?>
