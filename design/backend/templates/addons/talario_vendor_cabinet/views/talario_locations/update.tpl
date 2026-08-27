{capture name="mainbox"}
<form action="{""|fn_url}" method="post" name="talario_location_form" class="form-horizontal form-edit">
    <input type="hidden" name="dispatch" value="talario_locations.update" />
    {if $talario_location.location_id}
        <input type="hidden" name="location_id" value="{$talario_location.location_id}" />
    {/if}

    <div class="control-group">
        <label class="control-label cm-required" for="elm_talario_location_name">Название филиала:</label>
        <div class="controls">
            <input type="text" id="elm_talario_location_name" name="location_data[name]" value="{$talario_location.name}" class="input-large" />
            <p class="muted">Напишите название или ориентир, по которому вы отличаете этот филиал от других.</p>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label cm-required" for="elm_talario_location_address">Адрес:</label>
        <div class="controls">
            <input type="text" id="elm_talario_location_address" name="location_data[address]" value="{$talario_location.address}" class="input-xlarge" />
            <p class="muted">Укажите полный адрес, где проходят занятия.</p>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="elm_talario_location_details">Как найти:</label>
        <div class="controls">
            <textarea id="elm_talario_location_details" name="location_data[address_details]" rows="4" class="input-xlarge">{$talario_location.address_details}</textarea>
            <p class="muted">Напишите так, чтобы клиентам было легко вас найти: вход, этаж или ориентир.</p>
        </div>
    </div>

    <div class="buttons-container">
        <a class="btn" href="{"talario_locations.manage"|fn_url}">Отменить</a>
        <button type="submit" class="btn btn-primary">Сохранить</button>
    </div>
</form>
{/capture}
{include file="common/mainbox.tpl" title="Филиал" content=$smarty.capture.mainbox}
