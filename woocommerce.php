<?php

if ( ! class_exists( 'bss_woo' ) ) {

    class bss_woo {

        public function __construct() {

            add_filter( 'woocommerce_checkout_fields' , array($this, 'custom_override_checkout_fields') );

            add_filter( 'woocommerce_return_to_shop_redirect', array($this, 'wc_empty_cart_redirect_url' ));

            add_action( 'woocommerce_review_order_before_payment', 'my_back_to_cart_link' );

        }

        static function custom_override_checkout_fields( $fields ) {

            //unset($fields['billing']['billing_first_name']);

            unset($fields['billing']['billing_title']);

            //unset($fields['billing']['billing_last_name']);

            unset($fields['billing']['billing_company']);

            //unset($fields['billing']['billing_address_1']);

            //unset($fields['billing']['billing_address_2']);

            //unset($fields['billing']['billing_city']);

            //unset($fields['billing']['billing_postcode']);

            //unset($fields['billing']['billing_country']);

            //unset($fields['billing']['billing_state']);

            unset($fields['billing']['billing_phone']);

            //unset($fields['order']['order_comments']);


            //unset($fields['billing']['billing_last_name']);

            //unset($fields['billing']['billing_city']);

            return $fields;  

        } 

        static function woo_product_obj( $product_id ) {

            $_pf = new WC_Product_Factory();  

            $product = $_pf->get_product($product_id);

            return $product;      

        }

        public function get_product_info( $product_id, $class = null ) {

            $product = $this->woo_product_obj( $product_id );

            return wc_get_template_html( 'woocommerce/single-product/add-to-cart/simple.php', array("product" => $product, "class" => $class) );

        }

        static function wc_empty_cart_redirect_url() {

            return '/';

        }

        static function my_back_to_cart_link(){

            global $woocommerce;

            $cartUrl = $woocommerce->cart->get_cart_url();

            $backToCartLink="<p class='backtocart'><a class='btn btn-primary' href='".$cartUrl."'>".__('Edit Cart','wooint')."</a></p>";

            echo $backToCartLink;

        }

    }

}

$bss_woo = new bss_woo();

?>