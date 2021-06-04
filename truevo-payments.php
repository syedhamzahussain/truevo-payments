<?php

/*
 * Plugin Name: Truevo Payment Gateway
 * Description: Truevo Payment Gateway provide you Truevo Payment Integration.
 * Author: Syed Hamza Hussain
 * Author URI: https://www.upwork.com/fl/syedhamzahussain
 * Version: 1.1.1.0
 *
 */

/*
 * This action hook registers our PHP class as a WooCommerce payment gateway
 */
add_filter('woocommerce_payment_gateways', 'tpg_add_class');

function tpg_add_class($gateways) {

    $gateways[] = 'WC_Truevo_Gateway'; // your class name is here
    return $gateways;
}

/*
 * The class itself, please note that it is inside plugins_loaded action hook
 */
add_action('plugins_loaded', 'tpg_init_gateway_class');

function tpg_init_gateway_class() {
    require_once 'class-truevo-payment-gateway.php';
}

function truevo_pay_endpoints() {
    add_rewrite_endpoint('truevo-pay', EP_ROOT | EP_PAGES);
}

add_action('init', 'truevo_pay_endpoints');

function truevo_pay_query_vars($vars) {
    $vars[] = 'truevo-pay';

    return $vars;
}

add_filter('query_vars', 'truevo_pay_query_vars', 0);

function truevo_pay_flush_rewrite_rules() {
    flush_rewrite_rules();
}

add_action('wp_loaded', 'truevo_pay_flush_rewrite_rules');

function my_custom_endpoint_content() {

    include 'woocommerce/checkout/truevo-pay.php';
}

add_action('woocommerce_truevo-pay_endpoint', 'my_custom_endpoint_content');

add_filter('wc_get_template', function($template, $template_name, $args, $template_path, $default_pat) {
    global $wp;
    
    //  print_r($wp->query_vars);
    if (!empty($wp->query_vars['truevo-pay'])) {
        
        if ($template_name == 'checkout/form-checkout.php') {
            $template = dirname(__FILE__) . '/woocommerce/checkout/truevo-pay.php';
        }
        //   echo $template_name;;
        //self::order_pay( $wp->query_vars['order-pay'] );
    }

    return $template;
}, 99, 5);

add_action('wp', function() {
    global $wp;

    if (isset($wp->query_vars['order-received'])) {
        wc_nocache_headers();

        ob_start();

        // Pay for existing order.
        $order_key = wp_unslash($_GET['key']);
        $order_id = absint($wp->query_vars['order-received']);
        $order = wc_get_order($order_id);

        if ($order_id === $order->get_id() && hash_equals($order->get_order_key(), $order_key) && $order->needs_payment()) {


            if ('truevo' === $order->get_payment_method() && isset($_GET['id']) && !empty($_GET['id'] ) ) {
                $transaction_id = $_GET['id'];
                if ( ! $order->has_status( array( 'processing', 'completed' ) ) ) {
                    $order->payment_complete( $transaction_id );
                   
                }
            }
        }
    }
}, 20);
