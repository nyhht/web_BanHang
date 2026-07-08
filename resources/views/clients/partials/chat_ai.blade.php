<!-- Floating Chat -->
<div id="chat-widget" data-fetch-url="{{ route('chat.messages', [], false) }}" data-send-url="{{ route('chat.send', [], false) }}" data-csrf-token="{{ csrf_token() }}">
    <div id="chat-box" class="hidden" aria-live="polite">
        <div id="chat-header">
            <span>AI gợi ý món ăn</span>
            <button id="chat-close" type="button" aria-label="Đóng chat">&#9587;</button>
        </div>
        <div id="chat-messages">
        </div>
        <div id="chat-input">
            <input type="text" id="message-input" placeholder="Nhập tin nhắn...">
            <button id="send-btn" type="button">Gửi</button>
        </div>
    </div>

    <div id="chat-actions" aria-label="Hỗ trợ khách hàng">
        <button id="tawk-toggle" class="chat-action-btn" type="button" title="Chat trực tiếp với nhân viên" aria-label="Chat trực tiếp với nhân viên">
            <i class="fas fa-headset"></i>
        </button>
        <button id="chat-toggle" class="chat-action-btn" type="button" title="AI gợi ý món ăn" aria-label="Mở AI gợi ý món ăn" aria-controls="chat-box" aria-expanded="false">
            <i class="far fa-comment-dots"></i>
        </button>
    </div>
</div>
