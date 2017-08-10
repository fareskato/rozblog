<?php
/**
 * @package feyarose
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'col-lg-8' ); ?>>

	<header class="entry-header one-image">
		<!--	Displaying the post featured image	-->

		<a rel="external" href="<? the_permalink() ?>"><?php the_post_thumbnail( 'big-listing-image' ); ?></a>

		<div class="comm-area-wrapper">
			<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>
			<!--	Displaying the comment image	-->
			<img class="comment-one-img"
			     src="<?php bloginfo( 'template_url' ); ?>/images/sliced-images/comment.png"/>
			<!--	Displaying the comments	-->
			<?php comments_popup_link( '0', '1', '% ', 'comments-link' ); ?>
		</div>
		<?php if ( 'post' == get_post_type() ) : ?>
			<div class="entry-meta">
				<!-- Display the name of the current category -->
				<?php
                $categ = get_the_category();
				//$current_cat_id = get_the_category( false );
				echo '<span class="current-cat-name">' . $categ[0]->name . ', ' . '</span>';
				?>
				<?php feyarose_posted_on(); ?>
			</div><!-- .entry-meta -->
			<!--	Displaying the content	-->
			<?php the_excerpt(); ?>
		<?php endif; ?>
	</header>
	<!-- .entry-header -->


	<footer class="entry-footer">
		<?php feyarose_entry_footer(); ?>
	</footer>
	<!-- .entry-footer -->
</article><!-- #post-## -->