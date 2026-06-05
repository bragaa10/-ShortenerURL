<?php
/** @var yii\web\View $this */
?>
<!-- Chatbot Widget -->
<div id="chatbot-widget">
    <div id="chatbot-window">
        <div class="chatbot-header">
            <img src="<?= Yii::getAlias('@web/img/linky.png') ?>" class="avatar-img" alt="Linky">
            <div>
                <h4>Linky Assistant</h4>
                <span><i class="bi bi-circle-fill text-success" style="font-size: 8px;"></i> AI Online</span>
            </div>
            <div id="chatbot-close"><i class="bi bi-x-lg"></i></div>
        </div>
        <div class="chatbot-messages" id="chatbot-messages"></div>
        <div class="typing-indicator" id="typing-indicator" style="padding-left:15px;">Assistant is typing...</div>
        <form id="chatbot-form" class="chatbot-input">
            <input type="text" id="chatbot-input-field" placeholder="Ask me something..." autocomplete="off">
            <button type="submit"><i class="bi bi-send-fill"></i></button>
        </form>
    </div>
    <div id="chatbot-button">
        <i class="bi bi-chat-dots-fill"></i>
    </div>
</div>
