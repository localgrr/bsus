<?php

/**

 * Simple product add to cart

 *

 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/simple.php.

 *

 * HOWEVER, on occasion WooCommerce will need to update template files and you

 * (the theme developer) will need to copy the new files to your theme to

 * maintain compatibility. We try to do this as little as possible, but it does

 * happen. When this occurs the version of the template file will be bumped and

 * the readme will list any important changes.

 *

 * @see https://docs.woocommerce.com/document/template-structure/

 * @package WooCommerce/Templates

 * @version 3.4.0

 */



defined( 'ABSPATH' ) || exit;



if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

if( ! $product ) {

	return;

}

if ( ! $product->is_purchasable() ) {

	return;

} 


if ( $product->is_in_stock() ) : ?>

	

<div class="simple-woo-add-form">

	<div class="price-info">

	<?php 



	if( $product->is_on_sale() ) {

	?>

		<?php if($product->get_date_on_sale_to()) { ?><strong>Early Bird price until <?php echo wc_format_datetime($product->get_date_on_sale_to()); ?></strong><br><?php } ?>

		<del>&euro;<?php echo $product->get_regular_price(); ?></del> 

		&euro;<?php echo $product->get_sale_price(); 

	} else { 

		echo "&euro;" . $product->get_regular_price(); 

	} 



	?>

	</div>



	

		<?php //echo do_shortcode('[add_to_cart id="' . $product->get_id() . '" show_price="false"  quantity="1" style=""]'); ?>
		<a href="/?add-to-cart=<?=$product->get_id(); ?>" data-quantity="1" class="add_to_cart_button product_type_simple single_add_to_cart_button btn btn-outline-primary btn-block ajax_add_to_cart" data-product_id="<?=$product->get_id(); ?>" data-product_sku="<?=$product->get_sku(); ?>" rel="nofollow"> Add to cart</a>




</div>

<?php endif; ?>

