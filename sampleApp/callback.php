<?php 

require_once __dir__.'/../vendor/autoload.php';

use \Pinelabs\Php\API;

$merchantId = "106600";
$apiAccessCode = "bcf441be-411b-46a1-aa88-c6e852a7d68c";
$secret = "9A7282D0556544C59AFE8EC92F5C85F6";
$isTestMode = true;

$api = new API($merchantId, $apiAccessCode, $secret, $isTestMode);

echo '<pre>' . json_encode($_POST, JSON_PRETTY_PRINT) . '</pre>';

$receviedHash = $_POST['dia_secret'] ?? '';

if(isset($_POST['dia_secret'])){
    unset($_POST['dia_secret']);
}

if(isset($_POST['dia_secret_type'])){
    unset($_POST['dia_secret_type']);
}


echo $api->Hash()->Verify($receviedHash, $requestData = $_POST);

?>