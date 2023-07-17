<?php

class Gssm {

    private $gssmStripe;

    public function __construct($gssmStripe) {
        $this->gssmStripe = $gssmStripe;
    }

    /**
     * Creates a transfer.
     * 
     * @param array $data Associative array containing:
     *      'stripe_account'    => (string) Stripe account the transfer is meant for,
     *      'buyer_id'          => (int) User ID of the buyer
     *      'order_id'          => (string) Order ID,
     *      'amount'            => (float)  Transfer amount.
     *
     * @return int|WP_Error The post ID on success. An array with they key of 'error' on error. 
     */
    public function create_transfer($data){

        $data['type'] = 'gssm_transfer';
        $post = $this->create_post($data);
        return $post;

    }

    /**
     * Creates an escrow.
     * 
     * @param array $data Associative array containing:
     *      'stripe_account'    => (string) Stripe account the transfer is meant for,
     *      'buyer_id'          => (int) User ID of the buyer
     *      'order_id'          => (string) Order ID,
     *      'amount'            => (float)  Transfer amount.
     *
     * @return int|WP_Error The post ID on success. An array with they key of 'error' on error. 
     */
    public function create_escrow($data){
        
        $data['type'] = 'gssm_escrow';

        $data['release_requested'] = false;
        $data['release_approved'] = false;
        $data['releasable'] = strtotime("+7 days");

        $post = $this->create_post($data);

        return $post;

    }

    /**
     * Links a product to a Stripe account so the owner will receive the funds when 
     * the product is purchased. 
     * 
     * @param:
     *      'stripe_account'    => (string) Stripe account the transfer is meant for,
     *      'productID'         => (string) Parent Product ID
     *
     */
    public function update_product_stripe_account($productID, $stripe_account){
        update_post_meta($productID, 'gssm_linked_stripe_account', $stripe_account);
    }

    /**
     * Product will be mark as "Goods" - the transfer will be done directly, without escrow 
     * 
     * @param:
     *      'productID'         => (string) Parent Product ID
     *
     */
    public function mark_product_as_goods($productID){
        update_post_meta($productID, 'gssm_product_type', 'goods');
    }

    /**
     * Product will be mark as "Service" - funds will be kept in escrow until the client releases it 
     * 
     * @param:
     *      'productID'         => (string) Parent Product ID
     *
     */
    public function mark_product_as_services($productID){
        update_post_meta($productID, 'gssm_product_type', 'services');
    }

    /**
     * Requests an escrow release. 
     * 
     * TODO
     *
     */
    public function escrow_release_request($data){

        $check = $this->check_escrow_data($data);
        if ($check['error']){
            return $check;
        }

        update_post_meta( $data['escrow_id'], 'gssm_release_requested', true );

    }

    public function escrow_release_approve($data){

        $check = $this->check_escrow_data($data);
        if ( !array_key_exists('error', $data) ){
            return $check;
        }

        update_post_meta( $data['escrow_id'], 'gssm_release_approved', true );

    }
    
    /**
     * Retrieves the Stripe account of a user account.
     * 
     * @param:
     *      'userID'    => (string) User ID,
     *
     * @return int|WP_Error The Stripe account on success, false on failure.
     */
    public function get_stripe_account($userID = null){
        if ( $userID == null ){
            $userID = get_current_user_id();
        }
        $stripe_account = get_user_meta($userID, 'gssm_stripe_account');
        if ( is_array($stripe_account) ){
            $res = $stripe_account[0];
        } else {
            $res = false;
        }
        return $res;
    }
    
    public function connect_button($text = 'Connect with Stripe '){

        $url = $this->gssmStripe->get_connect_url();

        if ( $url == 'not_logged_in' ){
            echo '<p>User not logged in.</p>';
        } else if ($url == 'stripe_account'){
            echo '<p>Already connected.</p>';
        } else {
            echo '<a href="' . $url . '">' . $text . '</a>';
        }

        // if ($url){
        //     echo '<a href="' . $url . '">' . $text . '</a>';
        // }

    } 

    // Helper functions
    function create_post($data){

        if ( !$data['stripe_account'] || !$data['order_id'] || !$data['amount'] ){

            $res = array(
                'error' => 'Needs a stripe account, order ID and an amount - please check documentation.',
            );
            return $res;

        };

        // Create post object
        $my_post = array(
            'post_title'    => $data['order_id'],
            'post_status'   => 'publish',
            'post_type'     => $data['type'],
            'post_author'   => 1,
        );
        
        // Insert the post into the database  
        $post = wp_insert_post( $my_post );

        if ($post <= 0){
            $res = array(
                'error' => 'Error creating post.',
            );
            return $res;
        }
        
        if ( $data['type'] == 'gssm_escrow' ){

            update_post_meta($post, 'gssm_release_requested', $data['release_requested']);
            update_post_meta($post, 'gssm_release_approved', $data['release_approved']);
            update_post_meta($post, 'gssm_releasable', $data['releasable']);

        }

        update_post_meta($post, 'gssm_stripe_account', $data['stripe_account']);
        update_post_meta($post, 'gssm_order_id', $data['order_id']);
        update_post_meta($post, 'gssm_amount', $data['amount']);
        update_post_meta($post, 'gssm_buyer_id', $data['buyer_id']);

        return $post;

    }

    function check_escrow_data($data){

        if ( !array_key_exists('escrow_id', $data) ){
            return array(
                'error' => 'Parameter missing.',
            );
        }

        $post_type = get_post_type($data['escrow_id']);

        if ( $post_type != 'gssm_escrow' ){
            return array(
                'error' => 'Post needs to be an escrow.',
            );
        }

        return true;

    }


}

$gssmStripe = new GssmStripe(GSSM_STRIPE_PUBLIC);
$gssm = new Gssm($gssmStripe);

?>