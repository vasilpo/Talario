<script data-no-defer>
    const viber_text = "{$settings_pr.telegram.data.text}";;
    document.getElementById("viber_extended_share_btn")
        .setAttribute('href',"viber://forward?text=" + encodeURIComponent(viber_text + " " + window.location.href));
</script>
