<?php
global $wp;
$order_id       = $wp->query_vars['truevo-pay'];
$order          = wc_get_order($order_id);
$truevo_request = '';

if (isset($_GET['truevo_request'])) {
    $truevo_request = $_GET['truevo_request'];
}

$method = $order->get_payment_method();
$gateway = WC()->payment_gateways->payment_gateways()[$method];
if ($method === 'truevo') {
    $truevo_base_url = $gateway->base_url;
} else {
    return;
}
?>
<script>
    var wpwlOptions = {
            billingAddress: {
                    country: '<?= $order->get_billing_country() ?>',
                    state: '<?= $order->get_billing_state() ?>',
                    city: '<?= $order->get_billing_city() ?>',
                    postcode: '<?= $order->get_billing_postcode() ?>',
                    street1: '<?= $order->get_billing_address_1() ?>',
                    street2: '<?= $order->get_billing_address_2() ?>',
                    email: 'ssss@sss.sss'
            },
                  
            mandatoryBillingFields: {
                    country: true,
                    state: false,
                    city: true,
                    postcode: true,
                    street1: true,
                    street2: false
            }
    }</script>
<script
src="<?php echo $truevo_base_url ?>/v1/paymentWidgets.js?checkoutId=<?php echo $truevo_request; ?>'"></script>
<form action="<?= $order->get_checkout_order_received_url() ?>" class="paymentWidgets" data-brands="VISA MASTER AMEX"></form>