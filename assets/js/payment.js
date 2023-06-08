jQuery(document).ready(function($) {

    $('#stripe-payment-form').insertAfter('form.checkout.woocommerce-checkout');

    $('#stripe-payment-form').on('submit', function(event) {

        event.preventDefault();
        event.stopPropagation();

        checkout_data = {};

        // Retrieve billing info
        checkout_data.billing_first_name = $('#billing_first_name').val();
        checkout_data.billing_last_name = $('#billing_last_name').val();
        checkout_data.billing_company = $('#billing_company').val();
        checkout_data.billing_country = $('#billing_country').val();
        checkout_data.billing_address_1 = $('#billing_address_1').val();
        checkout_data.billing_address_2 = $('#billing_address_2').val();
        checkout_data.billing_city = $('#billing_city').val();
        checkout_data.billing_state = $('#billing_state').val();
        checkout_data.billing_postcode = $('#billing_postcode').val();
        checkout_data.billing_phone = $('#billing_phone').val();
        checkout_data.billing_email = $('#billing_email').val();

        // Retrieve shipping info
        checkout_data.shipping_first_name = $('#shipping_first_name').val();
        checkout_data.shipping_last_name = $('#shipping_last_name').val();
        checkout_data.shipping_company = $('#shipping_company').val();
        checkout_data.shipping_country = $('#shipping_country').val();
        checkout_data.shipping_address_1 = $('#shipping_address_1').val();
        checkout_data.shipping_address_2 = $('#shipping_address_2').val();
        checkout_data.shipping_city = $('#shipping_city').val();
        checkout_data.shipping_state = $('#shipping_state').val();
        checkout_data.shipping_postcode = $('#shipping_postcode').val();

        stripe.createToken(card).then(function(result) {
            if (result.error) {
                var errorElement = document.getElementById('card-errors');
                errorElement.textContent = result.error.message;
            } else {
                
                jQuery.ajax({

                    type: "post",
                    dataType: "json",
                    url: my_ajax_object.ajax_url,
                    data: {
                        action: 'checkout_handler',
                        stripe_token: result.token,
                        checkout_data: checkout_data
                    },
                    success: function(msg){
                        
                        // Redirect to thank you page
                        let checkout_url = msg.checkout_url + 'order-received/' + msg.id + '/?key=' + msg.key;
                        window.location.href = checkout_url;
            
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
        
                        console.log(xhr);
        
                    }
        
                });

            }
        });

    });

});