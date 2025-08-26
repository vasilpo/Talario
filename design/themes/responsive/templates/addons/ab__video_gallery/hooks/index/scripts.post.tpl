<script>
    (function (_, $) {
        _.ab__video_gallery = {
            settings: {
                on_thumbnail_click: '{$addons.ab__video_gallery.on_thumbnail_click}',
                controls: '{$addons.ab__video_gallery.controls}',
                loop: '{$addons.ab__video_gallery.repeat}',
            },
            players: { },
            youtube_api_loaded: 0,
            vimeo_api_loaded: 0
        };
    })(Tygh, Tygh.$);
</script>
{script src="js/addons/ab__video_gallery/func.js"}