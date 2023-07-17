<?php

abstract class GssmBase {

    protected $postType;
    protected $metaKey = 'gssm_data';

    public function __construct() {
        add_action('init', [$this, 'create_post_type']);
        add_action('add_meta_boxes', [$this, 'add_custom_meta_box']);
    }

    abstract function create_post_type();

    // Adding the admin metabox
    function add_custom_meta_box() {
        add_meta_box(
            $this->postType . '_metabox', // Unique ID
            'Details', // Title
            [$this, 'display_custom_metabox'], // Callback function
            $this->postType, // Post type to display the meta box
            'normal', // Context (normal, side, advanced)
            'default' // Priority (default, high, low, core)
        );
    }

    function display_custom_metabox($post) {

        $id = $post->ID;
        $meta = get_post_meta($id);
        $post_date = get_the_date( 'F j, Y', $id );
        $post_type = get_post_type($id);

        $stripe_account = get_post_meta($id, 'gssm_stripe_account', true);
        $order_id = get_post_meta($id, 'gssm_order_id', true);
        $amount = get_post_meta($id, 'gssm_amount', true);

        echo "<p>Created date : $post_date";

        if ( $post_type == 'gssm_escrow' ){

            $release_request = get_post_meta($id, 'gssm_release_requested', true);
            $release_approved = get_post_meta($id, 'gssm_release_approved', true);
            $releasable = get_post_meta($id, 'gssm_releasable', true);

            print_r($releasable);

        } else if ( $post_type == 'gssm_transfer' ){


        }

        echo '<pre>';
        print_r($meta);
        echo '</pre>';
        echo '<pre>';
        print_r($post_date);
        echo '</pre>';
        echo '<pre>';
        print_r($post_type);
        echo '</pre>';

    }

}

?>