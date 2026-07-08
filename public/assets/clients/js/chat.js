(function ($, window, document) {
    "use strict";

    if (!$) {
        return;
    }

    $(function () {
        var $widget = $("#chat-widget");

        if (!$widget.length) {
            return;
        }

        var $box = $("#chat-box");
        var $toggle = $("#chat-toggle");
        var $close = $("#chat-close");
        var $messages = $("#chat-messages");
        var $input = $("#message-input");
        var $send = $("#send-btn");
        var $tawkToggle = $("#tawk-toggle");
        var fetchUrl = $widget.data("fetch-url") || "/chat/messages";
        var sendUrl = $widget.data("send-url") || "/chat/send";
        var csrfToken =
            $widget.data("csrf-token") ||
            $('meta[name="csrf-token"]').attr("content") ||
            "";
        var tawkStatusTimer = null;

        function setScrollUpVisible(isVisible) {
            var $scrollUp = $("#scrollUp");

            if (!$scrollUp.length) {
                return;
            }

            if (isVisible) {
                $scrollUp.show();
            } else {
                $scrollUp.hide();
            }
        }

        function setChatOpen(isOpen) {
            $box.toggleClass("hidden", !isOpen);
            $toggle.attr("aria-expanded", isOpen ? "true" : "false");
            setScrollUpVisible(!isOpen);

            if (isOpen) {
                loadMessages();
                $input.trigger("focus");
            }
        }

        $toggle.on("click", function () {
            setChatOpen($box.hasClass("hidden"));
        });

        $close.on("click", function () {
            setChatOpen(false);
        });

        $send.on("click", sendMessage);

        $input.on("keypress", function (event) {
            if (event.which === 13) {
                event.preventDefault();
                sendMessage();
            }
        });

        $tawkToggle.on("click", openTawkChat);

        $(document)
            .on("tawk:ready", function () {
                clearTimeout(tawkStatusTimer);
                setTawkStatus("ready");

                if (window.__tawkOpenRequested) {
                    openTawkChat();
                }
            })
            .on("tawk:error", function () {
                clearTimeout(tawkStatusTimer);
                setTawkStatus("unavailable");
            });

        if (isTawkReady()) {
            setTawkStatus("ready");
        }

        function loadMessages() {
            $messages.html("");

            $.get(fetchUrl)
                .done(function (msgs) {
                    if (!Array.isArray(msgs) || msgs.length === 0) {
                        appendBotMessage("Xin chào, tôi có thể giúp gì cho bạn?");
                        return;
                    }

                    msgs.forEach(function (message) {
                        appendOne(message);
                    });
                    scrollMessagesToBottom();
                })
                .fail(function () {
                    appendBotMessage("Không tải được lịch sử chat. Vui lòng thử lại.", "is-error");
                });
        }

        function sendMessage() {
            var message = $input.val().trim();

            if (!message || $send.prop("disabled")) {
                return;
            }

            setSending(true);

            $.ajax({
                url: sendUrl,
                method: "POST",
                data: {
                    message: message,
                    _token: csrfToken,
                },
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                },
                dataType: "json",
            })
                .done(function (response) {
                    if (response.user) {
                        appendOne(response.user);
                    } else {
                        appendOne({ sender: "user", message: message });
                    }

                    if (response.bot) {
                        appendOne(response.bot);
                    }

                    $input.val("");
                })
                .fail(function (xhr) {
                    var errorMessage = "Lỗi: không gửi được tin nhắn.";

                    if (xhr.status === 419) {
                        errorMessage = "Phiên chat đã hết hạn. Vui lòng tải lại trang rồi thử lại.";
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    appendBotMessage(errorMessage, "is-error");
                })
                .always(function () {
                    setSending(false);
                    $input.trigger("focus");
                });
        }

        function setSending(isSending) {
            $send.prop("disabled", isSending).text(isSending ? "Đang gửi..." : "Gửi");
        }

        function appendBotMessage(message, extraClass) {
            appendOne({
                sender: "bot",
                message: message,
                extraClass: extraClass || "",
            });
        }

        function appendOne(message) {
            var className = message.sender === "user" ? "user-msg" : "bot-msg";
            var $message = $("<div/>", {
                class: $.trim(className + " " + (message.extraClass || "")),
                text: message.message || "",
            });

            $messages.append($message);
            scrollMessagesToBottom();
        }

        function scrollMessagesToBottom() {
            var messagesEl = $messages[0];

            if (messagesEl) {
                $messages.scrollTop(messagesEl.scrollHeight);
            }
        }

        function openTawkChat() {
            window.__tawkOpenRequested = true;

            if (isTawkReady()) {
                if (typeof window.Tawk_API.showWidget === "function") {
                    window.Tawk_API.showWidget();
                }

                window.Tawk_API.maximize();
                setTawkStatus("ready");
                return;
            }

            setTawkStatus("loading");
            clearTimeout(tawkStatusTimer);
            tawkStatusTimer = window.setTimeout(function () {
                if (!isTawkReady()) {
                    setTawkStatus("unavailable");
                }
            }, 8000);
        }

        function isTawkReady() {
            return !!(
                window.Tawk_API &&
                typeof window.Tawk_API.maximize === "function"
            );
        }

        function setTawkStatus(status) {
            $tawkToggle.removeClass("is-loading is-unavailable");

            if (status === "loading") {
                $tawkToggle
                    .addClass("is-loading")
                    .attr("title", "Đang kết nối Tawk")
                    .attr("aria-label", "Đang kết nối Tawk");
                return;
            }

            if (status === "unavailable") {
                $tawkToggle
                    .addClass("is-unavailable")
                    .attr("title", "Tawk chưa tải được, vui lòng thử lại")
                    .attr("aria-label", "Tawk chưa tải được, vui lòng thử lại");
                return;
            }

            $tawkToggle
                .attr("title", "Chat trực tiếp với nhân viên")
                .attr("aria-label", "Chat trực tiếp với nhân viên");
        }
    });
})(window.jQuery, window, document);
