<?php

/**
 * Generates requests to send to Truevo.
 */
class WC_Gateway_Truevo_Request {

    public function __construct($gateway) {
        $this->gateway = $gateway;
    }

    /**
     * Send request to truevo for request id.
     * @param WC_Order $order
     * @param bool $sandbox
     * @return object
     */
    function truevo_request($order, $sandbox = false) {

        $base_url = $this->gateway->base_url;

        $order_total = $order->get_total();
        $url = $base_url . "/v1/checkouts";
        $entity_id = $this->gateway->entity_id;
        $bearer_token = $this->gateway->bearer_token;
        $order_currency = $order->get_currency();
        $data = "entityId=$entity_id" .
            "&amount=$order_total" .
            "&currency=" . $order_currency .
            "&paymentType=DB";
        if ($this->enabled_test_mode == 'yes') {
            $data .= "&testMode=INTERNAL";
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer ' . $bearer_token));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);


        if (curl_errno($ch)) {
            return curl_error($ch);
        }

        curl_close($ch);

        $response = json_decode($responseData);
        if (empty($response)) {
            $logger = new WC_Logger();
            $log_entry = print_r($responseData, true);
            $logger->add('truevo-gateway', $log_entry);
        }
        return $response;
    }

    /**
     * Get the Truevo request URL for the order.
     *
     * @param  WC_Order $order Order object.
     * @param  bool     $sandbox Whether to use sandbox mode or not.
     * @return string
     */
    public function get_request_url($order, $sandbox = true) {
        $request = $this->truevo_request($order, $sandbox);

        $request_id = '';
        if (isset($request->id)) {
            $request_id = $request->id;
            $this->endpoint = wc_get_checkout_url() . 'truevo-pay/' . $order->get_id() . '/?truevo_request=' . $request_id;
            return $this->endpoint;
        } else {
            $logger = new WC_Logger();
            $log_entry = print_r($request, true);
            $logger->add('truevo-gateway', $log_entry);
            return $order->get_checkout_order_received_url();
        }
    }

    function payment_request($transaction_id) {
        
        $base_url = $this->gateway->base_url;
        $entity_id = $this->gateway->entity_id;
        $bearer_token = $this->gateway->bearer_token;
        $url = $base_url . "/v1/checkouts/$transaction_id/payment";
        $url .= "?entityId=$entity_id";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer ' . $bearer_token));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        $response = json_decode($responseData);
        if (empty($response)) {
            $logger = new WC_Logger();
            $log_entry = print_r($responseData, true);
            $logger->add('truevo-gateway', $log_entry);
        }
        return $response;
    }

}
