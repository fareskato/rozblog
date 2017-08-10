<?php
/**
 * feyarose functions and definitions
 *
 * @package feyarose
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if (!isset($content_width)) {
    $content_width = 640; /* pixels */
}

if (!function_exists('feyarose_setup')) :
    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which
     * runs before the init hook. The init hook is too late for some features, such
     * as indicating support for post thumbnails.
     */
    function feyarose_setup()
    {

        /*
         * Make theme available for translation.
         * Translations can be filed in the /languages/ directory.
         * If you're building a theme based on feyarose, use a find and replace
         * to change 'feyarose' to the name of your theme in all the template files
         */
        load_theme_textdomain('feyarose', get_template_directory() . '/languages');

        // Add default posts and comments RSS feed links to head.
        add_theme_support('automatic-feed-links');

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support('title-tag');

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
         */
        //add_theme_support( 'post-thumbnails' );

        // This theme uses wp_nav_menu() in one location.
        register_nav_menus(array(
            'primary' => __('Primary Menu', 'feyarose'),
        ));

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ));

        /*
         * Enable support for Post Formats.
         * See http://codex.wordpress.org/Post_Formats
         */
        add_theme_support('post-formats', array(
            'aside',
            'image',
            'video',
            'quote',
            'link',
        ));

        // Set up the WordPress core custom background feature.
        add_theme_support('custom-background', apply_filters('feyarose_custom_background_args', array(
            'default-color' => 'ffffff',
            'default-image' => '',
        )));
    }
endif; // feyarose_setup
add_action('after_setup_theme', 'feyarose_setup');

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function feyarose_widgets_init()
{
    register_sidebar(array(
        'name' => __('Sidebar', 'feyarose'),
        'id' => 'sidebar-1',
        'description' => '',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<h1 class="widget-title">',
        'after_title' => '</h1>',
    ));

    /*  Fares
     *  Category Form Widget area
     * */

    register_sidebar(array(
        'name' => __('Category Form Sidebar', 'feyarose'),
        'id' => 'cat-form-sidebar',
        'description' => '',
        'before_widget' => '<aside class="cat-form">',
        'after_widget' => '</aside>',
        'before_title' => '<p class="cat-form-title">',
        'after_title' => '</p>',
    ));

    /*
     * Top widgets area
     * */
    register_sidebar(array(
        'name' => __('Language selector', 'feyarose'),
        'id' => 'language-selector',
        'description' => 'The language selector links',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => ''
    ));
    register_sidebar(array(
        'name' => __('Search area', 'feyarose'),
        'id' => 'search-area',
        'description' => 'Search link',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => ''
    ));
    register_sidebar(array(
        'name' => __('Contacts in header', 'feyarose'),
        'id' => 'header-contacts',
        'description' => 'Contacts in header',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => ''
    ));
    register_sidebar(array(
        'name' => __('Top menu left', 'feyarose'),
        'id' => 'top-menu-left',
        'description' => 'First part of the top menu',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => ''
    ));

    register_sidebar(array(
        'name' => __('Top logo', 'feyarose'),
        'id' => 'top-logo',
        'description' => 'Logo RozBlog',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => ''
    ));

    register_sidebar(array(
        'name' => __('Customer menu', 'feyarose'),
        'id' => 'customer-menu',
        'description' => 'Customer menu on the top of the page',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => ''
    ));

    register_sidebar(array(
        'name' => __('Top menu right', 'feyarose'),
        'id' => 'top-menu-right',
        'description' => 'Second part of the menu',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => ''
    ));
    register_sidebar(array(
        'name' => __('Link to shop', 'feyarose'),
        'id' => 'link-to-shop',
        'description' => 'Stamp link to shop',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => ''
    ));

    register_sidebar(array(
        'name' => __('Content inner footer', 'feyarose'),
        'id' => 'content-inner-footer',
        'description' => 'Content inner footer for instagram integration',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => ''
    ));
    register_sidebar(array(
        'name' => __('Footer col 1-2', 'feyarose'),
        'id' => 'footer-col-1-2',
        'description' => 'First and second part of the footer combined',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => ''
    ));
    register_sidebar(array(
        'name' => __('Footer col 3', 'feyarose'),
        'id' => 'footer-col-3',
        'description' => 'Third column of the footer',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => ''
    ));
    register_sidebar(array(
        'name' => __('Footer col 4', 'feyarose'),
        'id' => 'footer-col-4',
        'description' => 'Fourth column of the footer',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => ''
    ));
}

add_action('widgets_init', 'feyarose_widgets_init');

function feyarose_my_menus()
{
    register_nav_menus(
        array(
            'top-left-menu' => __('Top left menu'),
            'top-right-menu' => __('Top right menu')
        )
    );
}

add_action('init', 'feyarose_my_menus');
/**
 * Enqueue scripts and styles.
 */
function boostrap_add_style()
{
    wp_enqueue_style('bootstrap-admin', get_template_directory_uri() . '/bootstrap.min.css');
    wp_enqueue_script('bootstrap-admin-js', get_template_directory_uri() . '/bootstrap/js/bootstrap.js', array('jquery'), '', true);
    wp_register_script('modaljs-admin', get_stylesheet_directory_uri() . '/js/bootstrap.min.js', array('jquery'), '1', true);
}

add_action('admin_enqueue_scripts', 'boostrap_add_style');

function feyarose_scripts()
{

    wp_enqueue_script('bootstrap-min-js', get_template_directory_uri() . '/bootstrap/js/bootstrap.min.js', array('jquery'), '', true);
    wp_enqueue_script('autocompleted-js', get_template_directory_uri() . '/js/jquery.maskedinput.min.js', array('jquery'), '', true);

    wp_enqueue_style('feyarose-style', get_stylesheet_uri());
    wp_enqueue_script('feyarose-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true);

    wp_enqueue_script('feyarose-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true);

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    /* owl carousel */
    wp_enqueue_style('owl-style', get_template_directory_uri() . '/owl.carousel.min.css');
    wp_enqueue_script('owl-style', get_template_directory_uri() . '/js/owl.carousel.min.js', array('jquery'), '', true);
    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/feyarose.css');



    wp_enqueue_style('jquery-ui-dialog');

    wp_enqueue_script('feyarose-parallax', get_template_directory_uri() . '/js/skrollr.min.js', array(), '20150205', false);


    wp_enqueue_script('feyarose-general', get_template_directory_uri() . '/js/feyarose_general.js', array(), '20150415', false);



    wp_enqueue_script('feyarose-homejs', get_template_directory_uri() . '/js/feyarose_home.js', array(), '20150429', false);





}

add_action('wp_enqueue_scripts', 'feyarose_scripts');


function language_selector_flags()
{
    if (function_exists('icl_get_languages')) {
        $langs = '';
        $languages = icl_get_languages('skip_missing=0&orderby=code&order=desc');
        echo '<div class="lang_selector">';
        if (!empty($languages)) {
            foreach ($languages as $l) {
                $class = $l['active'] ? ' class="active"' : null;
                $langs .= '<a ' . $class . ' href="' . $l['url'] . '">' . strtoupper($l['language_code']) . '</a> | ';
            }
            $langs = substr($langs, 0, -3);
            echo $langs;
        }
        echo '</div>';
    }
}


function get_homepage_rose_post($post_id, $type, $nb_words, $bgRose = null)
{
    $post_id = icl_object_id($post_id, 'post', true, ICL_LANGUAGE_CODE);
    $queried_post = get_post($post_id);
    $post_url = get_permalink($post_id);
    $rose = get_field('rose_post', $post_id);
    if ($rose == false) {
        $rose = get_field('rose_post', '12796');
    }
    if($bgRose != null) {
        $rose = $bgRose;
    }
    $category = get_the_category($post_id);
    $comments = wp_count_comments($post_id);
    $comments_html = "";
    if ($comments->approved > 0) {
        $comments_html = '<p><span class="feyarose-comment-count">' . $comments->approved . '</span></p>';
    }
    $html = '
    <div class="feyarose-' . $type . '-container" style="width: ' . $rose['width'] . 'px; height: ' . $rose['height'] . 'px; background-image: url(' . $rose['url'] . ');">
    ';
    //$img_bg = '<img src="'.$rose['url'].'" class="feyarose-'.$type.'-bgimg" alt="'.$rose['caption'].'"/>';
    //$html .= $img_bg;
    $html .= '<div class="feyarose-' . $type . '-content" style="width: ' . $rose['width'] . 'px;">
        <h4>' . $category[0]->name . '</h4>
        <h3>' . $queried_post->post_title . '</h3>
        <div class="feyarose-' . $type . '-body" >' . wp_trim_words($queried_post->post_content, $nb_words) . '</div>
        <a href="' . $post_url . '" class="btn btn-primary">' . __("Read more", "feyarose") . '</a>
        ' . $comments_html . '
    </div>';
    $html .= '</div>';
    return $html;
}

function get_homepage_rose_page($page_id, $type, $nb_words, $bgRose = null)
{
    $page_id = icl_object_id($page_id, 'page', true, ICL_LANGUAGE_CODE);
    $queried_post = get_post($page_id);
    $post_url = get_permalink($page_id);
    $rose = get_field('rose_post', $page_id);
    $category = get_the_category($page_id);
    $comments = wp_count_comments($page_id);
    $comments_html = "";
    if ($comments->approved > 0) {
        $comments_html = '<p><span class="feyarose-comment-count">' . $comments->approved . '</span></p>';
    }
    if($bgRose != null) {
        $rose = $bgRose;
    }
    $html = '
    <div class="feyarose-' . $type . '-container" style="width: ' . $rose['width'] . 'px; height: ' . $rose['height'] . 'px; background-image: url(' . $rose['url'] . ');">
    ';
    //$img_bg = '<img src="'.$rose['url'].'" class="feyarose-'.$type.'-bgimg" alt="'.$rose['caption'].'"/>';
    //$html .= $img_bg;
    $html .= '<div class="feyarose-' . $type . '-content" style="width: ' . $rose['width'] . 'px;">
        <h4>' . $category[0]->name . '</h4>
        <h3>' . $queried_post->post_title . '</h3>
        <div class="feyarose-' . $type . '-body" >' . wp_trim_words($queried_post->post_content, $nb_words) . '</div>
        <a href="' . $post_url . '" class="btn btn-primary">' . __("Read more", "feyarose") . '</a>
        ' . $comments_html . '
    </div>';
    $html .= '</div>';
    return $html;


}

/**
 * Implement the Custom Header feature.
 */
//require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';


// FARES
/*
 |-----------------------------------------------
 | Images Resize :
 |-----------------------------------------------
*/

add_theme_support('post-thumbnails');
set_post_thumbnail_size(947, 360, true);
add_image_size( 'products-carousel', 1120, 420, true );
/*
function feyarose_add_image_sizes () {
    add_theme_support('post-thumbnails');
    set_post_thumbnail_size( 700, 700,true);
    add_image_size( 'big-listing-image', 627, 330, true );
    add_image_size( 'small-listing-image', 316, 260, true );
}

add_action( 'after_setup_theme', 'feyarose_add_image_sizes' );

add_filter( 'image_size_names_choose', 'custom_image_sizes_choose' );
function custom_image_sizes_choose( $sizes ) {
    $custom_sizes = array(
        'big-listing-image' => 'Post Listing Big Image',
        'small-listing-image' => 'Post Listing Small Image'
    );
    return array_merge( $sizes, $custom_sizes );
}
*/

/*
 |-----------------------------------------------
 | Pagination :
 |-----------------------------------------------
*/
if (!function_exists('wpex_pagination')) {

    function wpex_pagination()
    {
        global $wp_query;
        $total = $wp_query->max_num_pages;
        $big = 999999999; // need an unlikely integer
        if ($total > 1) {
            if (!$current_page = get_query_var('paged')) {
                $current_page = 1;
            }
            if (get_option('permalink_structure')) {
                $format = 'page/%#%/';
            } else {
                $format = '&paged=%#%';
            }
            echo paginate_links(array(
                'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                'format' => $format,
                'current' => max(1, get_query_var('paged')),
                'total' => $total,
                'mid_size' => 2,
                'type' => 'list',
                'prev_text' => 'Back',
                'next_text' => 'Next',
            ));
        }
    }

}
/*
|---------------------------------
| WooCommerce Manipulation:
|---------------------------------
 */
/* Disable the wooCommerce styles */
add_filter('woocommerce_enqueue_styles', '__return_empty_array');


/* Move Product Title Up To The Top Of Page */
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
add_action('woocommerce_before_main_content', 'woocommerce_template_single_title', 5);

/* Reposition WooCommerce breadcrumb : Refer to content-single-product.php */
function woocommerce_remove_breadcrumb()
{
    remove_action(
        'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
}

add_action(
    'woocommerce_before_main_content', 'woocommerce_remove_breadcrumb'
);

function woocommerce_custom_breadcrumb()
{
    woocommerce_breadcrumb();
}

add_action('woo_custom_breadcrumb', 'woocommerce_custom_breadcrumb');

/* Change the breadcrumb delimeter from / to >  */
add_filter('woocommerce_breadcrumb_defaults', 'jk_change_breadcrumb_delimiter');
function jk_change_breadcrumb_delimiter($defaults)
{

    $defaults['delimiter'] = ' &gt; ';

    return $defaults;
}

/* change position of add-to-cart on single product */
//remove_action( 'woocommerce_single_product_summary','woocommerce_template_single_add_to_cart', 30 );
//add_action( 'woocommerce_single_product_summary','woocommerce_template_single_add_to_cart', 9 );


add_action('after_setup_theme', 'woocommerce_support');
function woocommerce_support()
{
    add_theme_support('woocommerce');
}

add_action('wp_enqueue_scripts', 'wcqi_enqueue_polyfill');
function wcqi_enqueue_polyfill()
{
    wp_enqueue_script('wcqi-number-polyfill');
}

// Hook in
add_filter('woocommerce_checkout_fields', 'custom_override_checkout_fields');

// Our hooked in function - $fields is passed via the filter!
function custom_override_checkout_fields($fields)
{

    unset($fields['billing']['billing_address_2']);
    unset($fields['shipping']['shipping_address_2']);
    unset($fields['shipping']['shipping_country']);
    unset($fields['shipping']['shipping_first_name']);
    unset($fields['shipping']['shipping_last_name']);
    unset($fields['shipping']['shipping_company']);
    unset($fields['shipping']['shipping_company']);
    unset($fields['shipping']['shipping_postcode']);
    unset($fields['shipping']['shipping_state']);

    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_state']);
    unset($fields['billing']['billing_postcode']);

    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_last_name']);
    unset($fields['order']['order_comments']);



    return $fields;
}


/**
 * Change on single product panel "Product Description"
 * since it already says "features" on tab.
 */
add_filter('woocommerce_product_description_heading',
    'feyarose_product_description_heading');

function feyarose_product_description_heading()
{
    return '';//__('', 'woocommerce');
}

/* Replace the rest of text with [...] : refer to content-search and content-postlistingBig.php and content-postlistingSmall.php */
function custom_excerpt_length($length)
{
    return 20;
}

add_filter('excerpt_length', 'custom_excerpt_length', 999);
/* clickable ellipsis */
function new_excerpt_more($more)
{
    return ' <a class="click-ellps" href="' . get_permalink(get_the_ID()) . '">[...]</a>';
}

add_filter('excerpt_more', 'new_excerpt_more');


/**
 * Set page url when cart is empty
 */
add_filter('wpmenucart_emptyurl', 'add_wpmenucart_emptyurl', 1, 1);
function add_wpmenucart_emptyurl($empty_url)
{
    $empty_url = 'https://google.com';

    return $empty_url;
}

add_filter('excerpt_more', 'new_excerpt_more');


function custom_placeholder()
{
    if (is_cart()) {
        add_filter('woocommerce_placeholder_img_src', 'custom_placeholder_img');

        function custom_placeholder_img($src)
        {
            $upload_dir = wp_upload_dir();
            $uploads = untrailingslashit($upload_dir['baseurl']);
            $src = $uploads . '/your/directory/custom_placeholder.jpg';
            $src = get_template_directory() . '/images/product-placeholder.png';

            return $src;
        }
    }
}

add_action('init', 'custom_placeholder');

function wqep_included_billing_information_keys_filter_custom($keys)
{

    unset($keys['billing_company']);
    return $keys;

}

add_filter('wqep_included_billing_information_keys_filter', 'wqep_included_billing_information_keys_filter_custom');


function my_order_default_product_keys_filter($key)
{
    return array('name', 'quantity');
}

add_filter('wqep_included_order_default_product_keys_filter', 'my_order_default_product_keys_filter');

function wqep_included_order_keys_filter_custom($keys)
{

    array_push($keys, 'Delivery Date');

    return $keys;

}

add_filter('wqep_included_order_keys_filter', 'wqep_included_order_keys_filter_custom');

add_action('wp_enqueue_scripts', 'custom_enqueue_datepicker');

function custom_enqueue_datepicker()
{
    // Optional - enqueue styles
    wp_enqueue_style('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/smoothness/jquery-ui.css', false, '1.0', false);

    // Enqueue YOURTHEME/js/datepicker.js
    wp_enqueue_script('your-datepicker-script', get_stylesheet_directory_uri() . '/js/bouquet-datepicker.js', array(
        'jquery',
        'jquery-ui-datepicker'
    ), '1.0', true);
}

/* Display just posts in the search result */
/*
function SearchFilter($searchPost)
{
    if ($searchPost->is_search) {
        $searchPost->set('post_type', array('product'));
    }

    return $searchPost;
}

add_filter('pre_get_posts', 'SearchFilter');
*/

function feyarose_getCleanNameOfProductAddon($name, $reverse_return = false)
{
    $name = str_replace('   ', ' - ', $name);
    $aname = explode(' - ', $name);

    if (isset($aname[1])) {
        $name = $aname[1];
    }

    //echo ICL_LANGUAGE_CODE;
    $listOfNames = array(
        'Количество роз в букете' => array('id' => 'nbroses', 'en' => 'Quantity of roses in the bouquet', 'ru' => 'Количество роз в букете'),
//        'Дата доставки' => array('id' => 'deliverydate', 'en' => 'Date of delivery', 'ru' => 'Дата доставки'),
        'Дата доставки' => array('id' => 'additional_field_98_field', 'en' => 'Date of delivery', 'ru' => 'Дата доставки'),
        'Временной интервал доставки' => array('id' => 'deliveryperiod', 'en' => 'Time of delivery', 'ru' => 'Временной интервал доставки'),
        'Улица' => array('id' => 'deliverystreet', 'en' => 'Street', 'ru' => 'Улица'),
        'Домофон\/код' => array('id' => 'deliverycode', 'en' => 'Intercom/code', 'ru' => 'Домофон/код'),
        'Дом' => array('id' => 'deliverybuilding', 'en' => 'Home', 'ru' => 'Дом'),
        'Квартира' => array('id' => 'deliveryappartment', 'en' => 'Apartment', 'ru' => 'Квартира'),
        'Этаж' => array('id' => 'deliveryetage', 'en' => 'Floor', 'ru' => 'Этаж'),
        'Метро' => array('id' => 'deliverymetro', 'en' => 'Metro', 'ru' => 'Метро'),
        'Город' => array('id' => 'deliverytown', 'en' => 'City', 'ru' => 'Город'),
        'Адрес доставки' => array('id' => 'deliveryaddress', 'en' => 'Delivery address', 'ru' => 'Адрес доставки'),
        'ФИО' => array('id' => 'recipientname', 'en' => 'Full name', 'ru' => 'ФИО'),
//        'Имия' => array('id' => 'billing_first_name', 'en' => 'First name', 'ru' => 'Имия'),
        'Телефон' => array('id' => 'recipientphone', 'en' => 'Phone', 'ru' => 'Телефон'),
        'Получатель' => array('id' => 'recipient', 'en' => 'Receiver', 'ru' => 'Получатель'),
        'Открытка к букету' => array('id' => 'postcard', 'en' => 'Postcard to flowers', 'ru' => 'Открытка к букету'),
//		'Без открытки'                => array('id'=>'postcard-without','en'=>'no card','ru'=>'Без открытки'),
        'Это подарок' => array('id' => 'postcard-without', "en" => "it's a present", 'ru' => 'Это подарок'),
        'Крим Пьяже' => array('id' => 'postcard-krim-piaget', 'en' => 'Cream Piaget', 'ru' => 'Крим Пьяже'),
        'Анджи Романтика' => array('id' => 'postcard-andgiromantika', 'en' => 'Angie Romantica', 'ru' => 'Анджи Романтика'),
        'Пинк Романтика' => array('id' => 'postcard-pink-romantika', 'en' => 'Pink Romantica', 'ru' => 'Пинк Романтика'),
        "Пинк О'Хара" => array('id' => 'postcard-pink-ohara', 'en' => 'Pink O’Hara', 'ru' => "Пинк О'Хара"),
        'Ив Пьяже' => array('id' => 'postcard-yves-piaget', 'en' => 'Yves Piaget', 'ru' => 'Ив Пьяже'),
        'Авангард' => array('id' => 'postcard-avant-garde', 'en' => 'Avant-Garde', 'ru' => 'Авангард'),
        'Текст на открытке' => array('id' => 'postcard-message', 'en' => 'Text on the card', 'ru' => 'Текст на открытке'),
        'Комментарий к доставке' => array('id' => 'delivery-comment', 'en' => 'Commentary on delivery', 'ru' => 'Комментарий к доставке'),
        'Москва' => array('id' => 'stock-city-moscow', 'en' => 'Moscow', 'ru' => 'Москва'),
        'Санкт-Петербург' => array('id' => 'stock-city-peter', 'en' => 'Saint-Petersburg', 'ru' => 'Санкт-Петербург'),
        'В пределах КАД' => array('id' => 'stock-city-mkad-peter', 'en' => 'For the KAD', 'ru' => 'В пределах КАД'),
        'За КАД в пределах Санкт-Петербурга' => array('id' => 'stock-city-peter-kad', 'en' => 'Within the KAD St. Petersburg', 'ru' => 'За КАД в пределах Санкт-Петербурга'),
        'В пределах МКАД' => array('id' => 'stock-city-mkad-mosc', 'en' => 'Within the MKAD', 'ru' => 'В пределах МКАД'),
        'Зеленоградский АО; Троицкий АО' => array('id' => 'stock-city-grad', 'en' => 'Zelenograd AO ; Trinity АО', 'ru' => 'Зеленоградский АО ; Троицкий АО'),
        'За МКАД в пределах Москвы \(кроме Зеленоградского АО и Троицкого АО\)' => array('id' => 'stock-city-trinity', 'en' => 'Outside MKAD within Moscow (except Zelenograd АО and Trinity АО )', 'ru' => 'За МКАД в пределах Москвы (кроме Зеленоградского АО и Троицкого АО)'),
        'Классический микс' => array('id' => 'prod-type-classic', 'en' => 'Classic mix ', 'ru' => 'Классический микс'),
        'Серебряный микс' => array('id' => 'prod-type-silver', 'en' => 'Silver mix ', 'ru' => 'Серебряный микс'),
        'Пастельный микс' => array('id' => 'prod-type-pastel', 'en' => 'Pastel mix ', 'ru' => 'Пастельный микс'),
        'Осенний микс' => array('id' => 'prod-type-autumn', 'en' => 'Autumn mix ', 'ru' => 'Осенний микс'),
        'Темный микс' => array('id' => 'prod-type-dark', 'en' => 'Dark mix ', 'ru' => 'Темный микс'),
        'Типы букеты' => array('id' => 'prod-types', 'en' => 'Bouquet types ', 'ru' => 'Типы букеты'),
        'Подарок' => array('id' => 'gift-date', 'en' => 'Present date ', 'ru' => 'Подарок'),
        'скрытый этикетки' => array('id' => 'secret-lab', 'en' => 'secret label ', 'ru' => 'скрытый этикетки'),
        'от' => array('id' => 'gift-from', 'en' => 'From ', 'ru' => 'от'),
        'для' => array('id' => 'gift-to', 'en' => 'To ', 'ru' => 'для'),
        'Delivery period' => array('id' => 'deliveryperiod', 'en' => 'Delivery period', 'ru' => 'Delivery period'),
        'Вы можете добавить личное послание Вашего букета' => array('id' => 'checkbox-add-card', 'en' => 'You can add a personal message to your bouquet', 'ru' => 'Вы можете добавить личное послание Вашего букета'),
//        'TEST'                    => array('id'=>'additional_field_620','en'=>'TEST-en','ru'=>'TEST'),


    );
    foreach ($listOfNames as $origName => $aTranslation) {
        if (preg_match("/^" . strtolower($origName) . "(.*)/i", strtolower($name)) > 0) {
            if ($reverse_return == true) {
                return $aTranslation[ICL_LANGUAGE_CODE];
            }
            return $aTranslation['id'];
        }
    }
    return $name;
}

function feyarose_addon_end_action($addon)
{
    $name = feyarose_getCleanNameOfProductAddon($addon['name']);
    if ($name == 'deliveryaddress') {
        $post_id = icl_object_id(13051, 'page', true, ICL_LANGUAGE_CODE);
        $post_url = get_permalink($post_id);
        //echo '<div><a href="'.$post_url.'">'.__('Условия доставки и оплаты').'</a></div>';
    }
}

add_action('wc_product_addon_end', 'feyarose_addon_end_action');


remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);

function feyarose_before_login_form()
{
    if (!is_checkout()) {
        echo "<div class='col-lg-12'><h4>";
        echo __('Registration is processed  at the moment of first order.', 'feyarose');
        echo "</h4></div>";
    }
}

// Регистрация осуществляется в процессе оформления первого заказа.
// Registration is processed  at the moment of first order.
add_action('woocommerce_login_form_start', 'feyarose_before_login_form');

/* fares */

/**
 * Add delivered status to order list
 */
add_action('init', 'register_custom_post_status', 10);
function register_custom_post_status()
{
    register_post_status('wc-delivered', array(
        'label' => _x('Delivered Order', 'Order status', 'woocommerce'),
        'public' => true,
        'exclude_from_search' => false,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'label_count' => _n_noop('Delivered Order <span class="count">(%s)</span>', 'Delivered Order <span class="count">(%s)</span>', 'woocommerce')
    ));

}

/**
 * Add custom status(Delivery) to order page drop down
 */
add_filter('wc_order_statuses', 'custom_wc_order_statuses');
function custom_wc_order_statuses($order_statuses)
{
    $order_statuses['wc-delivered'] = _x('Delivered Order', 'Order status', 'woocommerce');

    return $order_statuses;
}

/**
 * Add icon for delivered order statuses
 **/
add_action('wp_print_scripts', 'feyarose_add_custom_order_status_icon');
function feyarose_add_custom_order_status_icon()
{
    if (!is_admin()) {
        return;
    }
    ?>
    <style>
        /* Add custom status order icons */
        .column-order_status mark.delivered {
            content: url(/wp-content/themes/feyarose/images/truck2.png);
        }
    </style> <?php
}

/* add order action button */
function add_delivery_to_order_admin_actions($actions)
{
    global $post;
    global $the_order;
    $actions = array();
    if ($the_order->has_status('processing')) {
        $actions['complete'] = array(
            'url' => wp_nonce_url(admin_url('admin-ajax.php?action=woocommerce_mark_order_status&status=completed&order_id=' . $post->ID), 'woocommerce-mark-order-status'),
            'name' => __('Complete', 'woocommerce'),
            'action' => "complete"
        );
    }
    if ($the_order->has_status('completed')) {
        $actions['delivery'] = array(
            'url' => wp_nonce_url(admin_url('admin-ajax.php?action=woocommerce_mark_order_status&status=delivered&order_id=' . $post->ID), 'woocommerce-mark-order-status'),
            'name' => __('Delivered', 'woocommerce'),
            'action' => "delivery"
        );
    }
    $actions['view'] = array(
        'url' => admin_url('post.php?post=' . $post->ID . '&action=edit'),
        'name' => __('View', 'woocommerce'),
        'action' => "view",
    );
    return $actions;
}

add_filter('woocommerce_admin_order_actions', 'add_delivery_to_order_admin_actions', 10, 3);
/*
* @author Fares
* ADDS STYLESHEET ON WP-ADMIN
**/
add_action('admin_enqueue_scripts', 'safely_add_stylesheet_to_admin');
function safely_add_stylesheet_to_admin()
{
    wp_enqueue_style('feyarose-admin-style', get_template_directory_uri() . '/feyarose-admin.css');
    wp_enqueue_script('feyaroseorder_stock', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'));
}

/*
* @author Fares
* Remove  woocommerce related product
**/
function feyarose_remove_related_products($args)
{
    return array();
}

add_filter('woocommerce_related_products_args', 'feyarose_remove_related_products', 10);
/*
* @author Fares
* enqueues font awesome stylesheet(CDN)
**/
/*
function enqueue_font_awesome_stylesheets()
{
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');
}

add_action('wp_enqueue_scripts', 'enqueue_font_awesome_stylesheets');
*/
/*
* @author Fares
* enqueues bootstrap glyphicon stylesheet(CDN)
**/
/*
* @author Fares
* Rose Product Ajax
**/
function rose_product_enqueue_js()
{
    wp_enqueue_script('rose_product_popup_ajax', get_stylesheet_directory_uri() . '/js/rose_product_popup_ajax.js', array('jquery'), '1.0', true);
}

add_action('wp_enqueue_scripts', 'rose_product_enqueue_js');





/* Change add to cart button text */
add_filter('woocommerce_product_add_to_cart_text', 'altima_custom_add_cart_text_archive', 11);
function altima_custom_add_cart_text_archive()
{
    global $product;
    $product_type = $product->product_type;
    switch ($product_type) {
        case 'simple':
            return __('Add to cart', 'woocommerce');
            break;
        case 'variable':
            return __('Select option', 'woocommerce');
            break;
        default:
            return __('any thing', 'woocommerce');
    }
}

/*
* @author Fares
* Create custom post type
**/
// feyarose custom post type function
function create_rose_posttype()
{
    register_post_type('shop_carouse',
        array(
            'labels' => array(
                'name' => __('Shop carousel'),
                'singular_name' => __('Rose')
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'roses'),
        )
    );
}

add_action('init', 'create_rose_posttype');
// Creating a function to create options
function rose_custom_post_type_options()
{
    $labels = array(
        'name' => _x('Shop carousel', 'Post Type General Name', 'feyarose'),
        'singular_name' => _x('Shop carousel', 'Post Type Singular Name', 'feyarose'),
        'menu_name' => __('Shop carousel', 'feyarose'),
        'parent_item_colon' => __('Parent Rose', 'feyarose'),
        'all_items' => __('Shop carousel items', 'feyarose'),
        'view_item' => __('View Rose', 'feyarose'),
        'add_new_item' => __('Add New Rose', 'feyarose'),
        'add_new' => __('Add New', 'feyarose'),
        'edit_item' => __('Edit Rose', 'feyarose'),
        'update_item' => __('Update Rose', 'feyarose'),
        'search_items' => __('Search Rose', 'feyarose'),
        'not_found' => __('Not Found', 'feyarose'),
        'not_found_in_trash' => __('Not found in Trash', 'feyarose'),
    );
// Set other options for Custom Post Type
    $args = array(
        'label' => __('roses', 'feyarose'),
        'description' => __('Rose news and reviews', 'feyarose'),
        'labels' => $labels,
        // Features this CPT supports in Post Editor
        'supports' => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields',),
        // You can associate this CPT with a taxonomy or custom taxonomy.
        'taxonomies' => array('genres'),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'page',
    );
    // Registering feyarose custom post type(roses)
    register_post_type('shop_carouse', $args);
}

add_action('init', 'rose_custom_post_type_options', 0);
/* End custom post type */

/* DISPLAY MESSAGE IF LESS THAN 12 ROSES IN THE CART */




function feyarose_get_cart_roses ($wooCommerceObj = null){
    global $woocommerce;
    $comm = $woocommerce;
    if($wooCommerceObj != null) {
        $comm = $wooCommerceObj;
    }
    $items = $comm->cart->get_cart();
    $rosestotal = 0;

    if(count($items) > 0) {
        foreach($items as $key => $item) {
            $cart_product_id =  $item['product_id'];
            $cart_product_terms = get_the_terms($cart_product_id,'product_cat');
            $cat_name='';
            if(count($cart_product_terms)>0 && $cart_product_terms != false) {
                //var_dump($cart_product_terms);
                foreach($cart_product_terms as $cart_product_category) {
                    $cat_name =  $cart_product_category->name;
                }
            }

            if(($cat_name == 'roz') || ($cat_name == 'Roses') || ($cat_name == 'rose') || ($cat_name == 'Розы') ) {
                $rosestotal = $rosestotal +intval($item['quantity']);
            }
        }
    }

    return $rosestotal;
}
function less_than_twelve_rose($wooCommerceObj = null) {
    // check how many roses in cart

    $rosestotal = feyarose_get_cart_roses($wooCommerceObj);
    $message = '';
    // display error message if there are less than 12 roses in the cart
    if($rosestotal > 0 && $rosestotal < 12){
        $diff = 12 - $rosestotal;
        $roseNum = ($diff == 1) ? 'rose' : 'roses';
        $onClick = "onclick='jQuery(\"#myModalFooterDelivery\").modal(\"show\");'";
        if($diff == 1 ){
            $message = sprintf(__('You need to add %s rose so that the delivery is <a href="#" %s >free*</a>','woothemes'),$diff,  $onClick);
        } elseif($diff < 5) {
            $message = sprintf(__('You need to add %s roses so that the delivery is <a href="#" %s >free*</a>.','woothemes'),$diff, $onClick);
        }else {
            $message = sprintf(__('You need to add %s roses so that the delivery is <a href="#" %s >free*</a>','woothemes'),$diff, $onClick);
        }

    }

    if($rosestotal > 0 && $rosestotal < 12) {
        if($wooCommerceObj == null) {
            global $woocommerce;
            $wooCommerceObj = $woocommerce;
        }

            
            $amount = $wooCommerceObj->cart->subtotal;
            if($amount >= 3000) {
                $message = '';
            }


    }

    return $message;
}




add_action('woocommerce_checkout_process', 'feyaroseorders_checkout_validation');

function feyaroseorders_checkout_validation()
{
    $types = unserialize(get_option('feyaroseorders_products_types'));
    global $woocommerce;
    $items = $woocommerce->cart->get_cart();
    $item_tsDate = strtotime($_POST['additional_field_98']);  // (delivery date in timestamp format)
    $item_DeliveryCity = $_POST['additional_field_394'];       // (delivery city)
    $isError = false;

    //wc_add_notice("<pre>".print_r($types, true)."</pre>", 'error');

    foreach ($items as $item => $values) {
// get item data.
        $item_id = $values['product_id'];                          // (item id)
        //checking the russian version of the product
        $item_id = icl_object_id($item_id, 'product', true, 'ru');

        $item_numOfRoses = $values['addons'][0]['value'];          // (number of roses)
        $stockToCheck = null;
        $product_type = null;
        foreach ($types as $item_type) {
            $item_type_id = $item_type[1];

            $item_type_type = $item_type[0];

            if (($item_type_id == $item_id) && ($item_type_type == 'Bouquet')) {
                $product_type = $item_type_type;
                $stockToCheck = unserialize(get_option('feyaroseorders_stock'));
                break;
            }
            if (($item_type_id == $item_id) && ($item_type_type != 'Bouquet')) {
                $product_type = $item_type_type;
                $stockToCheck = unserialize(get_option('feyaroseorders_stock_'.$item_id));
                break;
            }


        }

        foreach($stockToCheck as $stockTS => $stockObj) {
            $dateStock = date('Y-m-d', $stockTS);
            $dayStockTS = strtotime($dateStock);
            if($dayStockTS == $item_tsDate) {
                $totalQtyOfThisItem = ($product_type == 'Bouquet') ? $item_numOfRoses * $values['quantity'] : $values['quantity'];
                if(($stockObj['stock'] - ($stockObj['to_deliver'] + $totalQtyOfThisItem)) < 0) {
                    $product = get_post($item_id);
                    $message = (ICL_LANGUAGE_CODE == 'ru') ? $product->post_title.' '.__('нет в наличии на этот день: ').$dateStock : $product->post_title.' '.__('is not available at this date of delivery : ').$dateStock;
                    wc_add_notice($message, 'error');
                    $isError = true;
                    break;
                }
            }
        }
    }

    $rosestotal = 0;
    foreach($items as $key => $item) {
        $cart_product_id =  $item['product_id'];
        $cart_product_terms = get_the_terms($cart_product_id,'product_cat');
        foreach($cart_product_terms as $cart_product_category) {
            $cat_name =  $cart_product_category->name;
        }
        if(($cat_name == 'roz') ||($cat_name == 'Roses') || ($cat_name == 'rose') || ($cat_name == 'Розы') ) {
            $rosestotal = $rosestotal +intval($item['quantity']);
        }
    }

    if($isError) {
        $message = (ICL_LANGUAGE_CODE == 'ru') ? 'пожалуйста, измените дату доставки или ваш заказ <br /><a class="btn btn-primary" href="/korzina">Вернуться в корзину</a>' : 'Please change the delivery date or modify your order <br /><a class="btn btn-primary" href="/cart/?lang=en">Back to the cart</a>';
        wc_add_notice($message, 'error');
    }

//    wc_add_notice("<pre>".print_r($stockToCheckRose, true) ."</pre>", 'error' );

}



//Feyarose check if a product can be ordered for tomorrow
function feyarose_check_product_availability($product_id, $date = null, $town = null)    {
    $ts = null;
    if($date == null) {
        $ts = mktime(0,0,0,date('m'),date('d')+1, date('Y'));
    } else {
        $expl = explode('-', $date);
        $ts = mktime(0,0,0,$expl[1],$expl[2],$expl[0]);
    }
    $types = unserialize(get_option('feyaroseorders_products_types'));
    $product_type = 'Bouquet';
    foreach ($types as $item_type) {
        if($item_type[1] == $product_id) {
            $product_type = $item_type[0];
            break;
        }
    }
    /*We'll take both peter and moscow stocks*/
    $stock_m = array();
    switch($product_type) {
        case 'Bouquet':
            $stock_m = unserialize(get_option('feyaroseorders_stock'));
            break;

        default:
            $stock_m = unserialize(get_option('feyaroseorders_stock_'.$product_id));
            break;
    }

    $rest_in_stock = 0;


    if($town == null || $town == 'moscow') {
        if(is_array($stock_m)) {
            foreach($stock_m as $ts_day => $stock) {
                if($ts == $ts_day ) {
                    if(is_array($stock) && array_key_exists('to_deliver', $stock)) {
                        $rest_in_stock = $rest_in_stock + ($stock['stock'] - $stock['to_deliver']);
                        break;
                    }
                }
            }
        }
    }


    $retour = $ts;
    if($rest_in_stock <= 50) {
        $ts = $ts + (60 * 60 * 24);
        //echo date('Y-m-d',$ts);
        return feyarose_check_product_availability($product_id, date('Y-m-d',$ts), $town);
    } else {
        return $retour;
    }

}
function feyarose_get_instagram () {

}
/* Custom thumbnail size for rose list */
function feyarose_add_thumbnail_sizes()
{
    if ( function_exists( 'feyarose_add_thumbnail_sizes' ) ) {
        add_image_size( 'rose-list-size', 262, 262, true ); //(cropped)
    }
}
add_action('after_setup_theme', 'feyarose_add_thumbnail_sizes');



add_action('admin_head', 'my_custom_fonts');

function my_custom_fonts() {
    echo '<style>
    .widefat * {
      word-wrap: normal !important;
    }
  </style>';
}


//Add custom shipping method for feyarose
add_action( 'woocommerce_cart_calculate_fees','woocommerce_custom_surcharge' );
function woocommerce_custom_surcharge() {
    global $woocommerce;

    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;

    $skip_fee = (count($woocommerce->cart->get_applied_coupons()) > 0 && $woocommerce->cart->subtotal >= 3000 ) ? true : false;
    $skip_fee = (feyarose_get_cart_roses() >= 12) ? true : $skip_fee;
    if(
        $woocommerce->cart->cart_contents_total < 3000 &&
        !$skip_fee
    ) {
        $surcharge = 500;
        $woocommerce->cart->add_fee( 'Доставка', $surcharge, true, '' );
    }


}

add_action( 'woocommerce_email_after_order_table', 'add_payment_method_to_admin_new_order', 15, 2 );

/**
 * Add used coupons to the order confirmation email
 *
 */
function add_payment_method_to_admin_new_order( $order, $is_admin_email ) {

    if ( $is_admin_email ) {

        if( $order->get_used_coupons() ) {

            $coupons_count = count( $order->get_used_coupons() );

            echo '<h4>' . __('Coupons used') . ' (' . $coupons_count . ')</h4>';

            echo '<p><strong>' . __('Coupons used') . ':</strong> ';

            $i = 1;
            $coupons_list = '';

            foreach( $order->get_used_coupons() as $coupon) {
                $coupons_list .=  $coupon;
                if( $i < $coupons_count )
                    $coupons_list .= ', ';
                $i++;
            }

            echo '<p><strong>Coupons used (' . $coupons_count . ') :</strong> ' . $coupons_list . '</p>';

        } // endif get_used_coupons

    } // endif $is_admin_email
}



add_action( 'woocommerce_admin_order_data_after_billing_address', 'custom_checkout_field_display_admin_order_meta', 10, 1 );

/**
 * Add used coupons to the order edit page
 *
 */
function custom_checkout_field_display_admin_order_meta($order){

    if( $order->get_used_coupons() ) {

        $coupons_count = count( $order->get_used_coupons() );

        echo '<h4>' . __('Coupons used') . ' (' . $coupons_count . ')</h4>';

        echo '<p><strong>' . __('Coupons used') . ':</strong> ';

        $i = 1;

        foreach( $order->get_used_coupons() as $coupon) {
            echo $coupon;
            if( $i < $coupons_count )
                echo ', ';
            $i++;
        }

        echo '</p>';
    }

}


// Defer Javascripts
// Defer jQuery Parsing using the HTML5 defer property
if (!(is_admin() )) {
    function defer_parsing_of_js ( $url ) {
        if ( FALSE === strpos( $url, '.js' ) ) return $url;
        if ( strpos( $url, 'jquery.js' ) ) return $url;
        // return "$url' defer ";
        return "$url' defer onload='";
    }
    add_filter( 'clean_url', 'defer_parsing_of_js', 11, 1 );
}


remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );



function feyarose_get_details_roses_in_cart() {
    global $woocommerce;
    $items = $woocommerce->cart->get_cart();
    $rosestotal = 0;
    $roses = array();
    if(count($items) > 0) {
        foreach($items as $key => $item) {
            $cart_product_id =  $item['product_id'];
            $cart_product_terms = get_the_terms($cart_product_id,'product_cat');
            $cat_name='';
            if(count($cart_product_terms)>0 && $cart_product_terms != false) {
                //var_dump($cart_product_terms);
                foreach($cart_product_terms as $cart_product_category) {
                    $cat_name =  $cart_product_category->name;
                }
            }

            if(($cat_name == 'roz') || ($cat_name == 'Roses') || ($cat_name == 'rose') || ($cat_name == 'Розы') ) {
                $rosestotal = $rosestotal +intval($item['quantity']);
                array_push($roses,$item);
            }
        }
    }
    return $roses;
}

function feyarose_get_latest_product_added_data($product_id) {
    global $woocommerce;
    $items = $woocommerce->cart->get_cart();
    $rosestotal = 0;
    $roses = array();
    $latest_item = null;
    $latest_item_id = 0;

    if(count($items) > 0) {
        foreach($items as $key => $item) {
            $cart_product_id =  $item['product_id'];
            if($product_id == $cart_product_id) {
                $latest_item_id = $product_id;
                $latest_item = $item;
                break;
            }
        }
    }
    return $latest_item;
}

function feyaroseorders_get_action_after_add_to_cart() {
    global $woocommerce;
    $cart_url = $woocommerce->cart->get_cart_url();
    $msg_roses = less_than_twelve_rose($woocommerce);
    $html_continue = "";
    if (isset($_POST['product_id'])) {
        $product_id = $_POST['product_id'];
        $latest_item = feyarose_get_latest_product_added_data($product_id);
        $str_added_to_cart = (ICL_LANGUAGE_CODE == 'ru') ? '"%s" добавлен в корзину.': '"%s" was successfully added to your cart.';
        $str_continue_shopping = (ICL_LANGUAGE_CODE == 'ru') ? 'Продолжить покупки': 'Continue Shopping';
        $str_to_cart = (ICL_LANGUAGE_CODE == 'ru') ? 'открыть корзину': 'go to the cart';

        $html_continue = '<div class="modal fade" id="modalCart" tabindex="-1" role="dialog" aria-labelledby="modalCart"
                                 data-keyboard="false" data-backdrop="static">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h3 class="entry-title single-post-head popup-header">

                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close"><span
                                                        aria-hidden="true">&times;</span></button>
                                            </h3>
                                        </div>
                                        <div class="modal-body">
                                            <!--  If the page upon  exists display the content   -->
                                            <div class="modal-content-row" style="text-align: center;">
                                            <div class="img-added">
                                            </div>
                                            <div  class="product-added">'.sprintf( $str_added_to_cart, $latest_item['data']->post->post_title ).'</div>
                                            <div>
                                                <a href="javascript:jQuery(\'#modalCart\').modal(\'hide\');" class="btn btn-primary">'.$str_continue_shopping.'</a>
                                                <a href="'.$cart_url.'" class="btn btn-primary">'.$str_to_cart.'</a>
                                            </div>
                                            </div>
                                        </div>
                    </div>
                    </div>
                    </div>
                    ';
    }

    $return = array(
        'msg_roses' => $msg_roses,
        'nb_items' => $woocommerce->cart->cart_contents_count,
        'html_continue' => $html_continue
    );
    wp_send_json($return);
}

add_action('wp_ajax_feyarose_orders_get_nb_cart_items','feyaroseorders_get_action_after_add_to_cart' );
add_action('wp_ajax_nopriv_feyarose_orders_get_nb_cart_items', 'feyaroseorders_get_action_after_add_to_cart' );


/**
    Add more link ito bouquets carousel
 */
function feyarose_bouquet_excerpt( $num_words = 20, $ending = '...', $post_id = null )
{
    global $post;
    $current_post = $post_id ? get_post( $post_id ) : $post;
    $excerpt = strip_shortcodes( $current_post->post_content );
    $excerpt = wp_trim_words( $excerpt, $num_words, $ending );
    $excerpt .= '<br /><a href="" title="'.$post->post_title.'" class="order-from-magazine more-bouquet" data-id ="'.$post_id.'">'.__('More', 'woothemes').'</a>';
    return $excerpt;
}






