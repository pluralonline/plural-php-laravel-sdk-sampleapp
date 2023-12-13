<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __dir__ . '/../vendor/autoload.php';

use Pinelabs\Php\API;

if (!empty($_POST['access_code']) && !empty($_POST['merchant_id']) && !empty($_POST['secret'])) {

    $merchantId = (isset($_POST['merchant_id']) && !empty($_POST['merchant_id'])) ? $_POST['merchant_id'] : '';
    $apiAccessCode = (isset($_POST['access_code']) && !empty($_POST['access_code'])) ? $_POST['access_code'] : '';
    $secret = (isset($_POST['secret']) && !empty($_POST['secret'])) ? $_POST['secret'] : '';
    $isTestMode = (isset($_POST['pg_mode']) && !empty($_POST['pg_mode'])) ? $_POST['pg_mode'] : false;

    $txn_data['amount_in_paisa'] = (isset($_POST['amount_in_paisa']) && !empty($_POST['amount_in_paisa'])) ? $_POST['amount_in_paisa'] : ''; // amount in paisa

    $temp=$_POST['product_details'];
    if(strlen($temp) !== 0){
    $products_data = json_decode($temp, true);
    }

    $api = new API($merchantId, $apiAccessCode, $secret, $isTestMode);

    $response = $api->EMI()->Calculator($txn_data, $products_data);

    if (!$response) {
        echo 'An Error Occurred, Please try again.';
        die();
    }

    $response = json_decode($response, true);

    if (json_last_error()) {
        echo 'An Error Occurred, Please try again.';
        die();
    }

    echo '<pre>' . json_encode($response, JSON_PRETTY_PRINT) . '</pre>';
    die;
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Pinelabs PHP/LARAVEL TEST</title>

    <style>
        .dropdown-menu {
            padding: 15px;
            max-height: 200px;
            overflow-x: hidden;
        }

        .dropdown-menu a {
            display: block;
        }

        .dropdown-menu a:hover {
            background-color: #f1f1f1;
        }

        .response {
            border: 1px solid #3498db; /* Border color: #3498db (a shade of blue) */
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <main>
            <div class="py-5 text-center">
                <h2>EMI Form</h2>
                <div class="text-center"> 
                    <div class="col-md-12 text-center">
                        <a href="./" > Home </a> | 
                        <a href="./Fetch.php" >Fetch Order </a> | 
                        <a href="./Hash.php" >Hash Verification </a> 
                    </div>  
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-lg-12">
                    <form method="post" id="EmiForm" class="needs-validation" novalidate>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label for="mid" class="form-label">Merchant ID</label>
                                <input type="text" name="merchant_id" class="form-control" id="mid" placeholder="Merchant ID" value="106598" required>
                            </div>

                            <div class="col-sm-6">
                                <label for="access_code" class="form-label">Access Code</label>
                                <input type="text" name="access_code" class="form-control" id="access_code" placeholder="API Access Code" value="4a39a6d4-46b7-474d-929d-21bf0e9ed607" required>
                            </div>

                            <div class="col-sm-6">
                                <label for="secret" class="form-label">Secret</label>
                                <input type="text" name="secret" class="form-control" id="secret" placeholder="Secret" value="55E0F73224EC458A8EC0B68F7B47ACAE" required>
                            </div>

                            <div class="col-sm-3">
                                <label for="mode" class="form-label">Gateway Mode</label>
                                <select name="pg_mode" id="mode" class="form-control">
                                    <option value="true">Sandbox</option>
                                    <option value="false">Production</option>
                                </select>
                            </div>

                            <div class="col-sm-3">
                                <label for="amount" class="form-label">Amount (In Paisa)</label>
                                <input type="text" name="amount_in_paisa" class="form-control" id="amount" placeholder="Amount (In Paisa)" value="4000000" required>
                            </div>

                            <div class="col-sm-12">
                                    <label for="response_data" class="form-label">Product Details</label>
                                    <textarea name="product_details" id="product_details" class="form-control" rows="6" >[{"product_code":"testSKU1","product_amount":"2000000"},{"product_code":"testSKU2","product_amount":"2000000"}]</textarea>
                            </div>

                        </div>
                </div>

                <button class="w-100 my-4 btn btn-primary btn-lg" type="button" id="fetchEmiOptions">Fetch EMI Options</button>
                </form>
            </div>

            <div id="response" class=""></div>
    </div>

    </main>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

</body>

<script>
    $(document).ready(function() {
       
        $("#fetchEmiOptions").click(function() {

            const currentUrl = new URL(window.location.href);
            var formData = $("#EmiForm").serialize();
            var response = document.getElementById("response");

            $.ajax({
                url: currentUrl, // Specify the URL of your PHP script
                method: "POST",
                data: formData, // Send serialized form data
                success: function(data) {
                    if (Object.keys(data).length > 0) {
                        response.classList.add("response");
                    }
                    $("#response").html(data);
                },
                error: function() {
                    alert("Error in AJAX request");
                }
            });
        });
    });
</script>

</html>