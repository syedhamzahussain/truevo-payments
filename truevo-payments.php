<?php
/*
 * Plugin Name: Truevo Payment Gateway
 * Description: Truevo Payment Gateway provide you Truevo Payment Integration.
 * Author: Syed Hamza Hussain
 * Author URI: https://www.upwork.com/fl/syedhamzahussain
 * Version: 1.1.1.3
 *
 */

require_once 'class-payment-gateway-loader.php';

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

