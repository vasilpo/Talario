{* Top reset button on filters popup for mobile *}
{if $ajax_div_ids}{strip}
    <div class="ty-product-filters__tools-top {if !$is_selected_filters}ty-product-filters__tools-top--hide{/if}" {""}
        data-ca-product-filters="toolsTop" {""}
    >
        <a href="{$filter_base_url|fn_url}" {""}
            rel="nofollow" {""}
            class="ty-btn ty-product-filters__reset-button-top cm-ajax cm-ajax-full-render cm-history {""}
                {if !$products}ty-btn__primary{/if}" {""}
            data-ca-event="ce.filtersinit" {""}
            data-ca-target-id="{$ajax_div_ids}" {""}
        >
            {include_ext file="common/icon.tpl"
                class="ty-icon-cw ty-product-filters__reset-icon-top"
            }
            {__("reset")}
        </a>
    </div>
{/strip}{/if}