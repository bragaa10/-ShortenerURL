<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Encurtador URLs',
    'baseUrl' => getenv('BASE_URL') ?: null,
    'qrCodePath' => 'uploads/qrcodes',
    'gemini_api_key' => getenv('GEMINI_API_KEY') ?: 'YOUR_GEMINI_API_KEY_HERE', // Put your Google Gemini API key here
];

