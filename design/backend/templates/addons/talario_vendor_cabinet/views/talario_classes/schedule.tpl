{capture name="mainbox"}
{include file="addons/talario_vendor_cabinet/components/white_shell.tpl"}
{include file="addons/talario_vendor_cabinet/components/cabinet_style.tpl"}
<div class="talario-cabinet talario-schedule-editor">
    <p class="muted">Укажите, когда проходит занятие и сколько детей может прийти. После сохранения эти данные автоматически используются календарём на витрине.</p>

    {if !$talario_schedule_locations}
        <div class="alert alert-warning">
            Сначала добавьте филиал в разделе «Центр».
            <a href="{"talario_locations.update"|fn_url}">Добавить филиал</a>
        </div>
    {/if}

    <form action="{fn_url('talario_classes.save_schedule')}" method="post" name="talario_schedule_form" class="form-horizontal">
        <input type="hidden" name="product_id" value="{$talario_schedule_product.product_id}" />
        {if $talario_schedule_parent_product_id}
            <input type="hidden" name="parent_product_id" value="{$talario_schedule_parent_product_id}" />
        {/if}

        <div class="control-group">
            <label class="control-label" for="talario_location">Филиал:</label>
            <div class="controls">
                <select id="talario_location" name="schedule_data[location_id]" class="input-xlarge" required>
                    <option value="">Выберите филиал</option>
                    {foreach $talario_schedule_locations as $location}
                        <option value="{$location.location_id}" {if (int) $talario_schedule_data.location_id === (int) $location.location_id}selected{/if}>{$location.name} — {$location.address}</option>
                    {/foreach}
                </select>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="talario_duration">Продолжительность:</label>
            <div class="controls">
                <div class="input-append">
                    <input id="talario_duration" type="number" min="1" max="1440" step="1" class="input-small" name="schedule_data[duration_minutes]" value="{$talario_schedule_data.duration_minutes}" required />
                    <span class="add-on">минут</span>
                </div>
            </div>
        </div>

        <h3>Когда проходят занятия</h3>
        <table class="table table-middle talario-schedule-table">
            <thead><tr><th>День</th><th>Проходит</th><th>Начало</th><th>Мест</th></tr></thead>
            <tbody>
            {foreach $talario_weekdays as $weekday => $weekday_name}
                {$day_data = $talario_schedule_data.days[$weekday]|default:[]}
                <tr>
                    <td><strong>{$weekday_name}</strong></td>
                    <td><input type="checkbox" name="schedule_data[days][{$weekday}][enabled]" value="1" {if !empty($day_data.enabled)}checked{/if} /></td>
                    <td><input type="time" step="60" name="schedule_data[days][{$weekday}][start_time]" value="{$day_data.start_time|default:''}" /></td>
                    <td><input type="number" min="1" max="10000" class="input-small" name="schedule_data[days][{$weekday}][capacity]" value="{$day_data.capacity|default:''}" placeholder="6" /></td>
                </tr>
            {/foreach}
            </tbody>
        </table>

        <div class="buttons-container">
            {if $talario_schedule_parent_product_id}
                <a class="btn" href="{"talario_classes.update?product_id=`$talario_schedule_parent_product_id`"|fn_url}">Назад к занятию</a>
            {else}
                <a class="btn" href="{"talario_classes.manage"|fn_url}">Назад</a>
            {/if}
            <button class="btn btn-primary" type="submit" {if !$talario_schedule_locations}disabled{/if}>Сохранить расписание</button>
        </div>
    </form>
</div>
{/capture}
{include file="common/mainbox.tpl" title="Расписание: `$talario_schedule_product.product`" content=$smarty.capture.mainbox}
