<?php

class GssmWoo {

    public function __construct() {
        add_action('woocommerce_before_shop_loop_item', [$this, 'remove_add_to_cart_button']);
        add_action('woocommerce_before_single_product_summary', [$this, 'remove_add_to_cart_button']);
    }

    public function remove_add_to_cart_button() {
        global $product;
    
        // Get product ID
        $product_id = $product->get_id();
    
        // Check if the product has the "test" meta
        $test_meta = get_post_meta($product_id, 'gssm_linked_stripe_account', true);
    
        // If "test" meta does not exist, remove "Add to Cart" button
        if (!$test_meta) {
            remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
            remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);

            add_action('woocommerce_after_shop_loop_item', [$this, 'print_custom_text'], 10);
            add_action('woocommerce_single_product_summary', [$this, 'print_custom_text'], 30);
        }
        
    }

    public function print_custom_text() {
        echo '<p>Product has no owner.</p>';
    }
}

$gssmWoo = new GssmWoo();

?>