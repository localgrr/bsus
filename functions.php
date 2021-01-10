<?php

/**

 * Enqueue scripts and styles.

 */

require_once('woocommerce.php');

remove_action( 'wp_enqueue_scripts', 'wpcf7_recaptcha_enqueue_scripts' );

function pre_r($code) {

	echo '<pre>';

	print_r($code);

 	echo '</pre>';

}   

require get_template_directory() . '/inc/enqueue.php';

show_admin_bar( false );





include "inc/bss_functions.php";
include "inc/class_list.php";
include "inc/single_class.php";
include "inc/waiting_list.php";

$bss_functions = new bss_functions();


if (!function_exists('write_log')) {

    function write_log ( $log )  {

        if ( true === WP_DEBUG ) {

            if ( is_array( $log ) || is_object( $log ) ) {

                error_log( print_r( $log, true ) );

            } else {

                error_log( $log );

            }

        }

    }

}

// Woo speed repair

add_action( 'wp_enqueue_scripts', 'child_manage_woocommerce_styles', 99 );

function child_manage_woocommerce_styles() {

    //remove generator meta tag

    remove_action( 'wp_head', array( $GLOBALS['woocommerce'], 'generator' ) );

    //first check that woo exists to prevent fatal errors

    if ( function_exists( 'is_woocommerce' ) ) {

        //dequeue scripts and styles

        if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() ) {

            wp_dequeue_style( 'woocommerce_frontend_styles' );

            wp_dequeue_style( 'woocommerce_fancybox_styles' );

            wp_dequeue_style( 'woocommerce_chosen_styles' );

            wp_dequeue_style( 'woocommerce_prettyPhoto_css' );

            wp_dequeue_script( 'wc_price_slider' );

            wp_dequeue_script( 'wc-single-product' );

            wp_dequeue_script( 'wc-add-to-cart' );

            wp_dequeue_script( 'wc-cart-fragments' );

            wp_dequeue_script( 'wc-checkout' );

            wp_dequeue_script( 'wc-add-to-cart-variation' );

            wp_dequeue_script( 'wc-single-product' );

            wp_dequeue_script( 'wc-cart' );

            wp_dequeue_script( 'wc-chosen' );

            wp_dequeue_script( 'woocommerce' );

            wp_dequeue_script( 'prettyPhoto' );

            wp_dequeue_script( 'prettyPhoto-init' );

            wp_dequeue_script( 'jquery-blockui' );

            wp_dequeue_script( 'jquery-placeholder' );

            wp_dequeue_script( 'fancybox' );

            wp_dequeue_script( 'jqueryui' );

        }

    }

 }


add_filter('excerpt_more', 'new_excerpt_more');



