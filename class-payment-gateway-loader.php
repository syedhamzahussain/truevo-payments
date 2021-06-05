<?php

class Truevo_Loader {

    /**
     * Class constructor, more about it in Step 3
     */
    public function __construct() {
        add_action('wp', array($this, 'mark_payment_complete'), 20);
        add_filter('wc_get_template', array($this, 'override_template'), 99, 5);
    }

    function mark_payment_complete() {
        global $wp;

        if (isset($wp->query_vars['order-received'])) {
            wc_nocache_headers();
            ob_start();

            // Pay for existing order.
            $order_key = wp_unslash($_GET['key']);
            $order_id = absint($wp->query_vars['order-received']);
            $order = wc_get_order($order_id);

            if ($order_id === $order->get_id() && hash_equals($order->get_order_key(), $order_key) && $order->needs_payment()) {


                if ('truevo' === $order->get_payment_method() && isset($_GET['id']) && !empty($_GET['id'])) {
                    $transaction_id = $_GET['id'];
                    if (!$order->has_status(array('processing', 'completed'))) {

                        $note = "Trevo charge complete (Charge ID:$transaction_id)";
                        $order->add_order_note($note);
                        $order->payment_complete($transaction_id);
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

}

new Truevo_Loader();
