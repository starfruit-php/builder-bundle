<?php

namespace Starfruit\BuilderBundle\Tool;

class ApiTool
{
    public static function call($method, $url, $headers = [], $body = null)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
        ));

        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $isRedirect = curl_getinfo($curl)['url'] !== $url;

        curl_close($curl);

        return compact('status', 'response', 'isRedirect');
    }
}
