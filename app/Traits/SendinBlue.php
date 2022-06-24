<?php
namespace App\Traits;

trait sendinblue
{
    function sendEmail($url,$obj=NULL){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            // CURLOPT_URL => 'https://api.sendinblue.com/v3/smtp/email',
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POSTFIELDS =>json_encode($obj),
            CURLOPT_HTTPHEADER => array(
                'api-key: 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',
                'Content-Type: application/json',
                'Accept: application/json'
            ),
        ));


        $response = curl_exec($curl);
        curl_close($curl);
        // echo $response;

    }

}