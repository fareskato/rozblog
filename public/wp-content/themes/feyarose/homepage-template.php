<?php
/*
Template Name: Homepage template
*/

get_header(); ?>

<?php
//scroll roses behavior

$nbRoses = 9;
//fares was here

$tileW = 100;
$tileH = 100;
$layerW = 320;
global $nbL, $nbCol;
$nbL = (2200 / $tileH);
$nbCol = $layerW / $tileW;

function searchForEmptySpace($i, $j, &$grid)
{
    global $nbL, $nbCol;
    if ($grid[$i][$j] === 0) {
        $grid[$i][$j] = 1;

        return array('y' => $i, 'x' => $j);
    } else {
        $nextL = floor(rand(0, $nbL));
        $nextC = floor(rand(0, $nbCol));

        return searchForEmptySpace($nextL, $nextC, $grid);
    }
}

function generate_grid($position)
{
    global $nbL, $nbCol, $nbRoses;
    //construction du tableau
    $aGrid = array();
    for ($i = 0; $i < $nbL; $i++) {
        $aGrid[$i] = array();
        for ($j = 0; $j < $nbCol; $j++) {
            $aGrid[$i][$j] = 0;
        }
    }
    for ($i = 1; $i < $nbRoses - 1; $i++) {
        $coords = searchForEmptySpace(floor(rand(0, $nbL)), floor(rand(0, $nbCol)), $aGrid);
        echo generate_scroll_rose($coords, $position, $i);
    }
}

function generate_scroll_rose($coords, $typeRose, $indexRose)
{
    global $tileW, $tileH, $layerW;

    $pixelStart = $coords['y'] * $tileH;
    $distanceRatioMax = 3;
    $pixelStop = 2200;//$pixelStart +  ( rand(1, $tileH) * (rand(1, $distanceRatioMax)) );
    $rotationMax = 120;


    $yOffsetStart = $pixelStart;
    $yOffsetStop = $pixelStop;

    $yDistanceFinal = $yOffsetStart + rand(0, 350);//$yOffsetStart + (rand(1, $distanceRatioMax) * $tileH);


    $rotation = floor(rand(30, $rotationMax)) * (floor(rand(1, 3)) - 1.1);


    $leftPercent = $coords['x'] * $tileW;
    $nbRose = $indexRose;//floor(rand(1,12));
    $leftMargin = ($typeRose == 'center') ? "margin-left:" . (-($tileW / 2) - $leftPercent) . "px;" : "";
    $typeRose = ($typeRose == 'center') ? "left:50%" : $typeRose . ":" . $leftPercent . "px";
    $html = '<div
        class="skrollable skrollable-between feyarose-scroll-rose feyascroll-rose' . $nbRose . ' ' . $typeRose . '"
        data-0="top: ' . $pixelStart . 'px; ' . $typeRose . ';' . $leftMargin . 'transform:rotate(0deg);"
        data-' . $pixelStop . '="top: ' . ($pixelStart - (rand(30, 90) * rand(0, 3))) . 'px; ' . $typeRose . ';' . $leftMargin . '" ></div>';

    return $html;
}


?>
<div id="skrollr-body"></div>
<div class="feyarose-scroll feyarose-scroll-1">
    <div class="feyascroll-layer1 feyascroll-layer skrollable skrollable-between"
         data-0="top:300px;"
         data-2400="top:-1200px;"
        >
        <div class="feyascroll-inner">
            <?php generate_grid('left'); ?>
        </div>

    </div>

    <div class="feyascroll-layer2 feyascroll-layer skrollable skrollable-between"
         data-0="top:550px;"
         data-2400="top:-500px;"
        >
        <div class="feyascroll-inner">
            <?php generate_grid('left'); ?>
        </div>

    </div>
    <div class="feyascroll-layer3 feyascroll-layer skrollable skrollable-between"
         data-0="top:-600px;"
         data-2400="top:350px;"
        >
        <div class="feyascroll-inner">
            <?php generate_grid('left'); ?>
        </div>

    </div>


</div>


<div class="feyarose-scroll feyarose-scroll-2">
    <div class="feyascroll-layer1 feyascroll-layer skrollable skrollable-between"
         data-0="top:0px;"
         data-2400="top:-650px;"
        >
        <div class="feyascroll-inner">
            <?php generate_grid('right'); ?>
        </div>
    </div>
    <div class="feyascroll-layer2 feyascroll-layer skrollable skrollable-between"
         data-0="top:0px;"
         data-2400="top:-1400px;"
        >
        <div class="feyascroll-inner">
            <?php generate_grid('right'); ?>
        </div>
    </div>
    <div class="feyascroll-layer3 feyascroll-layer skrollable skrollable-between"
         data-0="top:-600px;"
         data-2400="top:350px;"
        >
        <div class="feyascroll-inner">
            <?php generate_grid('right'); ?>
        </div>
    </div>

</div>
<div class="feyarose-scroll feyarose-scroll-3">
    <div class="feyascroll-layer4 feyascroll-layer skrollable skrollable-between"
         data-0="top:200px;"
         data-2400="top:-300px;"
        >
        <div class="feyascroll-inner">
            <?php generate_grid('center'); ?>
        </div>
    </div>
</div>
<!-- Jumbotron -->
<?php while (have_posts()) : the_post(); ?>
    <?php
    $url = '';
    if (has_post_thumbnail()) { // check if the post has a Post Thumbnail assigned to it.
        //the_post_thumbnail();
        $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
        $url = $thumb['0'];
    }
    $class = 'feyarose-pageheader-noimage';
    if ($url != '') {
        $class = '';
    }
    ?>
    <div class="jumbotron feyarose-pageheader-jumbotron " style="background-image: url(<?php echo $url; ?>);">
        <div class="container <?php echo $class; ?>">
            <div class="feyarose-pageheader-text col-lg-7 col-lg-offset-1 firstpageheader">
                <h2>
                <span class="feyarose-pageheader-title">
                    <?php the_title(); ?><br/>
                    <span class="feyarose-pageheader-titlebar"></span>
                    <span class="feyarose-pageheader-titlebar feyarose-pageheader-titlebar-right"></span>
                </span>

                </h2>
                <?php
                the_content();
                ?>
            </div>

        </div>

    </div>
<?php endwhile; // end of the loop. ?>
<div class="container feyarose-home-roses-container">
    <div class="row-fluid">
        <div class="col col-lg-10 col-lg-offset-1 feyarose-home-roses">
            <div class="row-fluid">
                <?php
                // get the roses id from the database(feyarose_settings option)
                $rosesIDS = unserialize(get_option('feyarose_settings'));
                ?>
                <div class="feyarose-home-rose rose-1">
                    <?php
                    echo get_homepage_rose_post(
                            $rosesIDS['rose01'],
                            'rose',
                            22,
                            array(
                                'url' => 'http://www.rozblog.ru/wp-content/uploads/2015/01/home-rose1.png',
                                'width' => '367',
                                'height' => '360'
                            )
                        );
                    ?>
                </div>
                <!--  Feyarose What is it page	  -->
                <div class="feyarose-home-circle circle-1">
                    <?php
                    echo get_homepage_rose_page(
                            $rosesIDS['rose02'],
                            'circle',
                            12,
                            array(
                                'url' => 'http://www.rozblog.ru/wp-content/uploads/2015/04/a-propos.png',
                                'width' => '477',
                                'height' => '477'
                            )
                        );
                    ?>
                </div>
                <div class="feyarose-home-rose rose-2">
                    <?php
                    echo get_homepage_rose_post(
                            $rosesIDS['rose03'],
                            'rose',
                            27,
                            array(
                                'url' => 'http://www.rozblog.ru/wp-content/uploads/2015/02/home-rose2.png',
                                'width' => '437',
                                'height' => '416'
                            )
                        );
                    ?>
                </div>
                <div class="feyarose-home-quote">
                    <p>
                        <?php echo __("Take everything, but allow me to feel", "feyarose") ?><br/>
                        <?php echo __("The freshness of this crimson rose once more", "feyarose") ?><br/>
                    </p>

                    <p class="feyarose-home-quote-signature">
                        <?php echo __("Anna Akhmatova", "feyarose"); ?>
                    </p>
                </div>
                <div class="feyarose-home-rose rose-3">
                    <?php
                    echo get_homepage_rose_post(
                            $rosesIDS['rose04'],
                            'rose',
                            20,
                            array(
                                'url' => 'http://www.rozblog.ru/wp-content/uploads/2015/02/home-rose4.png',
                                'width' => '388',
                                'height' => '391'
                            )

                        );
                    ?>
                </div>
                <div class="feyarose-home-circle circle-2">
                    <?php
                    echo get_homepage_rose_post(
                            $rosesIDS['rose05'],
                            'rose',
                            10,
                            array(
                                'url' => 'http://www.rozblog.ru/wp-content/uploads/2015/02/home-rose3.png',
                                'width' => '393',
                                'height' => '370'
                            )
                        );
                    ?>
                </div>
                <div class="feyarose-home-circle circle-3">
                    <?php
                    echo get_homepage_rose_post(
                            $rosesIDS['rose06'],
                            'circle',
                            12
                        );
                    ?>
                </div>

                <div class="feyarose-home-rose rose-4">
                    <?php
                    echo get_homepage_rose_post(
                            $rosesIDS['rose07'],
                            'rose',
                            27,
                            array(
                                'url' => 'http://www.rozblog.ru/wp-content/uploads/2015/01/home-rose1.png',
                                'width' => '367',
                                'height' => '360'
                            )
                        );
                    ?>
                </div>
            </div>

        </div>
    </div>
</div>
<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

    </main>
    <!-- #main -->
</div><!-- #primary -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
