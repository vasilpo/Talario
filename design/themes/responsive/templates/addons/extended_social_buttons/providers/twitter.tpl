<script data-no-defer>
    const twitter_data = "{$settings_pr.twitter.data}";
    document.getElementById("twitter_extended_share_btn")
        .setAttribute('href', "https://twitter.com/intent/tweet?" + decodeURIComponent(twitter_data).replace(/&amp;/g, '&') + "&url=" + encodeURIComponent(window.location.href));
</script>
