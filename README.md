<div class="markdown prose w-full break-words dark:prose-invert light">
   <h1>SSGM</h1>
   <hr>
   <h2>Overview</h2>
   <p>This developer-focused plugin provides a suite of helper functions designed to transform your standard online store into a fully-fledged online marketplace using Stripe Connect. It introduces a new level of complexity to your product offerings by allowing for the creation and management of two distinct product types: "Goods" and "Services".</p>
   <hr>
   <h3>Product Types</h3>
   <ul>
      <li><strong>Goods</strong>: These are simple products. When a customer makes a purchase, the payment is directly transferred to the product's owner.</li>
      <li><strong>Services</strong>: These products come with an added escrow feature. The payment for a service is held in escrow until the customer confirms the release, upon which the funds are transferred to the service provider.</li>
   </ul>
   <hr>
   <h2>Features</h2>
   <p>This plugin allows you to connect a Wordpress user to a Stripe Connect account. With this established, you can leverage the functions detailed below to facilitate a myriad of marketplace-related transactions.</p>
   <hr>
   <h2>Functionality</h2>
   <p>You'll find a documentation of the basic functions below. Code is pretty easy to read, though.</p>
   <hr>
   <h2>Future Enhancements</h2>
   <p>A ton, still work in progress. Feel free to contribute!.</p>
   <hr>
   <h2>Conclusion</h2>
   <p>By using this plugin, you open up a whole new world of possibilities for your online store, turning it into a versatile marketplace with escrow capabilities for service-based transactions. Happy selling!</p>
</div>

<div class="markdown prose w-full break-words dark:prose-invert light">
   <h1>Functions:</h1>
   <hr>
   <h3>create_transfer($data)</h3>
   <p>This function is used to create a new transfer. It requires a data associative array that contains information about the Stripe account, buyer, order, and the amount being transferred.</p>
   <p><strong>Parameters:</strong></p>
   <ul>
      <li>
         $data (array) - This is an associative array with the following key-value pairs:
         <ul>
            <li>'stripe_account' (string) - Stripe account the transfer is intended for.</li>
            <li>'buyer_id' (int) - User ID of the buyer.</li>
            <li>'order_id' (string) - Order ID.</li>
            <li>'amount' (float) - Transfer amount.</li>
         </ul>
      </li>
   </ul>
   <p><strong>Return Value:</strong> The function will return the post ID upon success. In case of an error, it will return an array with the key 'error'.</p>
   <hr>
   <h3>create_escrow($data)</h3>
   <p>This function is used to create an escrow. It also requires a data associative array with the same keys as create_transfer.</p>
   <p><strong>Parameters:</strong></p>
   <ul>
      <li>
         $data (array) - This is an associative array with the following key-value pairs:
         <ul>
            <li>'stripe_account' (string) - Stripe account the escrow is intended for.</li>
            <li>'buyer_id' (int) - User ID of the buyer.</li>
            <li>'order_id' (string) - Order ID.</li>
            <li>'amount' (float) - Escrow amount.</li>
         </ul>
      </li>
   </ul>
   <p><strong>Return Value:</strong> The function will return the post ID upon success. In case of an error, it will return an array with the key 'error'.</p>
   <hr>
   <h3>update_product_stripe_account($productID, $stripe_account)</h3>
   <p>This function is used to link a product to a Stripe account so the owner will receive the funds when the product is purchased.</p>
   <p><strong>Parameters:</strong></p>
   <ul>
      <li>$productID (string) - The Product ID which needs to be linked.</li>
      <li>$stripe_account (string) - The Stripe account to which the product will be linked.</li>
   </ul>
   <hr>
   <h3>mark_product_as_goods($productID)</h3>
   <p>This function marks a product as "Goods". When a product is marked as "Goods", the transfer of funds will be done directly without escrow.</p>
   <p><strong>Parameters:</strong></p>
   <ul>
      <li>$productID (string) - The Product ID which needs to be marked as "Goods".</li>
   </ul>
   <hr>
   <h3>mark_product_as_services($productID)</h3>
   <p>This function marks a product as "Service". When a product is marked as "Service", the funds will be kept in escrow until the client releases it.</p>
   <p><strong>Parameters:</strong></p>
   <ul>
      <li>$productID (string) - The Product ID which needs to be marked as "Service".</li>
   </ul>
   <hr>
   <h3 class="">escrow_release_request($data)</h3>
   <p>This function is used to make a request for an escrow release.</p>
   <p><strong>Parameters:</strong></p>
   <ul>
      <li>$data (array) - The array of data required for the request. The keys and values in this array will depend on the specifics of your escrow system.</li>
   </ul>
   <hr>
   <h3>escrow_release_approve($data)</h3>
   <p>This function is used to confirm an escrow release.</p>
   <p><strong>Parameters:</strong></p>
   <ul>
      <li>$data (array) - The array of data required for approval. The keys and values in this array will depend on the specifics of your escrow system.</li>
   </ul>
   <hr>
   <p>Please read the comments above each function declaration in the code for more detailed information, and ensure to follow the expected data structures for function parameters. Happy Coding!</p>
</div>
