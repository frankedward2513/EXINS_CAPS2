<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/EXINS/config/gemini_config.php';
function askGemini($prompt)
{
    global $GEMINI_API_KEY;
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent?key="
        . urlencode($GEMINI_API_KEY);
    $data = [
        "contents" => [
            [
                "parts" => [
                    [
                        "text" => $prompt
                    ]
                ]
            ]
        ]
    ];

    $ch = curl_init($url);

    if ($ch === false) {
        return [
            "success" => false,
            "error" => "Unable to initialize cURL."
        ];
    }

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json"
        ],
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_TIMEOUT => 60
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);

        return [
            "success" => false,
            "error" => "Gemini connection failed: " . $error
        ];
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    $result = json_decode($response, true);

    if ($httpCode < 200 || $httpCode >= 300) {
        return [
            "success" => false,
            "error" => $result["error"]["message"]
                ?? "Gemini API returned HTTP status " . $httpCode
        ];
    }

    $text = $result["candidates"][0]["content"]["parts"][0]["text"]
        ?? null;

    if (!$text) {
        return [
            "success" => false,
            "error" => "Gemini returned an empty response."
        ];
    }

    return [
        "success" => true,
        "analysis" => $text
    ];
}