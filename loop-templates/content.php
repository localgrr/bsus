<?php
/**
 * Post rendering content according to caller of get_template_part.
 *
 * @package understrap
 */

?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<header class="entry-header">

		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ),
		'</a></h2>' ); ?>

		<?php if ( 'post' == get_post_type() ) : ?>

			<div class="entry-meta">
				<?php understrap_posted_on(); ?>
			</div><!-- .entry-meta -->

		<?php endif; ?>
		<?php if ( 'class' == get_post_type() ) : 
			global $wpdb;
			$ev = get_field("class_events");
			$evid = $ev[0]["event"];
			$results = $wpdb->get_results( 
				$wpdb->prepare("SELECT start,end FROM {$wpdb->prefix}ai1ec_events WHERE post_id=%d", $evid) 
			);
			if($results[0]) {
				$format = "H:i";
				$start = $results[0]->start;
				$end = $results[0]->end;
				$start_time = date($format,$start);
				$end_time = date($format,$end);
				echo '<strong>' . date("d.m.Y",$end) . ' : ' . $start_time . ' to ' . $end_time . '</strong>';
			}
		?>

			<div class="entry-meta">
				<?php //understrap_posted_on(); ?>
			</div><!-- .entry-meta -->

		<?php endif; ?>

	</header><!-- .entry-header -->

	<?php echo get_the_post_thumbnail( $post->ID, 'large' ); ?>

	<div class="entry-content">
 
		<?php
		the_excerpt();
		?>

		<?php
		wp_link_pages( array(
			'before' => '<div class="page-links">' . __( 'Pages:', 'understrap' ),
			'after'  => '</div>',
		) );
		?>

	</div><!-- .entry-content -->

	<footer class="entry-footer">

		<?php understrap_entry_footer(); ?>

	</footer><!-- .entry-footer -->

</article><!-- #post-## -->
