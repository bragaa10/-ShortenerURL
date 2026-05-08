<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Política de Privacidade';
?>

<div class="page-header">
    <h1><i class="bi bi-shield-check"></i> Política de Privacidade</h1>
</div>

<div class="data-card">
    <div class="data-card-header">
        <h3>Encurtador URLs — Política de Privacidade e RGPD</h3>
        <span style="color: var(--text-muted); font-size: 13px;">Última atualização: <?= date('d/m/Y') ?></span>
    </div>
    <div class="data-card-body" style="line-height: 1.8; color: var(--text-secondary);">

        <h4 style="color: var(--text-primary); margin: 24px 0 12px;">1. Dados Recolhidos</h4>
        <p>Ao utilizar esta plataforma, recolhemos os seguintes dados para fins de tracking de acessos a links encurtados:</p>
        <ul style="padding-left: 24px; margin-bottom: 16px;">
            <li>Endereço IP (anonimizado após 90 dias)</li>
            <li>Data e hora do acesso</li>
            <li>User-Agent do browser</li>
            <li>País e cidade (obtidos por geolocalização do IP)</li>
            <li>Tipo de dispositivo, sistema operativo e browser</li>
            <li>URL de origem (Referer)</li>
            <li>Parâmetros UTM (se presentes no link)</li>
            <li>Idioma do browser</li>
        </ul>

        <h4 style="color: var(--text-primary); margin: 24px 0 12px;">2. Finalidade do Tratamento</h4>
        <p>Os dados recolhidos são utilizados exclusivamente para:</p>
        <ul style="padding-left: 24px; margin-bottom: 16px;">
            <li>Fornecer estatísticas ao criador do link (quantos acessos, de onde, etc.)</li>
            <li>Detetar abusos e links maliciosos</li>
            <li>Melhorar o desempenho da plataforma</li>
        </ul>

        <h4 style="color: var(--text-primary); margin: 24px 0 12px;">3. Retenção de Dados</h4>
        <p>Os registos de acesso (scan logs) são conservados por um período máximo de <strong>365 dias</strong>.
        Os dados de IPs são anonimizados ao fim de <strong>90 dias</strong>. Os utilizadores podem solicitar a eliminação
        dos seus dados a qualquer momento.</p>

        <h4 style="color: var(--text-primary); margin: 24px 0 12px;">4. Os Seus Direitos (RGPD)</h4>
        <p>Ao abrigo do Regulamento Geral sobre a Proteção de Dados (RGPD), tem os seguintes direitos:</p>
        <ul style="padding-left: 24px; margin-bottom: 16px;">
            <li><strong>Direito de Acesso</strong> — Consultar todos os dados que temos sobre si</li>
            <li><strong>Direito de Retificação</strong> — Corrigir dados incorretos</li>
            <li><strong>Direito ao Apagamento</strong> — Solicitar a eliminação da sua conta e dados</li>
            <li><strong>Direito de Oposição</strong> — Opor-se ao tratamento dos seus dados</li>
            <li><strong>Direito à Portabilidade</strong> — Exportar os seus dados em formato CSV</li>
        </ul>

        <h4 style="color: var(--text-primary); margin: 24px 0 12px;">5. Cookies</h4>
        <p>Utilizamos cookies técnicos essenciais para o funcionamento da plataforma (sessão, CSRF protection).
        Não utilizamos cookies de rastreamento de terceiros.</p>

        <h4 style="color: var(--text-primary); margin: 24px 0 12px;">6. Partilha de Dados</h4>
        <p>Os seus dados <strong>não são partilhados com terceiros</strong>, exceto para geolocalização de IPs
        (serviço externo ipapi.co, sujeito à sua própria política de privacidade). A geolocalização é feita
        no momento do acesso e apenas o resultado (país/cidade) é guardado.</p>

        <h4 style="color: var(--text-primary); margin: 24px 0 12px;">7. Segurança</h4>
        <p>Todas as passwords são armazenadas com hash bcrypt. As comunicações são realizadas sobre HTTPS.
        O acesso aos dados é restrito por autenticação e controlo de perfis de acesso.</p>

        <h4 style="color: var(--text-primary); margin: 24px 0 12px;">8. Contacto</h4>
        <p>Para exercer os seus direitos ou esclarecer dúvidas sobre privacidade, contacte o administrador
        desta plataforma.</p>

        <div style="margin-top: 32px; padding: 16px; background: rgba(16,185,129,0.08);
            border-left: 4px solid var(--accent-success); border-radius: 8px;">
            <p style="margin: 0; color: var(--accent-success); font-size: 14px;">
                <i class="bi bi-shield-fill-check"></i>
                <strong>Conformidade RGPD:</strong> Esta plataforma foi desenvolvida com privacidade por design,
                seguindo os princípios do Regulamento (UE) 2016/679.
            </p>
        </div>

    </div>
</div>
