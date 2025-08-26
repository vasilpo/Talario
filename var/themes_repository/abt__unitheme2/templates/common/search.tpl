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
        <input type="hidden" name="match" value="all" />
        <input type="hidden" name="subcats" value="Y" />
        <input type="hidden" name="pcode_from_q" value="Y" />
        <input type="hidden" name="pshort" value="Y" />
        <input type="hidden" name="pfull" value="Y" />
        <input type="hidden" name="pname" value="Y" />
        <input type="hidden" name="pkeywords" value="Y" />
        <input type="hidden" name="search_performed" value="Y" />

        {hook name="search:additional_fields"}{/hook}

        {strip}
            {if $settings.General.search_objects}
                {assign var="search_title" value=__("storefront_search_general")}
            {else}
                {assign var="search_title" value=__("search_products")}
            {/if}
            <input type="text" name="q" value="{$search.q}" id="search_input{$smarty.capture.search_input_id}" title="{$search_title}" class="ty-search-block__input cm-hint" />
            {if $settings.General.search_objects}
                {include file="buttons/magnifier.tpl" but_name="search.results" alt=__("storefront_search_general")}
            {else}
                {include file="buttons/magnifier.tpl" but_name="products.search" alt=__("storefront_search_general")}
            {/if}
        {/strip}

        {capture name="search_input_id"}{$block.snapping_id}{/capture}

    </form>
</div>
