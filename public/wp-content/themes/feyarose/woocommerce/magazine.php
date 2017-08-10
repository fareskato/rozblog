<?php
/*
 * Display the part of shop page under all products carousel
 * */
?>
<div class="row under-up-carousel">
    <div class=" under-carousel">
        <h3><?php echo strtoupper(__('our products', 'woothemes')) ?></h3>

        <div class=" col-lg-12 two-titles">
            <div class="col-lg-3"></div>
            <div class="one-product-type col-lg-6 col-xs-12">
                <ul>
                    <li><a href="#bouquets" id ='choose-bouquet' class="link-anchor"><?php echo strtoupper(__('bouquet mix', 'woothemes')) ?></a></li>
                    <li><a href="#roses" id="create-bouquet" class="link-anchor"><?php echo strtoupper(__('compose your bouquet', 'woothemes')) ?></a></li>
                </ul>
            </div>
            <div class="col-lg-3"></div>
        </div>
    </div>

    <h3 class="bouquet-mix-head" id="bouquets"><?php echo strtoupper(__('bouquet mix', 'woothemes')) ?></h3>

    <p class="col-lg-6 bouquet-mix-text hidden-xs">
        <?php
        echo __('Mix of colors, forms and aromas in the bouquets made of Feya rozy’s unique roses. Infinitely fragrant types create an united perfume composition. The irreproachable style of these bouquets gives the impression that roses have been recently cut off in the garden. Each combination is a work of art and an invitation to cheerfulness.');
        ?>
    </p>
</div>
<div class="row">
    <!-- Refer to header.php to see the bouquet bootstrap modal(popup) which contains the bouquet details -->
    <?php
    /*
    * @author Fares
    * Display the bouquet carousel
    **/
    $params = array(
        'post_type' => 'product',
        'product_cat' => 'Букет,Букеты,bouquet',
    );
    $the_query = new WP_Query($params);
    ?>
    <?php if (have_posts()) : ?>
    <?php  $arr = $the_query->posts; ?>
    <script type="text/javascript">
        //<![CDATA[
        var bouquetImages = {};
        //]]>
    </script>
    <div class="col-lg-12 bouquet-carousel-wrapper">
        <div class="carousel multi-item-carousel" id="theCarousel" style="background-color:#ced2d1;">
            <div class="carousel-inner bouquets-carousel-inner">


                <?php while ($the_query->have_posts()): $the_query->the_post(); ?>
                <?php
                global $product;
                global $post;
                $attachment_ids = $product->get_gallery_attachment_ids(); ?>
                    <div class="item bouquet-item <?php if ($the_query->current_post == 0): ?>active<?php endif; ?>">
                        <?php
                        $product_id = $the_query->post->ID ;
                        $thumbnail_id = get_post_thumbnail_id();
                        $thumbnail_url = wp_get_attachment_image_src($thumbnail_id, 'small-listing-image', true);
                        ?>
                        <div class="col-xs-12 col-sm-4 one-bouquet" style="text-align: center;">
                            <script type="text/javascript">
                                //<![CDATA[

                                bouquetImages[<?php echo $product_id; ?>] = {
                                    images: []
                                };
                                bouquetImages[<?php echo $product_id; ?>].images.push("<?php echo $thumbnail_url[0] ; ?>");
                                //]]>
                            </script>

                            <figure>
                                <img class="<?php echo $thumbnail_id ;?>" src="<?php echo $thumbnail_url[0]; ?>" data-thumbnail="<?php echo $thumbnail_id;  ?>" data-post_name="<?php the_title() ?>"  data-id="<?php echo  $product_id ;?>" data-toggle="RoseProductmodal"  data-target="#RoseModal" style="cursor: pointer;">
                                <figcaption>
                                    <span class="post_name" style="display: none"><?php echo $the_query->post->post_name ?></span>
                                    <h4 class="bouq-slide-title"><?php the_title(); ?></h4>
                                    <p class="bouq-slide-content">
                                        <?php echo feyarose_bouquet_excerpt(10,'...',$product_id); ?>
                                    </p>
                                    <?php
                                        if(count($attachment_ids) > 0):
                                            $productId = $the_query->post->ID ;
                                            ?>


                                        <?php foreach($attachment_ids as $one_gallery_img) :
                                        $one_gallery_img_path = wp_get_attachment_url( $one_gallery_img );
                                        ?>
                                            <script type="text/javascript">
                                                //<![CDATA[
                                                bouquetImages[<?php echo $productId; ?>].images.push("<?php echo $one_gallery_img_path ; ?>");
                                                //]]>
                                            </script>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <p class="bouq-slide-price">
                                        <?php woocommerce_template_single_add_to_cart(); ?>
                                    </p>
                                    <script type="text/javascript">
                                        //<![CDATA[
                                        jQuery('figcaption').find('.cart').find('.product-addon, .quantity, .buttons_added, .single_add_to_cart_button').hide();
                                        //]]>
                                    </script>
                                    <p>
                                        <a href="#" class="order-from-magazine btn btn-primary btn-lg" data-thumbnail="<?php echo $thumbnail_id;  ?>" data-post_name="<?php the_title() ?>"  data-id="<?php echo  $product_id ;?>" data-toggle="RoseProductmodal"  data-target="#RoseModal"><?php echo strtoupper(__('order', 'woothemes')) ?></a>
                                    </p>
                                </figcaption>
                            </figure>
                        </div>

                        <div id="wpb_wl_quick_view_<?php echo get_the_id(); ?>"  class=" mfp-hide mfp-with-anim wpb_wl_quick_view_content wpb_wl_clearfix wc-product-popup" style="display: none;">
                            <div class="wpb_wl_images  hidden-xs">
                                <?php
                                if ( has_post_thumbnail() ) {
                                    $caption = get_post( get_post_thumbnail_id() )->post_excerpt;
                                    $image_title = esc_attr( get_the_title( get_post_thumbnail_id() ) );
                                    $image_link  = wp_get_attachment_url( get_post_thumbnail_id() );
                                    $image       = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
                                        'title' => $image_title,
                                        'class' => ' attachment-shop_single wp-post-image img-flytocart '
                                    ) );
                                    $attachment_count = count( $product->get_gallery_attachment_ids() );
                                    if ( $attachment_count > 0 ) {
                                        $gallery = '[product-gallery]';
                                    } else {
                                        $gallery = '';
                                    }
                                    echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" itemprop="image" class="woocommerce-main-image zoom" title="%s" data-rel="prettyPhoto' . $gallery . '">%s<span class="product_caption">'.$caption.'</span></a>', $image_link, $image_title, $image ), $post->ID );

                                } else {
                                    echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="%s" />', wc_placeholder_img_src(), __( 'Placeholder', 'woocommerce-lightbox' ) ), $post->ID );
                                }
                                ?>
                                <!-- Display product gallery -->
                                <div class="bouquet-gallery col-sm-12">
                                    <?php
                                    global $product;
                                    $attachment_ids = $product->get_gallery_attachment_ids();
                                    if(count($attachment_ids) > 0) {
                                        echo "<div class='one-bouquet-gallery-image col-xs-4'  >".apply_filters( 'woocommerce_single_product_image_html', sprintf( '%s', $image ), $post->ID )."</div>";
                                    }
                                    foreach( $attachment_ids as $attachment_id )
                                    {
                                        //$image_link = wp_get_attachment_url( $attachment_id );
                                        $image_link = wp_get_attachment_image( $attachment_id, 'single_product_large_thumbnail_size' );
                                        echo "<div class='one-bouquet-gallery-image col-xs-4'  >".$image_link."</div>";
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="wpb_wl_summary summary entry-summary ">
                                <!-- Product Title -->
                                <?php $pptitle = get_the_title(); ?>
                                <div class="col-lg-6 col-lg-offset-3 col-xs-12" style="padding: 0"><h2 class="wpb_wl_product_title"><span><?php echo __('Your ', 'feyarose'); ?><?php echo $pptitle; ?></span></h2></div><div class="clearfix"></div>
                                <!-- Product description -->
                                <div class="bouquet-popup-description hidden-xs">
                                    <?php the_content(); ?>
                                </div>
                                <!-- Product short description -->
                                <?php// woocommerce_template_single_excerpt();?>
                                <!-- Product cart link -->
                                <div class="col-xs-12" style="padding-left: 0"><?php woocommerce_template_single_add_to_cart();?></div>
                                <div class="col-xs-12" style="padding-left: 0; margin-bottom: 25px">
                                    <div class="mkad-free" style="width: 200px; text-align: left;"><a href="#" style="font-size: 13px;" onclick="jQuery('#myModalFooterDelivery').modal('show');"><?php echo __('Free delivery in MKAD','feyarose'); ?>&nbsp;<i class="fa fa-info-circle"></i></a></div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>

                    <?php
                endwhile;
                ?>

            </div>
            <a class="left carousel-control " href="#theCarousel" data-slide="prev"><i class="glyphicon glyphicon-chevron-left hidden-xs"></i></a>
            <a class="right carousel-control" href="#theCarousel" data-slide="next"><i class="glyphicon glyphicon-chevron-right hidden-xs"></i></a>
        </div>
    </div>
    <?php endif;
    //    wp_reset_query();
    ?>
</div>

<?php
$args = array(
    'post_type' => 'product',
    'product_cat' => 'roz,rose,Розы, Roses',
    'posts_per_page' => '-1'
);
$the_query = new WP_Query($args);
if($the_query->have_posts()) { //if we have published roses
    ?>

    <div class="row">

        <div class="col-lg-12 compose-bouquet">
        </div>

        <h3 id="roses" class="bouquet-mix-head"><?php echo strtoupper(__('compose your bouquet', 'woothemes')) ?></h3>
        <p class="col-lg-6 bouquet-mix-text hidden-xs">
            <?php
            echo __('You can here take a walk as if you were in a beautiful garden and gather your unique bouquet. Here grow many fragrant roses from the most different types that  you can select. Your bouquet can consist of any quantity of roses of different aromas and colors or be monochromatic. But  remember: each rose grows in its own time, don’t miss your favorite.');
            ?>
        </p>

        <!--  Display message if there are less than 12 roses in the cart  -->
        <div class="col-lg-6 add-rose-msg"><p id="twelve-roses" class='twelve-roses-message'><?php echo less_than_twelve_rose(); ?></p></div>
    </div>
    <div class="row">
        <div class="col-lg-12 rose-list-wrapper">
            <?php
            $count_loop = 1;
            while ($the_query->have_posts()) : $the_query->the_post();
                global $product;
                //get the tags of the russian version
                $item_rose_original_id = icl_object_id($the_query->post->ID, 'product', true, 'ru');
                $rose_tags=  get_the_terms( $item_rose_original_id, 'product_tag' );
                ?>
                <?php if($count_loop == 4): ?>
                    <div class="col-lg-4 col-xs-12 col-sm-6 col-md-4 magazine-one-rose fragrance-precious-element ">
                        <div class="element-wrapper row">
                            <div class="element">
                                <div class="col-lg-12 col-xs-12 element-img">
                                    <img src="<?php bloginfo('template_directory'); ?>/images/frag-big.png" alt=""/>
                                </div>
                                <div class="col-lg-12 col-xs-12 element-head">
                                    <h3><span><?php echo __('Fragrant rose','woothemes') ?></span></h3>
                                </div>
                                <div class="col-lg-10 col-lg-offset-1 element-text">
                                    <p><?php echo __('Some roses have such a strong aroma  that they can fill you with peace thanks to their special invigorating force. Such types of roses are noted by pictogram “fragrant rose”.','woothemes') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="col-lg-4 col-xs-12 col-sm-6 col-md-4 magazine-one-rose <?php echo $the_query->post->ID ?>">
                    <?php

                    $nb_images = (has_post_thumbnail($the_query->post->ID)) ? 1 : 0;
                    $attachment_ids = $product->get_gallery_attachment_ids();
                    $count_img_rose = 0;
                    $image_link = "";
                    if(count($attachment_ids) > 0) {
                        foreach( $attachment_ids as $attachment_id )
                        {
                            if($count_img_rose == 0) {
                                $image_link .= wp_get_attachment_image( $attachment_id, 'rose-list-size');
                                $count_img_rose++;
                            }


                        }
                    }
                    $nb_images = $nb_images + $count_img_rose;

                    ?>

                        <div class="flower-list-item row nb-img-<?php echo $nb_images; ?>" style="background: #fff; text-align: center;">
                            <h4><?php the_title(); ?></h4>
                            <!--                    <a href="--><?php //echo get_permalink($the_query->post->ID) ?><!--"-->
                            <!--                       title="--><?php //echo esc_attr($the_query->post->post_title ? $the_query->post->post_title : $the_query->post->ID); ?><!--">-->
                            <!--                        --><?php //woocommerce_show_product_sale_flash($post, $product); ?>
                            <!--                    </a>-->
                            <div class="flower-item-content">
                                <div class="rose-desc-wrapper">
                                    <?php
                                    if (has_post_thumbnail($the_query->post->ID)) {
                                        echo get_the_post_thumbnail($the_query->post->ID, 'rose-list-size', array('class'=>' attachment-rose-list-size wp-post-image img-flytocart ') );
                                    }else{
                                        echo '<img src="' . woocommerce_placeholder_img_src() . '" />';
                                    }
                                    ?>
                                    <?php
                                    echo $image_link;
                                    ?>
                                    <div class="single-rose-desc hidden-xs hidden-sm"><div class="desc-table"><?php the_content(); ?></div></div>
                                </div>
                            <div class="from-price"><?php //_e('From','woothemes'); ?></div>
                            <div class="price"><?php echo $product->get_price_html(); ?></div>
                            <div class="rose-tags col-xs-12">
                                <?php if($rose_tags): ?>
                                    <ul class="rose-list-tags">
                                        <?php foreach($rose_tags as $rose_tag) : ?>
                                            <?php $tag_name = $rose_tag->name;?>
                                            <li class="<?php echo $tag_name ?>"> </li>
                                        <?php endforeach ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                            <div class="rose-list-add-to-cart col-xs-12 " id="<?php echo $the_query->post->ID ?>">
                                <?php woocommerce_template_single_add_to_cart();?>
                            </div>

                        </div>
                    </div>

                </div>
                <?php if($count_loop == 7): ?>
                    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4 magazine-one-rose fragrance-precious-element">
                        <div class="element-wrapper row">
                            <div class="element">
                                <div class="col-xs-12 element-img">
                                    <img src="<?php bloginfo('template_directory'); ?>/images/prec-big.png" alt=""/>
                                </div>
                                <div class="col-xs-12 element-head">
                                    <h3><span><?php echo __('Rare rose','woothemes') ?></span></h3>
                                </div>
                                <div class="col-lg-10 col-lg-offset-1 element-text">
                                    <p><?php echo __('The most refined and unique types of roses, specially selected by company “Feya rozy”. As if on the beckoning of a magic wand, these roses within few hours could be in your hands! Search for pictogram “rare rose”','woothemes') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php
                $count_loop++;
            endwhile; ?>
            <?php wp_reset_query(); ?>
            <!--products-->
        </div>
    </div>
    <?php
}//end if we have published roses
?>
<div class="adding-msg" style="display:none;"><?php print (ICL_LANGUAGE_CODE == 'en') ? 'Please wait' : 'Добавляем'; ?></div>
<?php
$shortcode_instagram = stripslashes(get_option('feyarose_instagram_shortcode', null));
if($shortcode_instagram != null) {

    ?>
<!--  Companies  -->
    <div class="row">
        <h3 class="bouquet-mix-head"><?php echo __('Our clients', 'woothemes') ?></h3>
        <div class="owl-carousel owl-theme owl-loaded clients no-padding">
                <img src="<?php bloginfo('template_directory'); ?>/images/clients/chanel.jpg" alt=""/>
                <img src="<?php bloginfo('template_directory'); ?>/images/clients/clarins.jpg" alt=""/>
                <img src="<?php bloginfo('template_directory'); ?>/images/clients/dior.jpg" alt=""/>
                <img src="<?php bloginfo('template_directory'); ?>/images/clients/edf.jpg" alt=""/>
                <img src="<?php bloginfo('template_directory'); ?>/images/clients/guerlain.jpg" alt=""/>
                <img src="<?php bloginfo('template_directory'); ?>/images/clients/itar_tass.jpg" alt=""/>
                <img src="<?php bloginfo('template_directory'); ?>/images/clients/lamaree.jpg" alt=""/>
                <img src="<?php bloginfo('template_directory'); ?>/images/clients/lladro.jpg" alt=""/>
                <img src="<?php bloginfo('template_directory'); ?>/images/clients/mazars.jpg" alt=""/>
                <img src="<?php bloginfo('template_directory'); ?>/images/clients/piaget.jpg" alt=""/>
                <img src="<?php bloginfo('template_directory'); ?>/images/clients/vtb.jpg" alt=""/>
        </div>
    </div>


    <div class="row">
        <h3 class="bouquet-mix-head"><?php echo strtoupper(__('follow us !', 'woothemes')) ?></h3>
    </div>
    <div class="row">
        <div class="col-lg-12 inastagram-footer">
            <?php
            feyarose_get_instagram();
            echo do_shortcode($shortcode_instagram);
            ?>
        </div>
    </div>
    <?php
}

?>
<!-- Socials in mobile -->
<div class="row">
    <div class="col-xs-12 mobile-socials-wrapper hidden-sm hidden-md hidden-lg">
        <div class="col-xs-11 mobile-socials-container">
            <div class="col-xs-6 mobile-socials-text">
                <p><?php echo __('And also follow us in:', 'woothemes') ?></p>
            </div>
            <div class="col-xs-1 vkontakti">
                <a href="https://new.vk.com/feyarozy" target="_blank"><img src="<?php bloginfo('template_directory'); ?>/images/VK-Logo-EPS-vector-image1.png" alt=""></a>
            </div>
            <div class="col-xs-1"></div>
            <div class="col-xs-1 faceBook">
                <a href="https://www.facebook.com/pages/%D0%A4%D0%B5%D1%8F-%D1%80%D0%BE%D0%B7%D1%8B-fragrantroseru/157352240943901" target="_blank"><img src="<?php bloginfo('template_directory'); ?>/images/fb-art.jpg" alt=""></a>
            </div>
        </div>
    </div>
</div>
