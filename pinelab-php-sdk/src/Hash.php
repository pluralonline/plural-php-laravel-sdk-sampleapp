<?php

namespace Pinelabs\Php;

class Hash extends API
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

    public function Create(string $request)
    {
        $hash = strtoupper(
            hash_hmac(
                "sha256",
                $request,
                self::hex2Str($this->secret)
            )
        );

        return $hash;
    }

    public function Verify(string $receiveHash, array $request){
        ksort($request);
        
        $dataString = "";

        foreach ($request as $key => $value)
        {
            $dataString .= $key . "=" . $value . "&";
        }

        $dataString = substr($dataString, 0, -1);

        $createHash = strtoupper(hash_hmac("sha256", $dataString, self::hex2Str($this->secret)));

        if($receiveHash === $createHash){
            return true;
        }
        return false;
    }

    private function hex2Str(string $hex)
    {
        $string='';
        for ($i=0; $i < strlen($hex)-1; $i+=2) {
            $string .= chr(hexdec($hex[$i].$hex[$i+1]));
        }
        return $string;
    }

}
