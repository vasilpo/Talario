<script data-no-defer>
    const telegram_desc = "{$settings_pr.telegram.data.text}";
    document.getElementById('telegram_extended_share_btn')
        .setAttribute('href',"https://telegram.me/share/url?url=" + encodeURIComponent(window.location.href) + '&text' + encodeURIComponent(telegram_desc));
</script>
