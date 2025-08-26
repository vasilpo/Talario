<div id="order_logs">
    {if $logs}
        {assign var="order_statuses" value=$smarty.const.STATUSES_ORDER|fn_get_simple_statuses:true} 
        <div class="table-responsive-wrapper">
        <table width="100%" class="table table-middle table--relative table-responsive">
            <thead>
                <tr>
                    <th width="5%" class="center">{__("id")}</th>
                    <th width="15%">{__("user")}</th>
                    <th width="15%" class="left">{__("action")}</th>
                    <th width="50%" class="left">{__("description")}</th>
                    <th width="15%" class="center">{__("date")}</th>
                </tr>
            </thead>
            {foreach from=$logs item=log}
            {math equation="x+1" x=$log_id|default:0 assign="log_id"}
                <tr>
                    <td data-th="{__("id")}" class="center">#{$log_id}</td>
                    <td data-th="{__("user")}">{if $log.user_id}
                        <a href="{"profiles.update&user_id=`$log.user_id`"|fn_url}" class="strong">{$log.firstname}&nbsp;{$log.lastname}</a>
                    {else}
                        {if $log.action == "rus_order_logs_order_created"}
                            {__('guest')}
                        {else}
                            {__('system')}
                        {/if}
                    {/if}</td>
                    <td data-th="{__("action")}" class="left">{__($log.action)}</td>
                    <td data-th="{__("description")}" class="left">{$log.description nofilter}</td>
                    <td data-th="{__("date")}" class="center">
                        {$log.timestamp|date_format:"`$settings.Appearance.date_format`"},&nbsp;{$log.timestamp|date_format:"`$settings.Appearance.time_format`"}
                    </td>
                </tr>
            {/foreach}
            </table>
        </div>
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}
<!--order_logs--></div>