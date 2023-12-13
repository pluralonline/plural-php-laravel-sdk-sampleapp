<?php

namespace Pinelabs\Php;

use Pinelabs\Php\Utils\Request;

class EMI extends API
{
    private $merchantId;

    private $apiAccessCode;

    private $secret;

    private $isTestMode;

    public function __construct($merchantId, $apiAccessCode, $secret, $isTestMode = false)
    {
        parent::__construct($merchantId, $apiAccessCode, $secret, $isTestMode);

        $this->merchantId = $merchantId;
        $this->apiAccessCode = $apiAccessCode;
        $this->secret = $secret;
        $this->isTestMode = $isTestMode; 
    }

    public function Calculator($txn_data, $productsDetails)
    {
        try {
            $requestBody = [
                'merchant_data' => [
                    'merchant_id' => $this->merchantId,
                    'merchant_access_code' => $this->apiAccessCode,
                ],
                'payment_data' => [
                    'amount_in_paisa' => $txn_data['amount_in_paisa'],
                ],
                'product_details' => $productsDetails
            ];

            $headers = array(
                'Content-Type' => 'application/json',
            );
            
            $response = Request::POST($this->getApiUrl().'v2/emi/calculator', $requestBody, $headers);
            $response = json_decode($response, true);

            if ((isset($response['response_code']) && $response['response_code'] != 1) || (isset($response['respone_code']) && $response['respone_code'] != 1)) {
                return json_encode([
                    'status' => false,
                    'message' => $response['response_message'] ?? 'Something went wrong',
                    'respone_code' => $response['respone_code'] ?? '',
                ], JSON_UNESCAPED_SLASHES);
            }
    
            return json_encode($response);

        } catch (\Exception $e) {
            throw new \Exception ($e->getMessage());
        }
    }
}
