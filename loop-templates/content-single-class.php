
	<header class="entry-header">

		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

	</header><!-- .entry-header -->

	<?php 

	echo get_the_post_thumbnail( $id, 'large' );

	the_content();

	do_shortcode('[bss_get_teacher]'); ?>

	<div class="entry-content">

		<?php 

		echo do_shortcode('[shop_messages]');

		echo do_shortcode('[bss_class_times_and_location]');

		?>

	</div><!-- .entry-content --> 

</article><!-- #post-## -->