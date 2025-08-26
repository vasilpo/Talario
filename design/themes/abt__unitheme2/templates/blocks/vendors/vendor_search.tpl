{* the overall structure and toggle button should be identical to code in file templates/addons/abt__unitheme2_mv/overrides/blocks/vendors of "AB: UniTheme2 (Multi-Vendor)" module for equal display and switching mechanic in popup mode *}

<a href="javascript:void(0);" rel="nofollow" onclick="$(this).parent().next().toggleClass('hidden');$(this).next().toggleClass('view');$(this).toggleClass('hidden');" class="ut2-btn-search"><i class="ut2-icon-search"></i><i class="ut2-icon-baseline-close hidden"></i></a>

{*<div class="ut2-btn-search js_ut2-search__opener">
    <i class="ut2-icon-search"></i>
</div>*}

<div class="ty-search-block">
    {*<div class="js_ut2-search__closeer">
        <i class="ut2-icon-baseline-close"></i>
    </div>*}

    <form action="{""|fn_url}" name="search_form" method="get">
        <input type="hidden" name="subcats" value="Y" />
        <input type="hidden" name="pcode_from_q" value="Y" />
        <input type="hidden" name="status" value="A" />
        <input type="hidden" name="pshort" value="Y" />
        <input type="hidden" name="pfull" value="Y" />
        <input type="hidden" name="pname" value="Y" />
        <input type="hidden" name="pkeywords" value="Y" />
        <input type="hidden" name="search_performed" value="Y" />
        <input type="hidden" name="company_id" value="{$company_id}" />
        <input type="hidden" name="category_id" value="{$category_data.category_id}" />

        {hook name="vendor_search:additional_fields"}{/hook}

        {strip}
            <input type="text" name="q" value="{$search.q}" title="{__("block_vendor_search")}" class="ty-search-block__input cm-hint" />

            {include file="buttons/magnifier.tpl" but_name="companies.products" alt=__("storefront_search_button")}

        {/strip}
    </form>
</div>