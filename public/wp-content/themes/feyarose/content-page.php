<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package feyarose
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<header class="entry-header">
		<!--FARES: Disable the title of the current page	-->
		<?php //the_title( '<h1 class="entry-title">', '</h1>' ); ?>
        <!--	Displaying the post featured image	-->
        <div class="row cat-full-width-image"><?php the_post_thumbnail(); ?></div>
	</header><!-- .entry-header -->

	<div class="entry-content">

		<?php the_content(); ?>

		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'feyarose' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php edit_post_link( __( 'Edit', 'feyarose' ), '<span class="edit-link">', '</span>' ); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
