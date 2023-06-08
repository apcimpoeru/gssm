<?php
/*
Template Name: My Custom Template
*/


get_header(); 


// get plugin dir location
$plugin_url = WP_PLUGIN_DIR;
$plugin_url = str_replace('/', '\\', $plugin_url);
require_once $plugin_url . '\marketplace\inc\stripe\init.php';

\Stripe\Stripe::setApiKey('sk_test_51N9a3qFcAW2surlwPRHvtD7fxctLrDQvYdQrTOflchg66Etk1VJMz5fSdX906dxz1gCnMIHSewXeIYG2PgTSST2o006jdKU3Cd'); // Your Stripe Secret Key

if (isset($_GET['code'])) { // If the OAuth response came with an authorization code
    $code = $_GET['code'];
    echo $code . '<br>';

    try {
        $response = \Stripe\OAuth::token([
            'grant_type' => 'authorization_code',
            'code' => $code,
        ]);

        $connected_account_id = $response->stripe_user_id;
        echo '<pre>';
        print_r($response);
        echo '</pre>';

        // Store this account ID for future use

    } catch (\Stripe\Exception\OAuth\OAuthErrorException $e) {
        // Error - you may want to handle this and display your own message
        print('Error! An error occurred when trying to connect your account.');
        // print the error
        echo '<pre>';
        print_r($e);
        echo '</pre>';
    }
} else {
    // There's an error. Stripe sends error related information in query parameters: error and error_description
    $error_description = $_GET['error_description'];
}

$clientId = 'ca_NvQvRUbTRYyiuGtDHimtqBNfnnLCHIwx'; // Your Stripe Connect client ID
$redirectUri = 'https://localhost/store/test'; // Your redirect URL

$url = "https://connect.stripe.com/oauth/authorize?response_type=code&client_id=$clientId&scope=read_write&redirect_uri=$redirectUri";

echo $url;

//////////////////////////////////////////////////////////////////////////////////////////////////////
global $woocommerce;
// Get the cart total as a float.
$cart_total = floatval($woocommerce->cart->get_total('edit'));

// Round the float to the nearest whole number.
$cart_total = round($cart_total);

echo $cart_total;

get_footer(); ?>