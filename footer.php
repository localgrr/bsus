<?php

/**

 * The template for displaying the footer.

 *

 * Contains the closing of the #content div and all content after

 *

 * @package understrap

 */

  

$the_theme = wp_get_theme();

$container = get_theme_mod( 'understrap_container_type' );

?> 



<?php get_sidebar( 'footerfull' ); ?>



<div class="wrapper" id="wrapper-footer">



	<div class="<?php echo esc_attr( $container ); ?>">



		<div class="row"> 



			<div class="col-md-12">



				<footer class="site-footer" id="colophon">



					<div class="site-info">



							&copy;2018 Berlin Stand-up School. Caroline Clifford &amp; Paul Salamone

							<a href="mailto:info@berlinstandupschool.com">Email us</a>

							<a href="/impressum" style="float:right">Impressum</a>
							<a href="/data-security" style="float:right; margin-right:20px">Privacy Policy</a>
							<a href="/refund-policy" style="float:right; margin-right:20px">Refund Policy</a>

					</div><!-- .site-info -->



				</footer><!-- #colophon -->



			</div><!--col end -->



		</div><!-- row end -->



	</div><!-- container end -->



</div><!-- wrapper end -->



</div><!-- #page we need this extra closing tag here -->



<?php include "inc/mailchimp-modal.php"; ?>

<?php include "inc/enquiry-modal.php"; ?>

<?php include "inc/waiting-list-modal.php"; ?>


<?php wp_footer(); ?>



</body>



</html>



