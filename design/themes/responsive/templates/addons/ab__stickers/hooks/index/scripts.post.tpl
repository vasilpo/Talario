{strip}
    <script>
        (function (_, $) {
            const extend_obj = {
                ab__stickers: {
                    timeouts: { },
                    runtime: {
                        controller_mode: '{$runtime.controller}.{$runtime.mode}',
                        caching: Boolean({fn_ab__stickers_sticker_is_cache_allowed()}),
                        cache_key: 'ab__stickers_{fn_get_storage_data("cache_id")}',
                    },
                    settings: {
                        abt__ut2: {
                            less: {json_encode(fn_ab__stickers_get_unitheme_less_settings(true)) nofilter}
                        }
                    }
                }
            };

            if (_?.ab__stickers?.functions) {
                extend_obj.ab__stickers.functions = _.ab__stickers.functions;
            }

            $.extend(_, extend_obj);
        })(Tygh, Tygh.$);
    </script>

    {script src="js/addons/ab__stickers/func.js"}

    {$theme_name = fn_get_theme_path('[theme]')}

    {if $theme_name === 'abt__unitheme2'}
        {script src="js/addons/ab__stickers/abt__ut2.js"}
    {elseif $theme_name === 'abt__youpitheme'}
        {script src="js/addons/ab__stickers/abt__yt.js"}
    {else}
        {script src="js/addons/ab__stickers/responsive.js"}
    {/if}

    {* FIXME: Move to deal of the day addon *}
    {if $addons.ab__deal_of_the_day.status === 'ObjectStatuses::ACTIVE'|enum}
        {script src="js/addons/ab__stickers/ab__dotd.js"}
    {/if}
{/strip}