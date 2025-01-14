<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashfree Checkout</title>
    <script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>
</head>

<body>
    <?php
    // Retrieve the payment session ID from the query string
    if (!isset($_GET['paymentSessionId'])) {
        die("Payment session ID not provided.");
    }
    $paymentSessionId = htmlspecialchars($_GET['paymentSessionId']);
    ?>
    <div class="row">
        <p>Click below to open the checkout page in the current tab</p>
        <button id="renderBtn">Pay Now</button>
    </div>
    <div id="cf_checkout"></div>
    <script>
        // same or diff tab checkout
        const cashfree = Cashfree({
            mode: 'sandbox'
        });

        window.addEventListener('DOMContentLoaded', () => {
            let checkoutOptions = {
                paymentSessionId: '<?php echo $paymentSessionId; ?>',
                redirectTarget: '_self', // _self, _blank, _top
            };
            cashfree.checkout(checkoutOptions);
        });
    </script>
</body>

</html>


<!-- 
    // Pop-up checkout
    
    <script>
        const cashfree = Cashfree({
            mode: 'sandbox'
        });
        let checkoutOptions = {
            paymentSessionId: '<?php echo $paymentSessionId; ?>',
            redirectTarget: "_modal"
        };

        cashfree.checkout(checkoutOptions).then((result) => {
            if (result.error) {
                // This will be true whenever user clicks on close icon inside the modal or any error happens during the payment
                alert("User has closed the popup or there is some payment error, Check for Payment Status");
                console.log(result.error);
            }
            if (result.redirect) {
                // This will be true when the payment redirection page couldnt be opened in the same window
                // This is an exceptional case only when the page is opened inside an inAppBrowser
                // In this case the customer will be redirected to return url once payment is completed
                alert("Payment will be redirected");
            }
            if (result.paymentDetails) {
                // This will be called whenever the payment is completed irrespective of transaction status
                alert("Payment has been completed, Check for Payment Status");
                console.log(result.paymentDetails.paymentMessage);
            }
        });
    </script> -->

<!-- 

// DOM Checkout
<script>
        const cashfree = Cashfree({
            mode: 'sandbox'
        });
        let checkoutOptions = {
            paymentSessionId: '<?php echo $paymentSessionId; ?>',
            redirectTarget: document.getElementById("cf_checkout"),
            appearance: {
                width: "425px",
                height: "700px",
            },
        };
        cashfree.checkout(checkoutOptions).then((result) => {
            if (result.error) {
                // This will be true when there is any error during the payment
                console.log("There is some payment error, Check for Payment Status");
                console.log(result.error);
            }
            if (result.redirect) {
                // This will be true when the payment redirection page couldnt be opened in the same window
                // This is an exceptional case only when the page is opened inside an inAppBrowser
                // In this case the customer will be redirected to return url once payment is completed
                console.log("Payment will be redirected");
            }
            if (result.paymentDetails) {
                // This will be called whenever the payment is completed irrespective of transaction status
                console.log("Payment has been completed, Check for Payment Status");
                console.log(result.paymentDetails.paymentMessage);
            }
        });
    </script>

-->