<?php
/**
 * @package feyarose
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<header class="entry-header">

		<?php the_post_thumbnail(); ?>
		<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>

		<?php if ( 'post' == get_post_type() ) : ?>
			<div class="entry-meta">
				<?php feyarose_posted_on(); ?>
				<?php the_content(); ?>
			</div><!-- .entry-meta -->
		<?php endif; ?>
	</header>
	<!-- .entry-header -->


	<footer class="entry-footer">
		<?php feyarose_entry_footer(); ?>
	</footer>
	<!-- .entry-footer -->
</article><!-- #post-## -->