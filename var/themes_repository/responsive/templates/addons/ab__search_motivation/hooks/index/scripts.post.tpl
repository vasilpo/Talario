{if !($runtime.controller == "products" && $runtime.mode == "search")}
<script>
    (function (_, $) {
        _.tr({
            'ab__sm.random_char': '{__('ab__sm.random_char')|escape:'javascript'}',
        });

        $.extend(_, {
            ab__sm: {
                phrases: {(""|fn_ab__search_motivation_get_phrases)|json_encode:$smarty.const.JSON_UNESCAPED_UNICODE nofilter},
                delay: {intval($addons.ab__search_motivation.delay|default:0) * 1000},
            }
        });
    })(Tygh, Tygh.$);
</script>
{script src="js/addons/ab__search_motivation/lib/theater.js"}
{script src="js/addons/ab__search_motivation/func.js"}
{/if}