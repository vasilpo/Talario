<script>
    (function (_, $) {
        $(document).ready(function() {
            $.getScript('js/addons/ab__deal_of_the_day/js_counter.js?v={$addons.ab__deal_of_the_day.build}', function () {
                ab_dotd_js_counter('ab__deal_of_the_day_{$block.block_id}', {$total_seconds}, JSON.parse('{json_encode(fn_ab__dotd_get_time_units_plurals()) nofilter}'));
            });
        });
    })(Tygh, Tygh.$);
</script>