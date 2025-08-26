{foreach from=$order_info.shipping item="shipping" key="shipping_id" name="f_shipp"}
    {if ($shipping.module === 'sdek2') && ($shipping.office_data)}
        <div class="well orders-right-pane form-horizontal">
            <div class="control-group shift-top">
                <div class="control-label">
                    {include file="common/subheader.tpl" title=__("rus_sdek2.header_shipping_office")}
                </div>
            </div>

            <p class="strong">{$shipping.office_data.code}, {$shipping.office_data.location.city}</p>

            <p class="muted">
                {$shipping.office_data.location.address}<br />
                {$shipping.office_data.work_time}<br />
                <bdi>{$shipping.office_data.phones.number}</bdi><br />
                {$shipping.office_data.note}<br />
            </p>
        </div>
    {/if}
{/foreach}
