<?php
/** @var yii\web\View $this */
/** @var string $shortCode */
/** @var string|null $error */

$this->title = 'Link Protegido';
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Protegido — Encurtador URLs</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --bg-primary: #0f1117;
            --bg-card: #1e2233;
            --border-color: #2a2d3a;
            --text-primary: #e8eaf0;
            --text-secondary: #8b8fa3;
            --accent-primary: #6366f1;
            --accent-warning: #f59e0b;
            --accent-danger: #ef4444;
            --bg-input: #1a1d28;
            --radius: 12px;
            --radius-sm: 8px;
            --gradient-primary: linear-gradient(135deg, #6366f1, #8b5cf6);
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            background-image:
                radial-gradient(ellipse at 20% 50%, rgba(99,102,241,0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 20%, rgba(245,158,11,0.06) 0%, transparent 50%);
        }
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
        }
        .icon {
            font-size: 48px;
            color: var(--accent-warning);
            text-align: center;
            display: block;
            margin-bottom: 16px;
        }
        h1 {
            font-size: 22px;
            font-weight: 700;
            text-align: center;
            margin: 0 0 8px;
        }
        p {
            color: var(--text-secondary);
            text-align: center;
            font-size: 14px;
            margin: 0 0 24px;
        }
        .error-msg {
            background: rgba(239,68,68,0.12);
            color: var(--accent-danger);
            border-left: 3px solid var(--accent-danger);
            border-radius: var(--radius-sm);
            padding: 10px 14px;
            font-size: 13px;
            margin-bottom: 16px;
        }
        label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }
        input[type="password"] {
            width: 100%;
            background: var(--bg-input);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            border-radius: var(--radius-sm);
            padding: 11px 14px;
            font-size: 15px;
            font-family: inherit;
            transition: border-color 0.2s;
            margin-bottom: 20px;
        }
        input[type="password"]:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
        }
        button {
            width: 100%;
            background: var(--gradient-primary);
            border: none;
            border-radius: var(--radius-sm);
            padding: 12px;
            font-size: 15px;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
            transition: opacity 0.2s;
            font-family: inherit;
        }
        button:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class="card">
        <i class="bi bi-lock-fill icon"></i>
        <h1>Link Protegido</h1>
        <p>Este link requer uma password para acesso. Por favor introduza a password abaixo.</p>

        <?php if ($error): ?>
            <div class="error-msg"><i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
            <label for="pw_input">Password de Acesso</label>
            <input type="password" id="pw_input" name="link_password" autofocus placeholder="Introduza a password...">
            <button type="submit"><i class="bi bi-arrow-right-circle"></i> Aceder ao Link</button>
        </form>
    </div>
</body>
</html>
