{if $field.field_type == "ProfileFieldTypes::CHECKBOX"|enum}
    <label for="elm_field_link" class="control-label">{__("link")}:</label>
    <div class="controls">
        <input id="elm_field_link" class="input-large" type="text" name="field_data[link]" value="{$field.link}" />
    </div>
{/if}