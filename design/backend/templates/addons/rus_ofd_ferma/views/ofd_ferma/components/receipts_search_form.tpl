<div class="sidebar-row">
    <h6>{__("search")}</h6>

    <form action="{""|fn_url}" name="receipts_search_form" method="get">
        <div class='row-fluid'>
            <div class='span6'>
                <div class="sidebar-field">
                    <label for="elm_addon">{__("rus_ofd_ferma.receipts_list.type")}</label>
                    <select name="search[type]">
                        <option value="">{__("any")}</option>
                        {foreach from=$types item="type_name" key="type_id"}
                            <option value="{$type_id}" {if $search.type == $type_id} selected="selected"{/if}>{$type_name}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="sidebar-field">
                    <label for="elm_addon">{__("rus_ofd_ferma.receipts_list.order_id")}</label>
                    <input type='text' name="search[order_id]" value='{$search.order_id}'>
                </div>
            </div>
            <div class='span6'>
                <div class="sidebar-field">
                    {include file="common/period_selector.tpl" period=$search.receipts_period form_name="receipts_search_form" display="form" prefix="receipts_"}
                </div>
            </div>
        </div>
        
        <div class="sidebar-field">
            <input class="btn" type="submit" name="dispatch[ofd_ferma.receipts]" value="{__("search")}">
        </div>
    </form>
</div>
<hr />