<?php

// Plugin name: Goods & Services Stripe Marketplace 2

$plugin_url = WP_PLUGIN_DIR;
// $plugin_url = str_replace('/', '\\', $plugin_url);
require_once $plugin_url . '/gssm-plugin/inc/stripe/init.php';

define('GSSM_STRIPE_PUBLIC', 'sk_test_51N9a3qFcAW2surlwPRHvtD7fxctLrDQvYdQrTOflchg66Etk1VJMz5fSdX906dxz1gCnMIHSewXeIYG2PgTSST2o006jdKU3Cd');
define('GSSM_CLIENT_ID', 'ca_NvQvRUbTRYyiuGtDHimtqBNfnnLCHIwx');
define('GSSM_REDIRECT_URI', 'http://137.184.83.232:8000/wp-json/gssm/v1/endpoint');

// include 'classes/WooCustomCheckout.php';
include 'classes/GssmBase.php';
include 'classes/GssmTransfer.php';
include 'classes/GssmEscrow.php';
include 'classes/GssmStripe.php';
include 'classes/Gssm.php';
include 'classes/GssmWoo.php';

/********************
*** ENQUEUE
********************/

add_action( 'wp_enqueue_scripts', 'gssm_enqueue' );

function gssm_enqueue() {

    // get current time
    $time = time();
    $version = $time;

    wp_enqueue_script( 'stripe-js', 'https://js.stripe.com/v3/', array(), 3, false );
    wp_enqueue_style( 'marketplace-style', plugin_dir_url( __FILE__ ) . 'assets/css/gssm.css', array(), $version, 'all' );
    wp_enqueue_script( 'marketplace-script', plugin_dir_url( __FILE__ ) . 'assets/js/gssm.js', array(), $version, true );

    wp_localize_script( 'marketplace-script', 'my_ajax_object', array( 
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'logout_nonce' => wp_create_nonce('ajax-logout-nonce'),
    ));

}


/********************
*** CUSTOM TEMPLATE
********************/

// // add custom template

// function my_plugin_add_template_to_select( $post_templates, $wp_theme, $post, $post_type ) {

//     // Add custom template named template-debug.php to select dropdown 
//     $post_templates[ 'debug.php' ] = 'My Custom Template';

//     return $post_templates;
// }
// add_filter( 'theme_page_templates', 'my_plugin_add_template_to_select', 10, 4 );

// function my_plugin_change_template( $template ) {

//     // Get global post
//     global $post;

//     // Get meta from post
//     $page_template = get_post_meta( $post->ID, '_wp_page_template', true );
        
//     // If page template is not empty and is debug.php
//     if ( !empty( $page_template ) && $page_template == 'debug.php' ) {
//         // return the template file from plugin directory
//         return plugin_dir_path( __FILE__ ) . 'debug.php';
//     }

//     // Return original
//     return $template;
// }
// add_filter( 'template_include', 'my_plugin_change_template' );

/********************
*** MISC
********************/

add_filter('use_block_editor_for_post', '__return_false', 10);
