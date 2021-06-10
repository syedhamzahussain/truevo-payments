<?php

class WC_Truevo_Gateway extends WC_Payment_Gateway {

    public $testmode = true;

    /**
     * Class constructor, more about it in Step 3
     */
    public function __construct() {

        $this->id   = 'truevo'; 
        $this->icon = ''; 
        $this->has_fields = true; 
        $this->method_title       = 'Truevo';
        $this->method_description = 'Description of Truevo payment gateway'; 
       
        $this->supports = array(
            'products'
        );

        // Method with all the options fields
        $this->init_form_fields();

        // Load the settings.
        $this->init_settings();
        $this->title       = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->enabled     = $this->get_option('enabled');
        $this->enabled_test_mode = $this->get_option('enabled_test_mode');

        $this->entity_id    = $this->get_option('entity_id');
        $this->bearer_token = $this->get_option('bearer_token');
        $this->base_url     = $this->get_option('base_url');


       
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

 
        add_action('wp_enqueue_scripts', array($this, 'payment_scripts'));
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
                'type' => 'url',
                'description' => 'Enter base url like https://test.truevo.eu without version i.e v1 or v2',
                'default' => 'https://truevo.eu',
                
            ),
            'enabled_test_mode' => array(
                'title' => 'Enable/Disable',
                'label' => 'Enable Test mode',
                'type' => 'checkbox',
                'description' => '',
                'default' => 'no'
            ),
        );
    }

    /**
     * You will need it if you want your custom credit card form, Step 4 is about it
     */
    public function payment_fields() {
        if ( $this->description ) {
			echo wp_kses_post( wpautop( wptexturize( wp_kses_post( $this->description ) ) ) );
		}          
       
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
        
        $response = array(
            'result' => 'success',
            'redirect' => $truevo_request->get_request_url($order, $this->testmode),
        );
        
       
        // Return thankyou redirect
        return  $response;
    }

    

}
