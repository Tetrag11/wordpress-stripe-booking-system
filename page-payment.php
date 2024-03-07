<?php

/**
 * Template Name: Payment Page
 */
get_header();
?>


<!-- Display a payment form -->
<form id="payment-form" action="<?php echo admin_url('admin-ajax.php');  ?>" method="post">
    <div id="link-authentication-element">
        <!--Stripe.js injects the Link Authentication Element-->
    </div>
    <div id="payment-element">
        <!--Stripe.js injects the Payment Element-->
    </div>
    <button id="submit">
        <div class="spinner hidden" id="spinner"></div>
        <span id="button-text">Pay now</span>
    </button>
    <div id="payment-message" class="hidden"></div>
</form>



<?php
get_footer();
?>