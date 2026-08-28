{capture name="mainbox"}
<div class="talario-cabinet">
    <section class="talario-todo">
        <h2>Центр</h2>
        <p class="muted">Эти данные будут использоваться в занятиях по умолчанию.</p>
        <form action="{""|fn_url}" method="post" class="form-horizontal form-edit">
            <input type="hidden" name="dispatch" value="talario_locations.update_center" />

            <div class="control-group">
                <label class="control-label cm-required" for="elm_talario_center_name">Название центра:</label>
                <div class="controls">
                    <input type="text" id="elm_talario_center_name" name="center_data[name]" value="{$talario_center.name}" class="input-xxlarge" />
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="elm_talario_center_description">О центре:</label>
                <div class="controls">
                    <input type="text" id="elm_talario_center_description" name="center_data[description]" value="{$talario_center.description}" maxlength="180" class="input-xxlarge" />
                    <p class="muted description">Одним предложением расскажите родителям, чем занимается ваш центр.</p>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label cm-required" for="elm_talario_center_address">Адрес:</label>
                <div class="controls">
                    <input type="text" id="elm_talario_center_address" name="center_data[address]" value="{$talario_center.address}" class="input-xxlarge" />
                    <p class="muted description">Основной адрес, где проходят занятия.</p>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="elm_talario_center_details">Как найти:</label>
                <div class="controls">
                    <textarea id="elm_talario_center_details" name="center_data[address_details]" rows="3" class="input-xxlarge">{$talario_center.address_details}</textarea>
                    <p class="muted description">Напишите так, чтобы клиентам было легко вас найти: вход, этаж или ориентир.</p>
                </div>
            </div>

            <div class="buttons-container">
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </div>
        </form>
    </section>

    <section class="talario-todo">
        <div class="talario-dashboard__header">
            <div>
                <h2>Филиалы</h2>
                <p class="muted">Если занятия проходят ещё по другим адресам, добавьте их здесь. Это необязательно.</p>
            </div>
            <a class="btn btn-primary" href="{"talario_locations.update"|fn_url}">+ Добавить филиал</a>
        </div>

        {if $talario_locations}
            <table class="table table-middle">
                <thead>
                    <tr>
                        <th>Филиал</th>
                        <th>Адрес</th>
                        <th class="right">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                {foreach $talario_locations as $location}
                    <tr>
                        <td><strong>{if $location.name}{$location.name}{else}Филиал{/if}</strong></td>
                        <td>
                            {$location.address}
                            {if $location.address_details}<div class="muted">{$location.address_details}</div>{/if}
                            {if $location.status !== "A"}<div class="muted">Приостановлен</div>{/if}
                        </td>
                        <td class="right nowrap">
                            <a class="btn" href="{"talario_locations.update?location_id=`$location.location_id`"|fn_url}">Редактировать</a>
                            <form action="{""|fn_url}" method="post" class="inline-block">
                                <input type="hidden" name="dispatch" value="talario_locations.update_status" />
                                <input type="hidden" name="location_id" value="{$location.location_id}" />
                                <input type="hidden" name="status" value="{if $location.status === "A"}D{else}A{/if}" />
                                <button type="submit" class="btn">{if $location.status === "A"}Приостановить{else}Возобновить{/if}</button>
                            </form>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        {else}
            <div class="no-items">Дополнительных филиалов пока нет.</div>
        {/if}
    </section>
</div>
{/capture}
{include file="common/mainbox.tpl" title="Центр" content=$smarty.capture.mainbox}
