<?php 

namespace Pinelabs\Php\Utils;

class Request
{
    public static function POST($uri, $body, $headers, $is_form=false)
    {
        if($is_form){
            $body = http_build_query($body, "", "&");
        }
        else{
            $body = json_encode($body);
        }

        $apiHeader = [];
        foreach ($headers as $key => $value) {
            $apiHeader = array_merge($apiHeader, [$key . ': ' . $value]);
        }
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid UTF-8");
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $uri,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => $apiHeader,
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        
        return $response;
    }
}

?>