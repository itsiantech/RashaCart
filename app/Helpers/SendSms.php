<?php

use Illuminate\Support\Facades\Http;

function sms_send($number, $message)
{
    $url = setting('sms_api_url');
    $api_key = setting('sms_api_key');
    $senderid = setting('sms_sender_id');

    if (!$url || !$api_key || !$senderid) {
        return json_encode([
            'response_code' => 0,
            'error_message' => 'SMS configuration missing.'
        ]);
    }

    $data = [
        "api_key" => $api_key,
        "senderid" => $senderid,
        "number" => $number,
        "message" => $message
    ];

    $response = Http::asForm()->post($url, $data);

    return $response->body();
}
