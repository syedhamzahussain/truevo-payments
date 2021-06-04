<?php

/**
 * Generates requests to send to Truevo.
 */
class WC_Gateway_Truevo_Request {

    public function __construct($gateway) {
        $this->gateway = $gateway;
    }

    /**
     * Get the PayPal request URL for an order.
     *
     * @param  WC_Order $order Order object.
     * @param  bool     $sandbox Whether to use sandbox mode or not.
     * @return string
     */
    public function get_request_url($order, $sandbox = false) {
        $this->endpoint = wc_get_checkout_url(). 'truevo-pay/'.$order->get_id().'/';
        /*
        $paypal_args = [];$this->get_paypal_args($order);
        
        $mask = array(
            'first_name' => '***',
            'last_name' => '***',
            'address1' => '***',
            'address2' => '***',
            'city' => '***',
            'state' => '***',
            'zip' => '***',
            'country' => '***',
            'email' => '***@***',
            'night_phone_a' => '***',
            'night_phone_b' => '***',
            'night_phone_c' => '***',
        );

       // echo 'Truevo Request Args for order ' . $order->get_order_number() . ': ' . wc_print_r(array_merge($paypal_args, array_intersect_key($mask, $paypal_args)), true);
        
         */return $this->endpoint ;//. http_build_query($paypal_args, '', '&');
         
    }

}
