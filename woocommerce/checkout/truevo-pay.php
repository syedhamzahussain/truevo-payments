<?php
global $wp;
$order_id  = $wp->query_vars['truevo-pay'];
$order = wc_get_order($order_id);
//echo $order->get_checkout_order_received_url();

function truevo_request( $order_id ) {
        $order = wc_get_order($order_id);
        $order_total = $order->get_total();
        $url = "https://test.truevo.eu/v1/checkouts";
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
?>
<script src="https://test.truevo.eu/v1/paymentWidgets.js?checkoutId=<?php echo truevo_request( $order_id )->id; ?>'"></script>
<form action="<?=$order->get_checkout_order_received_url()?>" class="paymentWidgets" data-brands="VISA MASTER AMEX"></form>