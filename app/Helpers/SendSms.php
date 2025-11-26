<?php

use Illuminate\Support\Facades\Http;

function sms_send($number, $message) {
    $url = setting('sms_api_url');
    $api_key = setting('sms_api_key');
    $senderid = setting('sms_sender_id');

    $data = [
        "api_key" => $api_key,
        "senderid" => $senderid,
        "number" => $number,
        "message" => $message
    ];

    // POST request পাঠানো HTTP Client দিয়ে
    $response = Http::asForm()->post($url, $data);

    // Response return
    return $response->body(); // অথবা $response->json() যদি JSON response হয়
}
