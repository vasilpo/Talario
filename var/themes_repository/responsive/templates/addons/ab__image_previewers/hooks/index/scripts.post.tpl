<script>
(function(_, $) {
    $.extend(_, {
        ab__ip_ps_settings: {
            display_zoom: {if $addons.ab__image_previewers.ps_display_zoom == "Y"} true {else} false {/if},
            display_fullscreen: {if $addons.ab__image_previewers.ps_display_fullscreen == "Y"} true {else} false {/if},
            close_with_gesture: {if $addons.ab__image_previewers.ps_close_with_gesture == "Y"} true {else} false {/if}
        }
    });
}(Tygh, Tygh.$));
</script>