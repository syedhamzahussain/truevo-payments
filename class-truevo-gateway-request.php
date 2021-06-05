<?php

/**
 * Generates requests to send to Truevo.
 */
class WC_Gateway_Truevo_Request {

    public function __construct($gateway) {
        $this->gateway = $gateway;
    }
    
    function truevo_request( $order ,$sandbox = false) {
        
        $order_total = $order->get_total();
        $url = "https://test.truevo.eu/v1/checkouts";
        if(!$sandbox){
            $url = "https://truevo.eu/v1/checkouts";
        }
        
        $data = "entityId=8ac7a4c779c0fcf10179c29f18d406d2" .
            "&amount=$order_total" .
            "&currency=USD" .
            "&paymentType=DB";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer OGFjN2E0Y2E2OTZiOWZjNzAxNjk2ZDIyZjVkMTAzM2F8dHNFeHNjQmE4Wg=='));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        return json_decode($responseData);
    }

    /**
     * Get the Truevo request URL for the order.
     *
     * @param  WC_Order $order Order object.
     * @param  bool     $sandbox Whether to use sandbox mode or not.
     * @return string
     */
    public function get_request_url($order, $sandbox = true) {
        $request_id = $this->truevo_request( $order, $sandbox )->id;
        $this->endpoint = wc_get_checkout_url(). 'truevo-pay/'.$order->get_id().'/?truevo_request='.$request_id;
        return $this->endpoint;
         
    }

}
