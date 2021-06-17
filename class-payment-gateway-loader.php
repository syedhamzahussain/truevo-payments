<?php

class Truevo_Loader {

    /**
     * Class constructor, more about it in Step 3
     */
    public function __construct() {
        add_action('wp', array($this, 'mark_payment_complete'), 20);
        add_filter('wc_get_template', array($this, 'override_template'), 99, 5);
        add_filter('woocommerce_checkout_redirect_empty_cart', array( $this, 'prevent_redirect'), 99);
    }

    function mark_payment_complete() {
        global $wp;
        if (isset($wp->query_vars['truevo-pay'])) {
            $order_id = absint($wp->query_vars['truevo-pay']);
            $order = wc_get_order($order_id);

            if ($order_id === $order->get_id() && $order->needs_payment()) {
                if ('truevo' === $order->get_payment_method() && isset($_GET['id']) && !empty($_GET['id'])) {
                    $transaction_id = $_GET['id'];
                    if (!$order->has_status(array('processing', 'completed'))) {
                        $this->verify_payment($transaction_id, $order);
                    }
                }
            }
        }
    }

    function override_template($template, $template_name, $args, $template_path, $default_pat) {
        global $wp;


        if (!empty($wp->query_vars['truevo-pay'])) {

            if ($template_name == 'checkout/form-checkout.php') {
                $template = dirname(__FILE__) . '/woocommerce/checkout/truevo-pay.php';
            }
        }

        return $template;
    }

    function verify_payment($transaction_id, $order) {
        
        include_once 'class-truevo-gateway-request.php';
        $method = $order->get_payment_method();
        $gateway = WC()->payment_gateways->payment_gateways()[$method];

        $truevo_request = new WC_Gateway_Truevo_Request($gateway);
        $response = $truevo_request->payment_request($transaction_id);

        if (!isset($response->id)) {
            return;
        }

        $code = $response->result->code;
        $url = '';
        if (preg_match("/^(000.000.|000.100.1|000.[36])/", $code) || preg_match("/^(000.400.0[^3]|000.400.[0-1]{2}0)/", $code)) {
            $note = "Trevo charge complete (Charge ID:$transaction_id)";
            $order->add_order_note($note);
            $order->payment_complete($transaction_id);
            $url = $order->get_checkout_order_received_url();
        } else {
            $note = $response->result->description;
            wc_add_notice($note, 'error');
            $order->add_order_note("Trevo charge Failed: " . $note);
            $url = $truevo_request->get_request_url($order, false);
        }
        wp_redirect($url);
        exit();
    }
    
    /**
     * Prevent redirection on checkout if user is paying for pending order.
     * @global object $wp
     * @param string $redirect
     * @return boolean
     */
    function prevent_redirect( $redirect ){
        global $wp;

        if (isset($wp->query_vars['truevo-pay'])) {
            $order_id = absint($wp->query_vars['truevo-pay']);
            $order = wc_get_order($order_id);

            if ( $order->needs_payment()) {
                if ('truevo' === $order->get_payment_method() && isset($_GET['truevo_request']) && !empty($_GET['truevo_request'])) {
                    return false;
                }
            }
        }
        return $redirect;
    }

}

new Truevo_Loader();
