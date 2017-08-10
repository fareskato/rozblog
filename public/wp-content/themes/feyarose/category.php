<?php
/**
 * The template for displaying category pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package feyarose
 */
get_header();
?>
<div class="container">
	<div id="primary" class="content-area">
		<main id="main" class="site-main col-lg-10 col-lg-offset-1" role="main">

			<?php
			//displays current object being retrieved from the database
			//$current_cat = get_the_category();
			//$cat_id = $current_cat[0]->object_id;
			?>
			<header class="page-header">

				<?php
				//Since the current object is a taxonomy term, we can pass it to the get_field() function
				// get_queried_object():retrieves the currently-queried object(post object,page object,category object....etc)
				$image = get_field( 'category_image', get_queried_object() );
				// If there is Featured Image for the category display it
				if ( $image ) {
					?>
					<div class="jumbotron cat-jamp row"
					     style="background-image: url(<?php echo $image['url']; ?>);background-repeat: no-repeat; background-size: cover;background-position: center center;">
						<div class=" container">

							<!-- Display the name of the current category if the category has desc -->
							<?php
							$category = get_queried_object();//get_the_category();

							if ( category_description( $category->cat_ID ) ) :

								if ( $category->slug !== 'rose-diary' ) {
									echo '<h2>' . $category->name . '</h2>';
								}
								?>
							<?php endif; ?>

							<span id="cat-desc"><?php if ( $category->slug !== 'rose-diary' ) {
									echo category_description($category->cat_ID);
								} ?></span>
						</div>
					</div>
					<!-- ELSE  display default image for the category in case there is no Featured Image  -->
				<?php } else { ?>
					<div class="jumbotron cat-jamp row"

					     style="background-image: url(<?php bloginfo( 'template_url' ); ?>/images/sliced-images/default.jpg);background-repeat: no-repeat; background-size: cover;">
						<div class=" container">

							<!-- Display the name of the current category -->
							<?php
							$current_cat_id = get_the_category( false );
							echo '<h2>' . get_cat_name( $current_cat_id ) . '</h2>';
							?>
							<?php //feyarose_posted_on();
							?>

							<span id="cat-desc"><?php echo category_description(); ?></span>
						</div>
					</div>
				<?php } ?>

			</header>
			<!--.page-header -->
			<?php if ( have_posts() ) : ?>

				<?php /* Start the Loop */
				?>
				<div id="main-article">
					<?php
					$iPost = 0;

					while ( have_posts() ) : the_post();
						if ( $iPost == 0 ) {
							echo "<div class='row'>";
							get_template_part( 'content', 'postlistingBig' );

							echo "<div class=\"col-lg-4\" >";

							dynamic_sidebar( 'cat-form-sidebar' );
							echo "</div>";

							echo "</div>";
						} elseif ( $iPost == 1 ) {
							echo "<div class='row'>";
							get_template_part( 'content', 'postlistingSmall' );
						} elseif ( $iPost == 3 ) {
							get_template_part( 'content', 'postlistingSmall' );
							echo "</div>";
						} elseif ( $iPost == 4 ) {
							echo "<div class='row'>";
							get_template_part( 'content', 'postlistingBig' );
						} elseif ( $iPost == 5 ) {
							get_template_part( 'content', 'postlistingSmall' );
							echo "</div>";
						} else {
							if ( $iPost % 6 == 0 ) {
								echo "<div class='row'>";
							}
							/* Include the Post-Format-specific template for the content.
							 * If you want to override this in a child theme, then include a file
							 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
							 */
							get_template_part( 'content', 'postlistingSmall' );

							if ( $iPost % 8 == 0 ) {
								//get_template_part( 'content', 'postlistingSmall' );
								echo "</div>";
							}
						}


						?>

						<?php
						$iPost ++;


					endwhile;
					if ( $iPost == 2 || $iPost == 4 || $iPost == 6 || $iPost == 7 ) {
						echo "</div>";
					}
					?>
				</div>
				<?php //the_posts_navigation();  ?>

			<?php else : ?>
				<?php
				echo "<div class='row'>";


				echo "<div class=\"col-lg-offset-8 col-lg-4\" >";
				dynamic_sidebar( 'cat-form-sidebar' );
				echo "</div>";
				echo "</div>";
				?>
				<?php //get_template_part( 'content', 'none' ); ?>

			<?php endif; ?>
			<!--  Pagination Displaying Code	-->
			<div class="row"><?php wpex_pagination(); ?></div>


		</main>
		<!-- #main -->
	</div>
	<!-- #primary -->

	<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
