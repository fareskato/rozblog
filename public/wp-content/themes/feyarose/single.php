<?php
/**
 * The template for displaying all single posts.
 *
 * @package feyarose
 */
get_header();
?>
<div class="container">
<?php while (have_posts()) :
	the_post(); ?>
	<div class="row">
		<div class="col col-lg-10 col-lg-offset-1">
			<div class="panel panel-default">
				<div class="panel-body">
					<div id="primary" class="content-area">
						<main id="main" class="site-main" role="main">
							<?php get_template_part( 'content', 'single' ); ?>

							<?php the_post_navigation(); ?>
						</main>
						<!-- #main -->
					</div>
					<!-- #primary -->
					<a name="comments-anchor" id="comments-anchor"></a>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col col-lg-10 col-lg-offset-1">
			<div class="panel panel-default offset2 span8">

				<div class="panel-body">
					<?php
					// If comments are open or we have at least one comment, load up the comment template
					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;
					?>
				</div>
			</div>
		</div>

	</div>

	<div class="row">
		<div class="col col-lg-8 col-lg-offset-2">
			<?php
			$share_url    = urlencode( get_post_permalink() );
			$share_title  = urlencode( get_the_title() );
			$share_domain = urlencode( $_SERVER['SERVER_NAME'] );
			?>
			<h3 class="post-share-title"><?php echo __( 'Share article', 'feyarose' ); ?></h3>
			<ul class="post-share-buttons">
				<li>
					<a title="Share on facebook" class="share-facebook"
					   href="http://www.facebook.com/share.php?u=<?php echo $share_url; ?>&title=<?php echo $share_title; ?>">
						Share on facebook
					</a>
				</li>
				<li>
					<a title="Share on twitter" class="share-twitter"
					   href="http://twitter.com/intent/tweet?status=<?php echo $share_title; ?>+<?php echo $share_url; ?>">
						Share on twitter
					</a>
				</li>
<!--				<li>
					<a title="Share on Google Plus" class="share-googleplus"
					   href="https://plus.google.com/share?url=<?php /*echo $share_url; */?>">
						Share on Google Plus
					</a>
				</li>
				<li>
					<a title="Share on linkedIn" class="share-linkedin"
					   href="http://www.linkedin.com/shareArticle?mini=true&url=<?php /*echo $share_url; */?>&title=<?php /*echo $share_title; */?>&source=<?php /*echo $share_domain; */?>">
						Share on linkedIn
					</a>
				</li>-->
				<li>
					<a title="Share on vKontakte" class="share-vkontakte"
					   href="http://vkontakte.ru/share.php?url=<?php echo $share_url; ?>">
						Share on vKontakte
					</a>
				</li>

			</ul>

		</div>
	</div>
<!--	Related Posts Area	-->
	<div class="row">
		<div class="col col-lg-10 col-lg-offset-1">
			<div class="panel panel-default offset2 span8">

				<div class="panel-body">
					<!-- Related Posts -->
					<?php
					$orig_post = $post;
					global $post;
					$categories = get_the_category( $post->ID );
					if ($categories) {
                        $category_ids = array();
                        foreach ( $categories as $individual_category ) {
                            $category_ids[] = $individual_category->term_id;
                        }
                        $args     = array(
                            'category__in'     => $category_ids,
                            'post__not_in'     => array( $post->ID ),
                            'posts_per_page'   => 3, // Number of related posts that will be displayed.
                            //'caller_get_posts' => 1,
                            'orderby'          => 'rand' // Randomize the posts
                        );
                        $my_query = new wp_query( $args );
                        if ($my_query->have_posts()) {
                    ?>
                        <div id="related_posts" class="clear row">
                            <h3><?php echo __( 'YOU MAY ALSO BE INTERESTED IN THESE ARTICLES', 'feyarose' ) ?></h3>
                            <ul>
                                <?php
                                while ( $my_query->have_posts() ) :
                                    $my_query->the_post();
                                    ?>
                                    <li>
                                        <a href="<?php the_permalink() ?>" rel="bookmark"
                                           title="<?php the_title(); ?>"></a>
                                        <!-- Display the images with the suitable size -->
                                        <a class="hover-anchor" rel="external"
                                           href="<?php the_permalink() ?>"><?php the_post_thumbnail( 'three-images-post' ); ?>
                                            <div class="related-hover">
                                                <div class="hover-text"><p class="enter"><?php echo __('GO','feyarose') ?></p></div>
                                            </div>
                                        </a>


                                        <div class="related_content">
                                            <a href="<?php the_permalink() ?>" rel="bookmark"
                                               title="<?php the_title(); ?>"><?php the_title(); ?></a>

                                            <div class="related-single-post">
                                                <?php

                                                $categ = get_the_category();
                                                echo '<span class="current-cat-name-single-post">' . $categ[0]->name . ', ' . '</span>';
                                                ?>
                                                <span class="related-single-post-date"><?php feyarose_posted_on(); ?></span>
                                            </div>

                                        </div>
                                    </li>
                                <?php
                                endwhile;
                                echo '</ul></div>';
                                }
							}
							$post = $orig_post;
							wp_reset_query();
                            ?>

					</div>
				</div>
			</div>

		</div>
		<?php
            endwhile;
        ?>

		<?php get_sidebar(); ?>
	</div>
	<?php get_footer();?>