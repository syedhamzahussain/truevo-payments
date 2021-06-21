<?php
global $wp;
$order_id = $wp->query_vars['truevo-pay'];
$order = wc_get_order($order_id);
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

$redirect_url = wc_get_checkout_url() . 'truevo-pay/' . $order->get_id() . '/?truevo_request=' . $truevo_request;
wc_print_notices();
?>
<script>
    var wpwlOptions = {
        onReady: function() {
                        let customer_email = '<?= $order->get_billing_email() ?>';
                        let custom_html= '<class="wpwl-group wpwl-group-custom-html wpwl-clearfix"><?= ($gateway->custom_html) ?></div>';
                        let customerEmailHtml = '<div class="wpwl-label wpwl-label-custom" style="display:inline-block">Email Address: </div>' + '<div class="wpwl-wrapper wpwl-wrapper-custom" style="display:inline-block">' + '<input name="customer.email" value="'+customer_email+'"  />' + '</div>';
                        jQuery('form.wpwl-form-card').find('.wpwl-button').before(customerEmailHtml);
                      //  jQuery('form.wpwl-form-card').find('.wpwl-group-submit').after(custom_html);
                },
            billingAddress: {
                    country: '<?= $order->get_billing_country() ?>',
                    state: '<?= $order->get_billing_state() ?>',
                    city: '<?= $order->get_billing_city() ?>',
                    postcode: '<?= $order->get_billing_postcode() ?>',
                    street1: '<?= $order->get_billing_address_1() ?>',
                    street2: '<?= $order->get_billing_address_2() ?>'
            },

            mandatoryBillingFields: {
                    country: true,
                    state: false,
                    city: true,
                    postcode: true,
                    street1: true,
                    street2: false
            }
             
    }
</script>
<script
src="<?php echo $truevo_base_url ?>/v1/paymentWidgets.js?checkoutId=<?php echo $truevo_request; ?>'"></script>
<form action="<?= $redirect_url ?>" class="paymentWidgets" data-brands="VISA MASTER AMEX"></form>
<div><?php echo isset($gateway->custom_html)?$gateway->custom_html:'' ;?></div>