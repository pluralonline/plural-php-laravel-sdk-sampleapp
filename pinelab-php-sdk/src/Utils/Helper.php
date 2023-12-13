<?php

namespace Pinelabs\Php\Utils;

class Helper
{
    public static function base64String(array $arr)
    {
        $string = [];
        foreach ($arr as $key => $value) {
            if ($value != null && $value != '') {
                $string[$key] = $value;
            }
        }

        return base64_encode(json_encode($string));
    }

    public static function formUrlEncodeString(array $arr)
    {
        ksort($arr);

        $dataString = "";

        foreach ($arr as $key => $value)
        {
            $dataString .= $key . "=" . $value . "&";
        }

        return substr($dataString, 0, -1);
    }

    public static function _parsePaymentMode($paymentMethods)
    {
        $methodMapping = self::paymentMode();
        $matchedCodes = [];

        foreach ($paymentMethods as $method => $value) {
            foreach ($methodMapping as $mapping) {
                if ($mapping['method'] === $method && $value) {
                    $matchedCodes[] = $mapping['code'];
                    break;
                }
            }
        }

        return implode(',', $matchedCodes);
    }

    private static function paymentMode(){
        $mode =[
            [
                'method' => 'cards',
                'code' => 1
            ],
            [
                'method' => 'netbanking',
                'code' => 3
            ],
            [
                'method' => 'emi',
                'code' => 4
            ],
            [
                'method' => 'upi',
                'code' => 10
            ],
            [
                'method' => 'wallet',
                'code' => 11
            ],
            
            [
                'method' => 'debit_emi',
                'code' => 14
            ],
            [
                'method' => 'prebooking',
                'code' => 16
            ],
            [
                'method' => 'bnpl',
                'code' => 17
            ],
            [
                'method' => 'cardless_emi',
                'code' => 19
            ],
        ];

        return $mode;
    }
}
?>