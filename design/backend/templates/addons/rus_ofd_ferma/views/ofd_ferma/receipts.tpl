{capture name="mainbox"}

    {include file="addons/rus_ofd_ferma/views/ofd_ferma/components/receipts_search_form.tpl"}

    {include file="common/pagination.tpl" save_current_page=true save_current_url=true}

    {assign var="return_current_url" value=$config.current_url|escape:url}

    {if $receipts}
        <form action="{"ofd_ferma.receipts"|fn_url}" method="POST" enctype="multipart/form-data" > 
        <table width="100%" class="table table-middle">
            <thead>
                <tr>
                  <th>Номер</th>
                  <th>Тип</th>
                  <th>Статус</th>  
                  <th>Заказ</th>          
                  <th>Сумма</th>
                  <th>ФН</th>
                  <th>РНМ</th>
                  <th>ФДН</th>
                  <th>ФПД</th>
                  <th>Дата и время</th>
                  <th></th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$receipts item="receipt"}
                <tr>
                    <td>
                        {if $receipt['id_link']}
                            <a href='{$receipt['id_link']|trim:"'"}' target='_blank'>{$receipt['id']}</a>
                        {else}
                            {$receipt['id']}
                        {/if}
                    </td>
                    <td>{$receipt.type_name}</td>
                    <td>
                        {$receipt.status_message}
                    </td>
                    <td>
                        {if $receipt.order_id}
                            <a href="{"orders.details?order_id=`$receipt.order_id`"|fn_url}">{$receipt.order_id}</a>
                        {/if}    
                    </td>
                    <td>{$receipt['total']}</td>
                    <td>{$receipt['FN']}</td>
                    <td>{$receipt['RNM']}</td>
                    <td>{$receipt['FDN']}</td>
                    <td>{$receipt['FPD']}</td>
                    <td>{$receipt['created_at']}</td>

                    <td>
                        {if $receipt['update']}
                            <button type="submit" name="update" title="Обновить чек" value="{$receipt.id}" class="btn"><i class="icon-refresh"></i></button>
                        {/if}  
                   </td>
                </tr>
            {/foreach}
            <tbody>
        </table>
        </form>
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}

    {include file="common/pagination.tpl"}
{/capture}



{include file="common/mainbox.tpl" title=__("rus_ofd_ferma.receipts_list.title") content=$smarty.capture.mainbox buttons=$smarty.capture.buttons sidebar=$smarty.capture.sidebar}
