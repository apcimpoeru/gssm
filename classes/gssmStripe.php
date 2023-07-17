<?php

class GssmStripe {
    
    private $apiKey;

    public function __construct($apiKey) {

        $this->apiKey = $apiKey;

        add_action('rest_api_init', array($this, 'register_api_routes'));
        \Stripe\Stripe::setApiKey($apiKey);

    }

    public function register_api_routes() {
        register_rest_route('gssm/v1', '/endpoint', array(
            'methods' => 'GET',
            'callback' => array($this, 'endpoint'),
        ));
    }

    public function confirm_stripe_account($code){

        try {
            $response = \Stripe\OAuth::token([
                'grant_type' => 'authorization_code',
                'code' => $code,
            ]);
    
            $connected_account_id = $response->stripe_user_id;
            $res = $connected_account_id;
    
        } catch (\Stripe\Exception\OAuth\OAuthErrorException $e) {
            $res = $e;
        }

        return $res;

    }

    public function endpoint(WP_REST_Request $request) {

        $state = $request->get_param('state');
        $code = $request->get_param('code');

        $user_id = $this->get_user_id_from_state($state);

        $stripe_account = $this->confirm_stripe_account($code);
        $error = false;

        if ( $stripe_account ){
            update_user_meta($user_id, 'gssm_stripe_account', $stripe_account);
        } else {
            // TODO
            // Error handling
            $error = true;
        }
        
        if ($error){
            $response = array(
                'success' => 0
            );
        } else {
            $response = array(
                'success' => 1,
                'message' => $stripe_account,
            );
        }
        

        return new WP_REST_Response($response, 200);

    }

    public function get_user_id_from_state($state){
        
        $userQuery = new WP_User_Query(array(
            'meta_key' => 'gssm_stripe_state',
            'meta_value' => $state
        ));
        
        $users = $userQuery->get_results();
        
        if ($users) {
            $user = $users[0];
        } else {
            $user = false;
        }

        return $user->data->ID;

    }

    public function get_connect_url(){

        // adds a state to the user so we can find him in the endpoint after he signed up with Stripe
        // but only generates the state if the user doesn't already have one
        $userID = get_current_user_ID();
        $user_state = get_user_meta($userID, 'gssm_stripe_state');
        $stripe_account = get_user_meta($userID, 'gssm_stripe_account');

        if ( !$userID ){
            return 'not_logged_in';
        }
        if ( $stripe_account ){
            return 'stripe_account';
        }

        if (!$user_state){
            $user_state = $user_state[0];
            do {
                // Generate a new identifier
                $state = bin2hex(random_bytes(15));
                
                // Check if the identifier has been used before
                $userQuery = new WP_User_Query(array(
                    'meta_key' => 'gssm_stripe_state',
                    'meta_value' => $state
                ));
                
                $users = $userQuery->get_results();
            
            } while (!empty($users));

            update_user_meta($userID, 'gssm_stripe_state', $state);
            
        } else {
            $state = $user_state;
        }

        $clientId = GSSM_CLIENT_ID; 
        $redirectUri = GSSM_REDIRECT_URI; 
        $state = $state[0];

        $url = "https://connect.stripe.com/oauth/authorize?response_type=code&client_id=$clientId&scope=read_write&redirect_uri=$redirectUri&state=$state";

        return $url;
        
    }

}

// $gssmStripe = new gssmStripe(GSSM_STRIPE_PUBLIC);