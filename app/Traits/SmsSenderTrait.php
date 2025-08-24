<?php

namespace App\Traits;

trait SmsSenderTrait
{
    public function sendSms($messageText, $recipientNumbers, $sender = "RCT.SA")
    {
        $apiKey = "JeIvJqFvRqKmlpJkRA9lKVQIOTM87IlTYWheRqRp";
        $apiSecret = "7JbrFBU9mXntcvN7tq7zfc6x4SNFzm7iD0oElYn8qVpWOcE7o4TvpAvir3dGPn2iPcKgObJO3ZaAqiz6tmJK4DAxHdipHEp63K9Z";
        $appHash = base64_encode("$apiKey:$apiSecret");

        $messages = [
            "messages" => [
                [
                    "text" => $messageText,
                    "numbers" => (array) $recipientNumbers,  
                    "sender" => $sender
                ]
            ]
        ];

        $url = "https://api-sms.4jawaly.com/api/v1/account/area/sms/send";
        $headers = [
            "Accept: application/json",
            "Content-Type: application/json",
            "Authorization: Basic $appHash"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($messages));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $responseJson = json_decode($response, true);

        return $this->handleSmsResponse($httpCode, $responseJson);
    }

    private function handleSmsResponse($httpCode, $responseJson)
    {
        if ($httpCode == 200) {
            if (isset($responseJson["messages"][0]["err_text"])) {
                return $responseJson["messages"][0]["err_text"];
            } else {
                return "تم الإرسال بنجاح. Job ID: " . $responseJson["job_id"];
            }
        } elseif ($httpCode == 400) {
            return $responseJson["message"];
        } elseif ($httpCode == 422) {
            return "نص الرسالة فارغ";
        } else {
            return "محظور بواسطة كلاودفلير. Status code: $httpCode";
        }
    }
}
