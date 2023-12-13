<?php

namespace Pinelabs\Php;

class API
{
    private $merchantId;

    private $apiAccessCode;

    private $secret;

    private $isTestMode;

    private $ProdDomain = 'https://pinepg.in/';

    private $TestDomain = 'https://uat.pinepg.in/';

    public function __construct($merchantId, $apiAccessCode, $secret, $isTestMode = false)
    {
        $this->merchantId = $merchantId;
        $this->apiAccessCode = $apiAccessCode;
        $this->secret = $secret;
        $this->isTestMode = $isTestMode;
    }

    public function Payment()
    {
        return new Payment($this->merchantId, $this->apiAccessCode, $this->secret, $this->isTestMode);
    }

    public function EMI()
    {
        return new EMI($this->merchantId, $this->apiAccessCode, $this->secret, $this->isTestMode);
    }

    public function Hash()
    {
        return new Hash($this->merchantId, $this->apiAccessCode, $this->secret, $this->isTestMode);
    }

    public function getApiUrl()
    {
        $url = $this->isTestMode ? $this->TestDomain : $this->ProdDomain;
        return $url .= 'api/';
    }
}
