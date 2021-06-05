<?php

class WC_Truevo_Gateway extends WC_Payment_Gateway {

    public $testmode = true;

    /**
     * Class constructor, more about it in Step 3
     */
    public function __construct() {

        $this->id = 'truevo'; // payment gateway plugin ID
        $this->icon = ''; // URL of the icon that will be displayed on checkout page near your gateway name
        $this->has_fields = true; // in case you need a custom credit card form
        $this->method_title = 'Truevo';
        $this->method_description = 'Description of Truevo payment gateway'; // will be displayed on the options page
        // gateways can support subscriptions, refunds, saved payment methods,
        // but in this tutorial we begin with simple payments
        $this->supports = array(
            'products'
        );

        // Method with all the options fields
        $this->init_form_fields();

        // Load the settings.
        $this->init_settings();
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->enabled = $this->get_option('enabled');

        $this->entity_id = $this->get_option('truevo_entity_id');
        $this->bearer_token = $this->get_option('truevo_bearer_token');
        $this->base_url = $this->get_option('truevo_base_url');


        // This action hook saves the settings
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

        // We need custom JavaScript to obtain a token
        add_action('wp_enqueue_scripts', array($this, 'payment_scripts'));
    }

    public function request() {
        $url = "https://test.truevo.eu/v1/checkouts";
        $data = "entityId=8ac7a4c779c0fcf10179c29f18d406d2" .
            "&amount=92.00" .
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
     * Plugin options, we deal with it in Step 3 too
     */
    public function init_form_fields() {

        $this->form_fields = array(
            'enabled' => array(
                'title' => 'Enable/Disable',
                'label' => 'Enable Truevo Gateway',
                'type' => 'checkbox',
                'description' => '',
                'default' => 'no'
            ),
            'title' => array(
                'title' => 'Title',
                'type' => 'text',
                'description' => 'This controls the title which the user sees during checkout.',
                'default' => 'Pay By Truevo',
                'desc_tip' => true,
            ),
            'description' => array(
                'title' => 'Description',
                'type' => 'textarea',
                'description' => 'This controls the description which the user sees during checkout.',
                'default' => 'Pay with your credit card via our Truevo payment gateway.',
            ),
            'entity_id' => array(
                'title' => 'Entity ID',
                'type' => 'text'
            ),
            'bearer_token' => array(
                'title' => 'Bearer Token',
                'type' => 'text',
            ),
            'base_url' => array(
                'title' => 'Base URL',
                'type' => 'url'
            ),
        );
    }

    /**
     * You will need it if you want your custom credit card form, Step 4 is about it
     */
    public function payment_fields() {
    }

    /*
     * Custom CSS and JS, in most cases required only when you decided to go with a custom credit card form
     */

    public function payment_scripts() {
        
    }

    /*
     * Fields validation, more in Step 5
     */

    public function validate_fields() {
        
    }

    /*
     * We're processing the payments here, everything about it is in Step 5
     */

    public function process_payment($order_id) {

        include_once 'class-truevo-gateway-request.php';

        $truevo_request = new WC_Gateway_Truevo_Request($this);
        $order          = wc_get_order($order_id);

        // Return thankyou redirect
        return array(
            'result' => 'success',
            'redirect' => $truevo_request->get_request_url($order, $this->testmode),
        );
    }

    

}
