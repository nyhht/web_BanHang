<div class="logout-confirm-modal" id="logout-confirm-modal" aria-hidden="true">
    <div class="logout-confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="logout-confirm-title">
        <h4 id="logout-confirm-title">Xác nhận đăng xuất</h4>
        <p>Bạn có chắc chắn muốn thoát phiên làm việc?</p>
        <div class="logout-confirm-actions">
            <button type="button" class="btn btn-danger" id="logout-confirm-yes">Có</button>
            <button type="button" class="btn btn-secondary" id="logout-confirm-no">Không</button>
        </div>
    </div>
</div>

<style>
    .logout-confirm-modal {
        position: fixed;
        inset: 0;
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(0, 0, 0, 0.45);
    }

    .logout-confirm-modal.is-open {
        display: flex;
    }

    .logout-confirm-dialog {
        width: min(420px, 100%);
        padding: 24px;
        border-radius: 6px;
        background: #fff;
        box-shadow: 0 18px 50px rgba(0, 0, 0, 0.22);
        text-align: center;
    }

    .logout-confirm-dialog h4 {
        margin: 0 0 10px;
        font-size: 20px;
        font-weight: 700;
        color: #1f2933;
    }

    .logout-confirm-dialog p {
        margin: 0 0 22px;
        color: #52616b;
    }

    .logout-confirm-actions {
        display: flex;
        justify-content: center;
        gap: 12px;
    }

    .logout-confirm-actions .btn {
        min-width: 92px;
    }
</style>

<script>
    (function () {
        var modal = document.getElementById('logout-confirm-modal');
        var confirmButton = document.getElementById('logout-confirm-yes');
        var cancelButton = document.getElementById('logout-confirm-no');
        var logoutUrl = null;

        if (!modal || !confirmButton || !cancelButton) {
            return;
        }

        function openLogoutConfirm(url) {
            logoutUrl = url;
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            cancelButton.focus();
        }

        function closeLogoutConfirm() {
            logoutUrl = null;
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
        }

        document.addEventListener('click', function (event) {
            var trigger = event.target.closest('[data-logout-confirm]');

            if (!trigger) {
                return;
            }

            event.preventDefault();
            openLogoutConfirm(trigger.getAttribute('href'));
        });

        confirmButton.addEventListener('click', function () {
            if (logoutUrl) {
                window.location.href = logoutUrl;
            }
        });

        cancelButton.addEventListener('click', closeLogoutConfirm);

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeLogoutConfirm();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                closeLogoutConfirm();
            }
        });
    })();
</script>
