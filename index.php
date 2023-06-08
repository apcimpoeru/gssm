<?php

// Plugin name: Stripe Services & Goods Marketplace

$plugin_url = WP_PLUGIN_DIR;
$plugin_url = str_replace('/', '\\', $plugin_url);
require_once $plugin_url . '\marketplace\inc\stripe\init.php';

include 'classes\WooCustomCheckout.php';

/********************
*** ENQUEUE
********************/

add_action( 'wp_enqueue_scripts', 'marketplaceEnqueueFiles' );

function marketplaceEnqueueFiles() {

    // get current time
    $time = time();
    $version = $time;

    wp_enqueue_script( 'stripe-js', 'https://js.stripe.com/v3/', array(), 3, false );
    wp_enqueue_style( 'marketplace-style', plugin_dir_url( __FILE__ ) . 'assets/css/style.css', array(), $version, 'all' );
    wp_enqueue_script( 'marketplace-script', plugin_dir_url( __FILE__ ) . 'assets/js/payment.js', array(), $version, true );

    wp_localize_script( 'marketplace-script', 'my_ajax_object', array( 
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'logout_nonce' => wp_create_nonce('ajax-logout-nonce'),
    ));

}


/********************
*** CUSTOM TEMPLATE
********************/

// add custom template

function my_plugin_add_template_to_select( $post_templates, $wp_theme, $post, $post_type ) {

    // Add custom template named template-debug.php to select dropdown 
    $post_templates[ 'debug.php' ] = 'My Custom Template';

    return $post_templates;
}
add_filter( 'theme_page_templates', 'my_plugin_add_template_to_select', 10, 4 );

function my_plugin_change_template( $template ) {

    // Get global post
    global $post;

    // Get meta from post
    $page_template = get_post_meta( $post->ID, '_wp_page_template', true );
        
    // If page template is not empty and is debug.php
    if ( !empty( $page_template ) && $page_template == 'debug.php' ) {
        // return the template file from plugin directory
        return plugin_dir_path( __FILE__ ) . 'debug.php';
    }

    // Return original
    return $template;
}
add_filter( 'template_include', 'my_plugin_change_template' );

/********************
*** MISC
********************/

add_filter('use_block_editor_for_post', '__return_false', 10);



function display_images_in_comments($comment_text) {
    // The regular expression for URL
    $pattern = '/(https?:\/\/[^\s]+)/i';

    // Find matches
    if (preg_match_all($pattern, $comment_text, $matches)) {
        foreach ($matches[0] as $match) {

            // Remove <a> tags
            $match = str_replace('"', '', $match);
            $match = str_replace("'", '', $match);

            $extensions = array('jpg', 'jpeg', 'gif', 'png', 'svg');
            $url_extension = pathinfo(parse_url($match, PHP_URL_PATH), PATHINFO_EXTENSION);

            if (in_array($url_extension, $extensions)) {
                $comment_text .= "<img src='$match'/>";
            }


        }
    }
    
    return $comment_text;
}
add_filter('comment_text', 'display_images_in_comments');