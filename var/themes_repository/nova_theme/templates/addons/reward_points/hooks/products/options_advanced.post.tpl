{if $runtime.controller === "products" && $runtime.mode === "view" || $quick_view}
    {* Disable the products:options_advanced hook for the product detail page and quick view *}
{else}
    {include file="design/themes/responsive/templates/addons/reward_points/hooks/products/options_advanced.post.tpl"
        show_price_values=$show_price_values
        dont_show_points=$dont_show_points
    }
{/if}