<div>
    <p>{__("predict.products.products_will_lose_some_features.warning", [$affected_feature_ids|count, "[product_search_url]" => "{fn_url("products.manage?product_ids_search_key={$product_ids_search_key}")}", "[feature_search_url]" => "{fn_url("product_features.manage?feature_ids_search_key={$feature_ids_search_key}")}", "[category_search_url]" => "{fn_url("categories.manage?group_by_level=0&item_ids={","|implode:$not_affected_categories_ids}")}"])}</p>
</div>
<div class="buttons-container pull-right">
    {include file="buttons/button.tpl" but_meta="cm-notification-close" but_text=__("cancel") but_role="action"}
    {if $confirm_dispatch}
        <a class="btn btn-primary cm-ajax cm-post cm-notification-close" href="{fn_url("$confirm_dispatch?confirm=1")}">{__("proceed")}</a>
    {else}
        {include file="buttons/button.tpl" but_text=__("proceed") but_name="dispatch[{$dispatch}]" but_role="submit-link" but_target_form=$form but_onclick="$('form[name={$form}').data('confirmed', true)"}
    {/if}
</div>
