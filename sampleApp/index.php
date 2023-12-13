<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __dir__ . '/../vendor/autoload.php';

use \Pinelabs\Php\API;

$callback_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") ? "https" : "http" . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . 'callback.php';


if(isset($_POST['pay_now'])){
    
    $merchantId = (isset($_POST['merchant_id']) && !empty($_POST['merchant_id'])) ? $_POST['merchant_id'] : '';
    $apiAccessCode = (isset($_POST['access_code']) && !empty($_POST['access_code'])) ? $_POST['access_code'] : '';
    $secret = (isset($_POST['secret']) && !empty($_POST['secret'])) ? $_POST['secret'] : '';
    $isTestMode = (isset($_POST['pg_mode']) && !empty($_POST['pg_mode'])) ? $_POST['pg_mode'] : false;
    
    $txn_id = (isset($_POST['txn_id']) && !empty($_POST['txn_id'])) ? $_POST['txn_id'] : '';
    $callback = (isset($_POST['callback_url']) && !empty($_POST['callback_url'])) ? $_POST['callback_url'] : '';
    $amount_in_paisa = (isset($_POST['amount_in_paisa']) && !empty($_POST['amount_in_paisa'])) ? $_POST['amount_in_paisa'] : ''; // amount in paisa
    
    // product_details
    $temp=$_POST['product_details'];
    if(strlen($temp) !== 0){
    $products_data = json_decode($temp, true);
    }
    $payment_modes = [
        'cards' => (isset($_POST['payment_mode']) && in_array('card', $_POST['payment_mode'])) ? true : false,
        'netbanking' => (isset($_POST['payment_mode']) && in_array('netbanking', $_POST['payment_mode'])) ? true : false,
        'wallet' => (isset($_POST['payment_mode']) && in_array('wallet', $_POST['payment_mode'])) ? true : false,
        'upi' => (isset($_POST['payment_mode']) && in_array('upi', $_POST['payment_mode'])) ? true : false,
        'emi' => (isset($_POST['payment_mode']) && in_array('emi', $_POST['payment_mode'])) ? true : false,
        'debit_emi' => (isset($_POST['payment_mode']) && in_array('debit_emi', $_POST['payment_mode'])) ? true : false,
        'cardless_emi' => (isset($_POST['payment_mode']) && in_array('cardless_emi', $_POST['payment_mode'])) ? true : false,
        'bnpl' => (isset($_POST['payment_mode']) && in_array('bnpl', $_POST['payment_mode'])) ? true : false,
        'prebooking' => (isset($_POST['payment_mode']) && in_array('prebooking', $_POST['payment_mode'])) ? true : false,
    ];
    
    $customer_data = [
        'customer_id' => (isset($_POST['customer_id']) && !empty($_POST['customer_id'])) ? $_POST['customer_id'] : '',
        'first_name' => (isset($_POST['first_name']) && !empty($_POST['first_name'])) ? $_POST['first_name'] : '',
        'last_name' => (isset($_POST['last_name']) && !empty($_POST['last_name'])) ? $_POST['last_name'] : '',
        'email_id' => (isset($_POST['email']) && !empty($_POST['email'])) ? $_POST['email'] : '',
        'mobile_no' => (isset($_POST['phone']) && !empty($_POST['phone'])) ? $_POST['phone'] : ''
    ];

    $billing_data = [
        'address1' => (isset($_POST['billing_address1']) && !empty($_POST['billing_address1'])) ? $_POST['billing_address1'] : '',
        'address2' => (isset($_POST['billing_address2']) && !empty($_POST['billing_address2'])) ? $_POST['billing_address2'] : '',
        'address3' => (isset($_POST['billing_address3']) && !empty($_POST['billing_address3'])) ? $_POST['billing_address3'] : '',
        'pincode' => (isset($_POST['billing_pincode']) && !empty($_POST['billing_pincode'])) ? $_POST['billing_pincode'] : '',
        'city' => (isset($_POST['billing_city']) && !empty($_POST['billing_city'])) ? $_POST['billing_city'] : '',
        'state' => (isset($_POST['billing_state']) && !empty($_POST['billing_state'])) ? $_POST['billing_state'] : '',
        'country' => (isset($_POST['billing_country']) && !empty($_POST['billing_country'])) ? $_POST['billing_country'] : '',
    ];

    $shipping_data = [
        'first_name' => (isset($_POST['shipping_first_name']) && !empty($_POST['shipping_first_name'])) ? $_POST['shipping_first_name'] : '',
        'last_name' => (isset($_POST['shipping_last_name']) && !empty($_POST['shipping_last_name'])) ? $_POST['shipping_last_name'] : '',
        'mobile_no' => (isset($_POST['shipping_phone']) && !empty($_POST['shipping_phone'])) ? $_POST['shipping_phone'] : '',
        'address1' => (isset($_POST['shipping_address1']) && !empty($_POST['shipping_address1'])) ? $_POST['shipping_address1'] : '',
        'address2' => (isset($_POST['shipping_address2']) && !empty($_POST['shipping_address2'])) ? $_POST['shipping_address2'] : '',
        'address3' => (isset($_POST['shipping_address3']) && !empty($_POST['shipping_address3'])) ? $_POST['shipping_address3'] : '',
        'pincode' => (isset($_POST['shipping_pincode']) && !empty($_POST['shipping_pincode'])) ? $_POST['shipping_pincode'] : '',
        'city' => (isset($_POST['shipping_city']) && !empty($_POST['shipping_city'])) ? $_POST['shipping_city'] : '',
        'state' => (isset($_POST['shipping_state']) && !empty($_POST['shipping_state'])) ? $_POST['shipping_state'] : '',
        'country' => (isset($_POST['shipping_country']) && !empty($_POST['shipping_country'])) ? $_POST['shipping_country'] : '',
    ];

    $udf_data = [
        'udf_field_1' => (isset($_POST['udf1']) && !empty($_POST['udf1'])) ? $_POST['udf1'] : '',
        'udf_field_2' => (isset($_POST['udf2']) && !empty($_POST['udf2'])) ? $_POST['udf2'] : '',
        'udf_field_3' => (isset($_POST['udf3']) && !empty($_POST['udf3'])) ? $_POST['udf3'] : '',
        'udf_field_4' => (isset($_POST['udf4']) && !empty($_POST['udf4'])) ? $_POST['udf4'] : '',
        'udf_field_5' => (isset($_POST['udf5']) && !empty($_POST['udf5'])) ? $_POST['udf5'] : '',
    ];
    
    if(!empty($merchantId) && !empty($apiAccessCode) && !empty($secret)){
        $api = new API($merchantId, $apiAccessCode, $secret, $isTestMode);
    }
    else{
        echo "Merchant details not found or incorrect."; die();
    }

    if(!empty($txn_id) && !empty($callback) && !empty($amount_in_paisa)){
        $txn_data = [
            'txn_id' => $txn_id,
            'callback' => $callback,
            'amount_in_paisa' => $amount_in_paisa, // amount in paisa
        ];
    }
    else{
        echo "Order details not found or incorrect."; die();
    }
    
    $response = $api->Payment()->Create($txn_data, $customer_data, $billing_data, $shipping_data, $udf_data, $payment_modes, $products_data);
    
    if (!$response) {
        echo 'An Error Occurred, Please try again.'; die();
    }

    $response = json_decode($response, true);
    
    if (json_last_error()) {
        echo 'An Error Occurred, Please try again.'; die();
    }

    if(isset($response['status']) && $response['status'] && isset($response['redirect_url'])){
        echo "<script>window.open('".$response['redirect_url']."','_blank');</script>";
    }
    else{
        print_r($response); die();
    }
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
        .dropdown-menu { padding: 15px;  max-height: 200px; overflow-x: hidden; } 
        .dropdown-menu a {  display: block; } 
        .dropdown-menu a:hover { background-color: #f1f1f1; }
    </style>
</head>

<body>
    <div class="container">
        <main>
            <div class="py-5 text-center">
                <h2>Test Form</h2>
                <div class="text-center"> 
                    <p class="mb-0">Check Fetch and EMI API Response</p>
                    <div class="col-md-12 text-center">
                        <a href="<?= $_SERVER['REQUEST_URI'] ?>Fetch.php">Fetch Order </a> | 
                        <a href="<?= $_SERVER['REQUEST_URI'] ?>Emi.php"> Fetch EMI </a> |
                        <a href="<?= $_SERVER['REQUEST_URI'] ?>Hash.php" >Hash Verification </a> 
                    </div>  
                </div>
            </div> 

            <div class="row">
                <div class="col-md-6 col-lg-12">
                    <form method="post" class="needs-validation" novalidate>
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

                            <div class="col-sm-6">
                                <label for="mode" class="form-label">Gateway Mode</label>
                                <select name="pg_mode" id="mode" class="form-control">
                                    <option value="true">Sandbox</option>
                                    <option value="false">Production</option>
                                </select>
                            </div>

                            <div class="col-sm-3">
                                <label for="txn_id" class="form-label">Transacrtion ID</label>
                                <input type="text" name="txn_id" class="form-control" id="txn_id" placeholder="Transacrtion ID" value="<?php echo uniqid() ?>" required>
                            </div>

                            <div class="col-sm-3">
                                <label for="amount" class="form-label">Amount (In Paisa)</label>
                                <input type="text" name="amount_in_paisa" class="form-control" id="amount" placeholder="Amount (In Paisa)" value="4000000" required>
                            </div>

                            <div class="col-sm-6">
                                <label for="callback_url" class="form-label">Callback URL</label>
                                <input type="text" name="callback_url" class="form-control" id="callback_url" placeholder="Callback URL" value="<?php echo $callback_url ?>" required>
                            </div>

                            <div class="col-sm-9">
                                    <label for="response_data" class="form-label">Product Details</label>
                                    <textarea name="product_details" id="product_details" class="form-control" rows="6" >[{"product_code":"testSKU1","product_amount":"2000000"},{"product_code":"testSKU2","product_amount":"2000000"}]</textarea>
                                </div>

                            <div class="col-sm-6">
                                <label for="callback_url" class="form-label">Payment Mode</label>
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        Select Payment Mode
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <li><input type="checkbox" name="payment_mode[]" id="card" value="card" checked><label for="card" style="margin-left: 5px;">Credit Debit Card</label></li>
                                        <li><input type="checkbox" name="payment_mode[]" id="netbanking" value="netbanking" checked><label for="netbanking" style="margin-left: 5px;">Net Nanking</label></li>
                                        <li><input type="checkbox" name="payment_mode[]" id="wallet" value="wallet" checked><label for="wallet" style="margin-left: 5px;">Wallet</label></li>
                                        <li><input type="checkbox" name="payment_mode[]" id="upi" value="upi" checked><label for="upi" style="margin-left: 5px;">UPI</label></li>
                                        <li><input type="checkbox" name="payment_mode[]" id="emi" value="emi"><label for="emi" style="margin-left: 5px;">EMI</label></li>
                                        <li><input type="checkbox" name="payment_mode[]" id="debit_emi" value="debit_emi"><label for="debit_emi" style="margin-left: 5px;">Debit Card EMI</label></li>
                                        <li><input type="checkbox" name="payment_mode[]" id="cardless_emi" value="cardless_emi"><label for="cardless_emi" style="margin-left: 5px;">Cardless EMI</label></li>
                                        <li><input type="checkbox" name="payment_mode[]" id="bnpl" value="bnpl"><label for="bnpl" style="margin-left: 5px;">BNPL</label></li>
                                        <li><input type="checkbox" name="payment_mode[]" id="prebooking" value="prebooking"><label for="prebooking" style="margin-left: 5px;">Pay Later</label></li>
                                    </ul>
                                </div>
                            </div>
                           
                            <a class="text-dark text-decoration-none mt-4 mb-4 d-flex" href="#customer_info" data-bs-toggle="collapse" role="button">
                                <i class="fa fa-chevron-down me-2 text-primary"></i>
                                <h5 class="mb-0">Customer Details</h5>
                            </a>

                            <div id="customer_info" class="collapse row">

                                <div class="col-sm-4">
                                    <label for="customer_id" class="form-label">Customer Id</label>
                                    <input type="text" name="customer_id" class="form-control" id="customer_id" placeholder="Customer Id" value="">
                                </div>

                                <div class="col-sm-4">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" name="first_name" class="form-control" id="first_name" placeholder="Enter First Name" value="">
                                </div>

                                <div class="col-sm-4">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" id="last_name" placeholder="Enter Last Name" value="">
                                </div>

                                <div class="col-sm-6 mt-2">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="text" name="email" class="form-control" id="Enter email" placeholder="Enter Email Id" value="">
                                </div>

                                <div class="col-sm-6 mt-2">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control" id="phone" placeholder="Enter Phone No" value="">
                                </div>

                            </div>

                            <a class="text-dark text-decoration-none mt-2 mb-4 d-flex" href="#billing_address" data-bs-toggle="collapse" role="button">
                                <i class="fa fa-chevron-down me-2 text-primary"></i>
                                <h5 class="mb-0">Billing Address</h5>
                            </a>

                            <div id="billing_address" class="collapse row">

                                <div class="col-sm-4">
                                    <label for="address1" class="form-label">Address 1</label>
                                    <input type="text" name="address1" class="form-control" id="billing_address1" placeholder="Address 1" value="">
                                </div>

                                <div class="col-sm-4">
                                    <label for="address2" class="form-label">Address 2</label>
                                    <input type="text" name="address2" class="form-control" id="billing_address2" placeholder="Address 2" value="">
                                </div>

                                <div class="col-sm-4">
                                    <label for="address3" class="form-label">Address 3</label>
                                    <input type="text" name="address3" class="form-control" id="billing_address3" placeholder="Address 3" value="">
                                </div>

                                <div class="col-sm-3 mt-2">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" name="city" class="form-control" id="billing_city" placeholder="City" value="">
                                </div>

                                <div class="col-sm-3 mt-2">
                                    <label for="state" class="form-label">State</label>
                                    <input type="text" name="state" class="form-control" id="billing_state" placeholder="State" value="">
                                </div>

                                <div class="col-sm-3 mt-2">
                                    <label for="country" class="form-label">Country</label>
                                    <input type="text" name="country" class="form-control" id="billing_country" placeholder="Country" value="">
                                </div>

                                <div class="col-sm-3 mt-2">
                                    <label for="pincode" class="form-label">Pin Code</label>
                                    <input type="text" name="billing_pincode" class="form-control" id="pincode" placeholder="Pin Code" value="">
                                </div>

                            </div>

                            <a class="text-dark text-decoration-none mt-2 mb-4 d-flex" href="#shipping_address" data-bs-toggle="collapse" role="button">
                                <i class="fa fa-chevron-down me-2 text-primary"></i>
                                <h5 class="mb-0">Shipping Address</h5>
                            </a>

                            <div id="shipping_address" class="collapse row">

                                <div class="mb-3 col-md-4">
                                    <label for="shipping_firstname">First Name</label>
                                    <input type="text" class="form-control" placeholder="Enter First Name" name="shipping_firstname" id="shipping_firstname">
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label for="shipping_lastname">Last Name</label>
                                    <input type="text" class="form-control" placeholder="Enter Last Name" name="shipping_lastname" id="shipping_lastname">
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label for="shipping_phone">Phone No</label>
                                    <input type="text" class="form-control" placeholder="Enter Phone No" name="shipping_phone" id="shipping_phone">
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label for="shipping_address1">Address Line 1</label>
                                    <input type="text" class="form-control" placeholder="Enter Address Line 1" name="shipping_address1" id="shipping_address1">
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label for="shipping_address2">Address Line 2</label>
                                    <input type="text" class="form-control" placeholder="Enter Address Line 2" name="shipping_address2" id="shipping_address2">
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label for="shipping_address3">Address Line 3</label>
                                    <input type="text" class="form-control" placeholder="Enter Address Line 3" name="shipping_address3" id="shipping_address3">
                                </div>

                                <div class="mb-3 col-md-3 mt-2">
                                    <label for="shipping_city">City</label>
                                    <input type="text" class="form-control" placeholder="Enter City" name="shipping_city" id="shipping_city">
                                </div>

                                <div class="mb-3 col-md-3 mt-2">
                                    <label for="shipping_state">State</label>
                                    <input type="text" class="form-control" placeholder="Enter State" name="shipping_state" id="shipping_state">
                                </div>

                                <div class="mb-3 col-md-3 mt-2">
                                    <label for="shipping_pincode">Pin Code</label>
                                    <input type="text" class="form-control" placeholder="Enter Pin Code" name="shipping_pincode" id="shipping_pincode">
                                </div>

                                <div class="mb-3 col-md-3 mt-2">
                                    <label for="shipping_country">Country</label>
                                    <input type="text" class="form-control" placeholder="Enter Country" name="shipping_country" id="shipping_country">
                                </div>

                            </div>

                            <a class="text-dark text-decoration-none mt-2 mb-4 d-flex" href="#additional_fields" data-bs-toggle="collapse" role="button">
                                <i class="fa fa-chevron-down me-2 text-primary"></i>
                                <h5 class="mb-0">Additional Fields </h5>
                            </a>

                            <div id="additional_fields" class="collapse row">

                                <div class="mb-3 col-md-6">
                                    <label for="udf1">udf 1</label>
                                    <input type="text" class="form-control" id="udf1" placeholder="Enter udf 1" name="udf1">
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="udf2">udf 2</label>
                                    <input type="text" class="form-control" id="udf2" placeholder="Enter udf 2" name="udf2">
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="udf3">udf 3</label>
                                    <input type="text" class="form-control" id="udf3" placeholder="Enter udf 3" name="udf3">
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="udf4">udf 4</label>
                                    <input type="text" class="form-control" id="udf4" placeholder="Enter udf 4" name="udf4">
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="udf4">udf 5</label>
                                    <input type="text" class="form-control" id="udf5" placeholder="Enter udf 5" name="udf5">
                                </div>

                            </div>


                        </div>

                        <button class="w-100 my-4 btn btn-primary btn-lg" type="submit" name="pay_now">Pay Now</button>
                    </form>
                </div>
            </div>
        </main>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    
</body>

</html>