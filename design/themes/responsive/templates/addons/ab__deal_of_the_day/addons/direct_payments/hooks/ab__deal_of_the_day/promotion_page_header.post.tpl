{if $promotion.company_id}
    <div class="ty-control-group">
        <label for="" class="ty-control-group__label">{__("vendor")}:</label>
        <span class="ty-control-group__item ty-company-name">
            <a href="{fn_url("companies.products?company_id=`$promotion.company_id`")}">{fn_get_company_name($promotion.company_id)}</a>
        </span>
    </div>
{/if}