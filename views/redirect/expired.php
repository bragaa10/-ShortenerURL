<?php
/** @var string $reason */
/** @var string $message */

use yii\helpers\Html;
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Indisponível</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: #0f1117;
            color: #e8eaf0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
        }
        .container { max-width: 480px; padding: 40px 20px; }
        .icon {
            font-size: 72px;
            margin-bottom: 24px;
            display: block;
        }
        .expired .icon { color: #f59e0b; }
        .inactive .icon { color: #ef4444; }
        h1 { font-size: 28px; font-weight: 700; margin-bottom: 12px; }
        p { color: #8b8fa3; font-size: 16px; line-height: 1.6; }
        .back-link {
            display: inline-block;
            margin-top: 32px;
            padding: 12px 28px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: opacity 0.2s;
        }
        .back-link:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class="container <?= $reason === 'expired' ? 'expired' : 'inactive' ?>">
        <i class="bi <?= $reason === 'expired' ? 'bi-clock-history' : 'bi-x-circle' ?> icon"></i>
        <h1>Link Indisponível</h1>
        <p><?= Html::encode($message) ?></p>
        <p style="margin-top: 8px;">Se acredita que isto é um erro, contacte o administrador.</p>
        <a href="/" class="back-link"><i class="bi bi-house"></i> Página Inicial</a>
    </div>
</body>
</html>
