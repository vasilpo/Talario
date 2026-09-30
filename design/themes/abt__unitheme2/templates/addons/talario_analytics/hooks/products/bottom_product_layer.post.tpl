{if $product.address|trim && $settings.geo_maps.general.provider}
    {$talario_map_provider = $settings.geo_maps.general.provider}
    {$talario_map_api_key = $settings.geo_maps[$talario_map_provider]["`$talario_map_provider`_api_key"]}
    {if $talario_map_provider === "yandex"}
        {$talario_map_suggest_api_key = $settings.geo_maps.yandex.yandex_suggest_api_key}
    {/if}

    {script src="js/addons/geo_maps/maps.js"}
    {script src="js/addons/geo_maps/code.js"}
    {script src="js/addons/geo_maps/locate.js"}

    {if $talario_map_provider === "yandex"}
        {script src="js/addons/geo_maps/provider/yandex/index.js" cookie-name="yandex_maps"}
        {script src="js/addons/geo_maps/provider/yandex/maps.js" cookie-name="yandex_maps"}
        {script src="js/addons/geo_maps/provider/yandex/code.js" cookie-name="yandex_maps"}
        {script src="js/addons/geo_maps/provider/yandex/locate.js" cookie-name="yandex_maps"}
    {elseif $talario_map_provider === "google"}
        {script src="js/addons/geo_maps/provider/google/index.js" cookie-name="google_maps"}
        {script src="js/addons/geo_maps/provider/google/maps.js" cookie-name="google_maps"}
        {script src="js/addons/geo_maps/provider/google/code.js" cookie-name="google_maps"}
        {script src="js/addons/geo_maps/provider/google/locate.js" cookie-name="google_maps"}
    {/if}

    {script src="js/addons/geo_maps/func.js"}

    <script>
        (function (_, $) {
            _.geo_maps = {
                provider: '{$talario_map_provider|escape:"javascript"}',
                api_key: '{$talario_map_api_key|escape:"javascript"}',
                {if $talario_map_provider === "yandex"}
                    suggest_api_key: '{$talario_map_suggest_api_key|escape:"javascript"}',
                {/if}
                yandex_commercial: {if $settings.geo_maps.yandex.yandex_commercial === "Y"}true{else}false{/if},
                language: "{$smarty.const.CART_LANGUAGE}"
            };
        })(Tygh, Tygh.$);
    </script>

    <section class="talario-lesson-map" aria-label="{__("address")}">
        <div class="talario-lesson-map__address">{$product.address|escape}</div>
        <div
            class="cm-geo-map-container cm-aom-map-container talario-lesson-map__canvas"
            data-ca-geo-map-language="{$smarty.const.CART_LANGUAGE}"
            data-ca-aom-address="{$product.address|escape:"html"}"
            data-ca-geo-map-controls-enable-search="false"
            data-ca-geo-map-controls-enable-zoom="true"
            data-ca-geo-map-controls-enable-layers="false"
            data-ca-geo-map-behaviors-enable-drag="true"
            data-ca-geo-map-behaviors-enable-drag-on-mobile="false"
            data-ca-geo-map-behaviors-enable-smart-drag="true"
            data-ca-geo-map-behaviors-enable-scroll-zoom="false"
            data-ca-geo-map-behaviors-enable-dbl-click-zoom="true"
            data-ca-geo-map-behaviors-enable-multi-touch="true"
        ></div>
    </section>
{/if}
