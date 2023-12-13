<?php

namespace Pinelabs\Php;

use Pinelabs\Php\Hash;
use Pinelabs\Php\Utils\Request;
use Pinelabs\Php\Utils\Helper;

class Payment extends API
{
    private $merchantId;

    private $apiAccessCode;

    private $secret;

    private $isTestMode;

    private $hash;

    public function __construct($merchantId, $apiAccessCode, $secret, $isTestMode = false)
    {
        parent::__construct($merchantId, $apiAccessCode, $secret, $isTestMode);

        $this->merchantId = $merchantId;
        $this->apiAccessCode = $apiAccessCode;
        $this->secret = $secret;
        $this->isTestMode = $isTestMode; 

        $this->hash = new Hash($merchantId, $apiAccessCode, $secret, $isTestMode);
    }

    /**
     * @method Create
     *
     * @param string, url, int ( in paisa eg 1rs = 100 paisa )
     **/
    public function Create($txn_data, $customerData = [], $billing_data = [], $shipping_data = [], $udfData = [], $paymenModes = [], $productsDetails = [])
    {        
        try {
            $payment_mode = (string) Helper::_parsePaymentMode($paymenModes);

            $requestBody = [
                'merchant_data' => [
                    'merchant_id' => $this->merchantId,
                    'merchant_access_code' => $this->apiAccessCode,
                    'merchant_return_url' => $txn_data['callback'],
                    'unique_merchant_txn_id' => $txn_data['txn_id'],
                ],
                'customer_data' => [
                    'customer_id' => $customerData['customer_id'] ?? '',
                    'first_name' => $customerData['first_name'] ?? '',
                    'last_name' => $customerData['last_name'] ?? '',
                    'email_id' => $customerData['email_id'] ?? '',
                    'mobile_no' => $customerData['mobile_no'] ?? '',
                    'billing_data' => $billing_data,
                    'shipping_data' => $shipping_data,
                ],
                'payment_data' => [
                    'amount_in_paisa' => $txn_data['amount_in_paisa'],
                ],
                'txn_data' => [
                    'navigation_mode' => 2,
                    'payment_mode' => $payment_mode,
                    'transaction_type' => 1,
                ],
                'product_details' => $productsDetails,
                'udf_data' => $udfData
            ];
    
            $encodedRequestBody = Helper::base64String($requestBody);
            
            $requestBody = [
                "request" => $encodedRequestBody
            ];
    
            $headers = array(
                'Content-Type' => 'application/json',
                'X-VERIFY' => $this->hash->Create($encodedRequestBody),
            );
            
            $response = Request::POST($this->getApiUrl().'v2/accept/payment', $requestBody, $headers);
            $response = json_decode($response, true);
    
            if ((isset($response['response_code']) && $response['response_code'] != 1) || (isset($response['respone_code']) && $response['respone_code'] != 1)) {
                return json_encode([
                    'status' => false,
                    'message' => $response['response_message'] ?? 'Something went wrong',
                    'respone_code' => $response['respone_code'] ?? '',
                ], JSON_UNESCAPED_SLASHES);
            }
    
            return json_encode([
                'status' => true,
                'token' => $response['token'],
                'redirect_url' => $response['redirect_url'],
            ], JSON_UNESCAPED_SLASHES);
        } 
        catch (\Exception $e) {
            throw new \Exception ($e->getMessage());
        }
    }

    public function Fetch($txnID = null, $txnType = 3)
    {
        try {
            $requestBody = [
                'ppc_MerchantID' => $this->merchantId,
                'ppc_MerchantAccessCode' => $this->apiAccessCode,
                'ppc_TransactionType' => $txnType,
                'ppc_UniqueMerchantTxnID' => $txnID,
            ];

            $encodedRequestBody = Helper::formUrlEncodeString($requestBody);

            $requestBodyWithHash = array_merge($requestBody, [
                'ppc_DIA_SECRET' => $this->hash->Create($encodedRequestBody),
                "ppc_DIA_SECRET_TYPE" => "sha256",
            ]);
    
            $headers = [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ];
 
            $response = Request::POST($this->getApiUrl().'PG/V2', $requestBodyWithHash, $headers, true);
            $response = json_decode($response, true);

            if(!isset($response['ppc_ParentTxnResponseCode'])){
                return json_encode([
                    'status' => false,
                    'message' => $response['ppc_TxnResponseMessage'] ?? 'Something went wrong',
                    'respone_code' => $response['ppc_TxnResponseCode'] ?? '',
                ], JSON_UNESCAPED_SLASHES);
            }
                    
            if ((isset($response['response_code']) && $response['response_code'] != 1) || (isset($response['respone_code']) && $response['respone_code'] != 1)) {
                return json_encode([
                    'status' => false,
                    'message' => $response['response_message'] ?? 'Something went wrong',
                    'respone_code' => $response['respone_code'] ?? '',
                ], JSON_UNESCAPED_SLASHES);
            }
    
            return json_encode($response);
        } 
        catch (\Exception $e) {
            throw new \Exception ($e->getMessage());
        }
    }

}
