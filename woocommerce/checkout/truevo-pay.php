<?php
global $wp;
$order_id       = $wp->query_vars['truevo-pay'];
$order          = wc_get_order($order_id);
$truevo_request = '';

if (isset($_GET['truevo_request'])) {
    $truevo_request = $_GET['truevo_request'];
}
?>
<script src="https://test.truevo.eu/v1/paymentWidgets.js?checkoutId=<?php echo $truevo_request; ?>'"></script>
<form action="<?= $order->get_checkout_order_received_url() ?>" class="paymentWidgets" data-brands="VISA MASTER AMEX"></form>