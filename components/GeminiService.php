<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\helpers\Json;

/**
 * GeminiService handles interactions with the Google Gemini AI API.
 */
class GeminiService extends Component
{
    public $apiKey;
    public $model = 'gemini-3.1-flash-lite';
    public $timeout = 15;

    /**
     * Sends a query to Gemini API and returns the response.
     * 
     * @param string $message User message
     * @param string $context System context/instructions
     * @return array ['success' => bool, 'response' => string, 'error' => string]
     */
    public function query($message, $context = "")
    {
        if (empty($this->apiKey) || $this->apiKey === 'YOUR_GEMINI_API_KEY') {
            return [
                'success' => false,
                'response' => "O chatbot não está configurado. Por favor, adicione a chave da API do Gemini.",
                'error' => 'API key not configured'
            ];
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key=" . $this->apiKey;

        $data = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $message]
                    ]
                ]
            ],
            "generationConfig" => [
                "maxOutputTokens" => 150,
                "temperature" => 0.5,
            ]
        ];

        if (!empty($context)) {
            $data["systemInstruction"] = [
                "parts" => [
                    ["text" => $context]
                ]
            ];
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, Json::encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        
        // Disable SSL verify for local dev to avoid certificate issues
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            Yii::error("Gemini API Error $httpCode: " . ($response ?: $error));
            return [
                'success' => false,
                'response' => "Estou com problemas para me conectar (Erro $httpCode). Por favor, verifique sua configuração ou tente novamente.",
                'error' => "HTTP $httpCode: " . ($response ?: $error)
            ];
        }

        $result = Json::decode($response);
        $botResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? "Desculpe, não tenho certeza de como responder a isso.";

        return [
            'success' => true,
            'response' => $botResponse,
            'error' => null
        ];
    }
}
