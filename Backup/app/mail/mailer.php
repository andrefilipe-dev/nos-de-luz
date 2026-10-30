<?php

function send_email($to, $subject, $body, $reply_to = null)
{
    $api_key = getenv('RESEND_API_KEY');

    $data = [
        'from' => 'onboarding@resend.dev',
        'to' => [$to],
        'subject' => $subject,
        'html' => $body
    ];

    if ($reply_to !== null) {
        $data['reply_to'] = $reply_to;
    }

    $ch = curl_init('https://api.resend.com/emails');

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_TIMEOUT => 15
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($response === false || $http_code < 200 || $http_code >= 300) {
        return false;
    }

    return true;
}

?>