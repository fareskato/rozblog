<?php
/**
 * The template part for displaying results in search pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package feyarose
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'col-lg-4 search-list-article' ); ?>>
	<div class="article-white three-images">
	<header class="entry-header  search-list">

		<a rel="external" href="<? the_permalink() ?>"><?php the_post_thumbnail( 'small-listing-image' ); ?></a>

		<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>

		<?php if ( 'post' == get_post_type() ) : ?>
			<div class="entry-meta">
				<!-- Display the name of the category -->
				<?php
				$current_cat_id = the_category_ID( false );
				echo '<span class="current-cat-name">' . get_cat_name( $current_cat_id ) . ', ' . '</span>';
				?>
				<?php feyarose_posted_on(); ?>
			</div><!-- .entry-meta -->
			<div class="tecr">
				<!--	Displaying the comment image	-->
				<img class="comment-three-img"
				     src="<?php bloginfo( 'template_url' ); ?>/images/sliced-images/comment.png"/>
				<!--	Displaying the comments	-->
				<?php comments_popup_link( '0', '1', '% ', 'comments-link' ); ?>
			</div>

			<div class="entry-summary">
				<!--	Displaying the content	-->
				<?php the_excerpt(); ?>
			</div>
			<!-- .entry-summary -->
		<?php endif; ?>
	</header>
	</div>
	<!-- .entry-header -->


	<footer class="entry-footer">
		<?php feyarose_entry_footer(); ?>
	</footer>
	<!-- .entry-footer -->
</article>
<!-- #post-## -->

