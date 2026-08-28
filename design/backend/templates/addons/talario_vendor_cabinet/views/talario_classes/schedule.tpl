{capture name="mainbox"}
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
            <label class="control-label">Период:</label>
            <div class="controls talario-schedule-dates">
                <label>С <input type="date" name="schedule_data[from_date]" value="{$talario_schedule_data.from_date}" required /></label>
                <label>По <input type="date" name="schedule_data[to_date]" value="{$talario_schedule_data.to_date}" required /></label>
                <span class="muted">По умолчанию: с сегодняшнего дня на один год.</span>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="talario_duration">Продолжительность:</label>
            <div class="controls">
                <div class="input-append">
                    <input id="talario_duration" type="number" min="1" max="1440" step="5" class="input-small" name="schedule_data[duration_minutes]" value="{$talario_schedule_data.duration_minutes}" required />
                    <span class="add-on">минут</span>
                </div>
            </div>
        </div>

        <h3>Дни и время</h3>
        <table class="table table-middle talario-schedule-table">
            <thead>
                <tr>
                    <th>День</th>
                    <th>Проходит</th>
                    <th>Начало</th>
                    <th>Мест</th>
                </tr>
            </thead>
            <tbody>
            {foreach $talario_weekdays as $weekday => $weekday_name}
                {$day_data = $talario_schedule_data.days[$weekday]|default:[]}
                <tr>
                    <td><strong>{$weekday_name}</strong></td>
                    <td>
                        <input type="checkbox" name="schedule_data[days][{$weekday}][enabled]" value="1" {if !empty($day_data.enabled)}checked{/if} />
                    </td>
                    <td>
                        <input type="time" name="schedule_data[days][{$weekday}][start_time]" value="{$day_data.start_time|default:''}" />
                    </td>
                    <td>
                        <input type="number" min="1" max="10000" class="input-small" name="schedule_data[days][{$weekday}][capacity]" value="{$day_data.capacity|default:''}" placeholder="6" />
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>

        <div class="buttons-container">
            <a class="btn" href="{"talario_classes.manage"|fn_url}">Назад</a>
            <button class="btn btn-primary" type="submit" {if !$talario_schedule_locations}disabled{/if}>Сохранить расписание</button>
        </div>
    </form>
</div>
{/capture}
{include file="common/mainbox.tpl" title="Расписание: `$talario_schedule_product.product`" content=$smarty.capture.mainbox}
