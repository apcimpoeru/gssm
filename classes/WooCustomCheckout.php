<?php

class WooCommerceCustomCheckout {

    public function __construct() {
        add_filter('woocommerce_order_button_html', [$this, 'hide_place_order_button']);
        add_action('woocommerce_after_checkout_form', [$this, 'add_checkout_button']);
        add_action( 'wp_ajax_checkout_handler', [$this, 'checkout_handler']);
        add_action( 'wp_ajax_nopriv_checkout_handler', [$this, 'checkout_handler']);
    } 
    

    public function hide_place_order_button() {
        return '';
    }

    public function render_checkout_button_HTML(){
        ?>

        <form id="stripe-payment-form">
            
            <div>
                <label for="card-element">
                    Credit or debit card
                </label>
                <div id="card-element">
                    <!-- A Stripe Element will be inserted here. -->
                </div>

                <!-- Used to display form errors. -->
                <div id="card-errors" role="alert"></div>
            </div>

            <button>Submit Payment</button>

        </form>
        
        <script>
            var stripe = Stripe('<?php echo STRIPE_PUBLIC_KEY ?>');
            var elements = stripe.elements();

            var card = elements.create('card');
            card.mount('#card-element');
        </script>
        <?php

    }

    public function render_login_button_HTML(){
        ?>

        <a href="<?php echo wp_login_url() ?>">Login to checkout</a>

        <?php
    }

    public function add_checkout_button() {
        if (is_user_logged_in()) {
            $this->render_checkout_button_HTML();
        } else {
            $this->render_login_button_HTML();
        }
    }

    public function checkout_handler() {

        global $woocommerce;
        
        $token = $_POST['stripe_token']['id'];

        $cart_total = floatval($woocommerce->cart->get_total('edit'));
        $charge = round($cart_total);

        $checkout_data = $_POST['checkout_data'];

        $charge = $this->make_stripe_charge($token, $charge);

        if ( $charge->status == 'succeeded' ) {

            $order = $this->place_order($checkout_data);

            // get checkout page
            $checkout_url = wc_get_checkout_url();
            $order['checkout_url'] = $checkout_url;

            if ( $order['id'] ){
                echo json_encode($order);
            }

        }

        wp_die();

    }

    public function place_order($checkout_data){

        // Get the current user
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
    
        // Create the order
        $order = wc_create_order(array('customer_id' => $user_id));
    
        // Get the cart
        $cart = WC()->cart;
    
        // Add the cart items to the order
        foreach ($cart->get_cart() as $cart_item_key => $values) {
            $item_id = $order->add_product(
                $values['data'], 
                $values['quantity'], 
                array(
                    'variation' => $values['variation'],
                    'totals'    => array(
                        'subtotal'     => $values['line_subtotal'], 
                        'subtotal_tax' => $values['line_subtotal_tax'], 
                        'total'        => $values['line_total'], 
                        'tax'          => $values['line_tax'], 
                        'tax_data'     => $values['line_tax_data']
                    )
                )
            );
        }
    
        // Set the shipping address
        $order->set_address(array(
            'first_name' => $checkout_data['shipping_first_name'] ?? $checkout_data['billing_first_name'],
            'last_name'  => $checkout_data['shipping_last_name'] ?? $checkout_data['billing_last_name'],
            'company'    => $checkout_data['shipping_company'] ?? $checkout_data['billing_company'],
            'email'      => $checkout_data['shipping_email'] ?? $checkout_data['billing_email'],
            'phone'      => $checkout_data['shipping_phone'] ?? $checkout_data['billing_phone'],
            'address_1'  => $checkout_data['shipping_address_1'] ?? $checkout_data['billing_address_1'],
            'address_2'  => $checkout_data['shipping_address_2'] ?? $checkout_data['billing_address_2'],
            'city'       => $checkout_data['shipping_city'] ?? $checkout_data['billing_city'],
            'state'      => $checkout_data['shipping_state'] ?? $checkout_data['billing_state'],
            'postcode'   => $checkout_data['shipping_postcode'] ?? $checkout_data['billing_postcode'],
            'country'    => $checkout_data['shipping_country'] ?? $checkout_data['billing_country']
        ), 'shipping');
    
        // Set the billing address (assuming it's the same as the shipping address)
        $order->set_address(array(
            'first_name' => $checkout_data['billing_first_name'],
            'last_name'  => $checkout_data['billing_last_name'],
            'company'    => $checkout_data['billing_company'],
            'email'      => $checkout_data['billing_email'],
            'phone'      => $checkout_data['billing_phone'],
            'address_1'  => $checkout_data['billing_address_1'],
            'address_2'  => $checkout_data['billing_address_2'],
            'city'       => $checkout_data['billing_city'],
            'state'      => $checkout_data['billing_state'],
            'postcode'   => $checkout_data['billing_postcode'],
            'country'    => $checkout_data['billing_country']
        ), 'billing');
    
        // Set the order totals
        $order->set_total($cart->cart_contents_total, 'cart_contents_total');
        $order->set_total($cart->tax_total, 'tax_total');
        $order->set_total($cart->shipping_total, 'shipping_total');
        $order->set_total($cart->get_total_discount(), 'discount_total');
        $order->set_total($cart->total, 'total');
    
        // Save the order
        $order->save();
    
        // Empty the cart
        $cart->empty_cart();

        // place order in processing
        $order->update_status('processing');

        $response = array();
        $response['id'] = $order->get_id();
        $response['key'] = $order->get_order_key();

        return $response;

    }

    public function make_stripe_charge($token, $amount){

        if ( !$token ) {
            return false;
        }
    
        \Stripe\Stripe::setApiKey(STRIPE_PRIVATE_KEY);
    
        try {
            $charge = \Stripe\Charge::create([
                'amount' => $amount, // This is in cents, so $20.00
                'currency' => 'usd',
                'description' => 'Example charge',
                'source' => $token,
            ]);
    
            return $charge;
    
        } catch(\Stripe\Exception\CardException $e) {
            // Since it's a decline, \Stripe\Exception\CardException will be caught
            echo 'Status is:' . $e->getHttpStatus() . "\n";
            echo 'Type is:' . $e->getError()->type . "\n";
            echo 'Code is:' . $e->getError()->code . "\n";
            // param is '' in this case
            echo 'Param is:' . $e->getError()->param . "\n";
            echo 'Message is:' . $e->getError()->message . "\n";
        } catch (\Stripe\Exception\RateLimitException $e) {
            // Too many requests made to the API too quickly
            echo 'Too many requests made to the API too quickly' . "\n";
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Invalid parameters were supplied to Stripe's API
            echo 'Invalid parameters were supplied to Stripe\'s API' . "\n";
        } catch (\Stripe\Exception\AuthenticationException $e) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            echo 'Authentication with Stripe\'s API failed' . "\n";
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            // Network communication with Stripe failed
            echo 'Network communication with Stripe failed' . "\n";
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Display a generic error message to the user
            echo 'Error on Stripe\'s server' . "\n";
        } catch (Exception $e) {
            // Something else happened, completely unrelated to Stripe
            echo 'General error' . "\n";
        }
    }
    
}

$WooCommerceCustomCheckout = new WooCommerceCustomCheckout();