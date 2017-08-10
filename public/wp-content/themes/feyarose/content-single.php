<?php
/**
 * @package feyarose
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<header class="entry-header">
		<div class="entry-meta single-post">

			<!-- Display the name of the current category -->
			<?php

            $categ = get_the_category();
			echo '<span class="current-cat-name">' . $categ[0]->name . ', ' . '</span>';
			?>
			<?php feyarose_posted_on(); ?>
		</div>
		<!-- .entry-meta -->
		<!--	Displaying the post title	-->
		<?php $the_title = get_the_title(); ?>
		<h1 class="entry-title single-post-head"><?php echo strtoupper( $the_title ); ?></h1>

		<!--	Displaying the post featured image	-->
		<div class="row cat-full-width-image"><?php the_post_thumbnail(); ?></div>

		<div class="row single-post-comm-area">
			<!--	Displaying the comment image	-->
			<img class="sigle-post-commm"
			     src="<?php bloginfo( 'template_url' ); ?>/images/sliced-images/comment.png"/>
			<!--	Displaying the comments	-->
			<span class="sigle-post-comm-num"><?php comments_popup_link( '0', '1', '% ', 'comments-link' ); ?></span>
		</div>

	</header>
	<!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?>
		<?php
		wp_link_pages( array(
			'before' => '<div class="page-links">' . __( 'Pages:', 'feyarose' ),
			'after'  => '</div>',
		) );
		?>
	</div>
	<!-- .entry-content -->

	<footer class="entry-footer">
		<?php feyarose_entry_footer(); ?>
	</footer>
	<!-- .entry-footer -->
</article><!-- #post-## -->
