<script data-no-defer>
    (function() {
        const telegram_desc = "{$settings_pr.telegram.data.text}";
        const btn = document.getElementById('telegram_extended_share_btn');
        if (btn) {
            btn.setAttribute('href', "https://telegram.me/share/url?url=" + encodeURIComponent(window.location.href) + '&text=' + encodeURIComponent(telegram_desc));
        }
    })();
</script>
