{if $talario_class.product_id}
    {$talario_class_page_title = "Редактировать занятие"}
{else}
    {$talario_class_page_title = "Новое занятие"}
{/if}
{capture name="mainbox"}
<div class="talario-cabinet talario-class-editor">
    <form action="{""|fn_url}" method="post" enctype="multipart/form-data" class="form-horizontal form-edit" name="talario_class_form">
        <input type="hidden" name="dispatch" value="talario_classes.save_class" />
        <input type="hidden" name="security_hash" value="{$security_hash}" />
        {if $talario_class.product_id}
            <input type="hidden" name="product_id" value="{$talario_class.product_id}" />
        {/if}

        <section class="talario-todo">
            <div class="control-group">
                <label class="control-label cm-required" for="elm_talario_class_name">
                    Название
                    <span class="cm-tooltip talario-help" title="Как называется ваше занятие. Это название клиенты увидят на витрине.">?</span>:
                </label>
                <div class="controls">
                    <input type="text" id="elm_talario_class_name" name="class_data[product]" value="{$talario_class.product}" class="input-xxlarge" required />
                </div>
            </div>

            {if $talario_class.variations}
                <div class="control-group">
                    <label class="control-label">Варианты занятия:</label>
                    <div class="controls">
                        <p class="muted description">Особенности вариантов берутся из настроенных для занятия характеристик. Цена «от» будет выбрана автоматически.</p>
                        <table class="table table-middle">
                            <thead><tr><th>Вариант</th><th>Цена, ₽</th><th></th></tr></thead>
                            <tbody>
                            {foreach $talario_class.variations as $variation}
                                <tr>
                                    <td>{$variation.product}</td>
                                    <td><input type="number" min="0" step="0.01" name="class_data[variation_prices][{$variation.product_id}]" value="{$variation.price}" class="input-small" required /></td>
                                    <td><label class="checkbox"><input type="checkbox" name="class_data[delete_variations][]" value="{$variation.product_id}" /> Удалить</label></td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </div>
                </div>
            {/if}

            {if $talario_class.product_id && $talario_variation_axes}
                <div class="control-group">
                    <label class="control-label">Добавить варианты:</label>
                    <div class="controls">
                        <p class="muted description">Добавьте только те сочетания, которые вы действительно проводите. Новые сочетания не создаются автоматически.</p>
                        <table class="table table-middle" id="talario_new_variations">
                            <thead><tr>{foreach $talario_variation_axes as $axis}<th>{$axis.description|default:$axis.internal_name}</th>{/foreach}<th>Цена, ₽</th><th></th></tr></thead>
                            <tbody></tbody>
                        </table>
                        <script type="text/x-talario-template" id="talario_variation_row_template">
                            <tr>
                                {foreach $talario_variation_axes as $axis}
                                    <td><select data-name="class_data[new_variations][__INDEX__][variants][{$axis.feature_id}]" class="input-medium" required>
                                        <option value="">—</option>
                                        {foreach $axis.variants as $variant}<option value="{$variant.variant_id}">{$variant.variant}</option>{/foreach}
                                    </select></td>
                                {/foreach}
                                <td><input data-name="class_data[new_variations][__INDEX__][price]" type="number" min="0" step="0.01" class="input-small" required /></td>
                                <td><button type="button" class="btn cm-talario-remove-variation">Удалить</button></td>
                            </tr>
                        </script>
                        <button type="button" class="btn" id="talario_add_variation">Добавить сочетание</button>
                    </div>
                </div>
            {elseif !$talario_class.product_id}
                <div class="control-group"><div class="controls muted">Сначала сохраните основные данные как черновик — после этого можно будет добавить варианты.</div></div>
            {/if}

            <div class="control-group">
                <label class="control-label cm-required" for="elm_talario_class_location">Адрес занятия:</label>
                <div class="controls">
                    <select id="elm_talario_class_location" name="class_data[location_id]" class="input-xxlarge" required>
                        {foreach $talario_class_locations as $location}
                            <option value="{$location.location_id}" {if $location.location_id == $talario_class_location_id}selected="selected"{/if}>
                                {if $location.name}{$location.name} — {/if}{$location.address}
                            </option>
                        {/foreach}
                    </select>
                    <p class="muted description">Адрес берётся из раздела «Центр». Если адресов несколько, выберите нужный.</p>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label cm-required" for="elm_talario_class_category">Категория:</label>
                <div class="controls">
                    <select id="elm_talario_class_category" name="class_data[category_id]" class="input-xlarge" required>
                        <option value="">Выберите категорию</option>
                        {foreach $talario_class_categories as $category}
                            <option value="{$category.category_id}" {if $category.category_id == $talario_class_category_id}selected="selected"{/if}>{$category.category}</option>
                        {/foreach}
                    </select>
                    <p class="muted description">Выберите одну из категорий Talario. Так занятие попадёт в нужный раздел витрины.</p>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label cm-required" for="elm_talario_class_price">Цена от:</label>
                <div class="controls">
                    <div class="input-append">
                        <input type="number" min="0" step="0.01" id="elm_talario_class_price" name="class_data[price]" value="{$talario_class.price}" class="input-small" required />
                        <span class="add-on">₽</span>
                    </div>
                    <p class="muted description">Укажите самую низкую стоимость занятия. Если есть бесплатное пробное занятие, укажите 0 ₽. Эта цена будет показана в каталоге по умолчанию.</p>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="elm_talario_class_age">Возраст в каталоге:</label>
                <div class="controls">
                    <input type="text" id="elm_talario_class_age" name="class_data[catalog_age]" value="{$talario_class.short_description|strip_tags}" class="input-xlarge" />
                    <p class="muted description">Напишите коротко, для какого возраста занятие, например «с 4 лет» или «7–10 лет». Этот текст увидят в карточке каталога.</p>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="elm_talario_class_description">Описание:</label>
                <div class="controls">
                    <textarea id="elm_talario_class_description" name="class_data[full_description]" rows="10" class="input-xxlarge">{$talario_class.full_description}</textarea>
                    <p class="muted description">Расскажите родителям, чем занимаются дети, как проходит занятие и что важно знать перед записью.</p>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="elm_talario_class_keywords">Ключевые слова:</label>
                <div class="controls">
                    <input type="text" id="elm_talario_class_keywords" name="class_data[meta_keywords]" value="{$talario_class.meta_keywords}" class="input-xxlarge" />
                    <p class="muted description">По каким словам родители могут искать ваше занятие. Перечислите слова и короткие фразы через запятую.</p>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Фото занятия:</label>
                <div class="controls talario-class-image">
                    {include file="common/form_file_uploader.tpl"
                        existing_pairs=(($talario_class.main_pair) ? [$talario_class.main_pair] : []) + $talario_class.image_pairs|default:[]
                        file_name="file"
                        image_pair_types=['N' => 'product_add_additional_image', 'M' => 'product_main_image', 'A' => 'product_additional_image']
                        image_object_id=$talario_class.product_id
                        allow_update_files=true
                    }
                    <p class="muted description">Добавьте несколько фото и отметьте главное — оно будет видно в каталоге.</p>
                </div>
            </div>
        </section>

        <div class="buttons-container talario-class-editor__buttons">
            <a class="btn" href="{"talario_classes.manage"|fn_url}">Отменить</a>
            {if $talario_class.product_id}
                <a class="btn" href="{"talario_classes.schedule?product_id=`$talario_class.product_id`"|fn_url}">Расписание</a>
            {/if}
            <button type="submit" name="save_action" value="draft" class="btn">Сохранить черновик</button>
            <button type="button" id="talario_preview_button" class="btn">Предварительный просмотр</button>
            <button type="submit" name="save_action" value="submit" class="btn btn-primary">Отправить на проверку</button>
        </div>
    </form>
</div>
<script>
(function (_, $) {
    var index = 0;
    $('#talario_add_variation').on('click', function () {
        var html = $('#talario_variation_row_template').html().replace(/__INDEX__/g, index++);
        var $row = $(html);
        $row.find('[data-name]').each(function () { this.name = $(this).data('name'); });
        $('#talario_new_variations tbody').append($row);
    });
    $(document).on('click', '.cm-talario-remove-variation', function () { $(this).closest('tr').remove(); });
    $('#talario_preview_button').on('click', function () {
        var form = this.form;
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        var previewWindow = window.open('about:blank', 'talario_class_preview');
        if (!previewWindow) {
            return;
        }
        var action = document.createElement('input');
        action.type = 'hidden';
        action.name = 'save_action';
        action.value = 'preview';
        form.appendChild(action);
        form.target = 'talario_class_preview';
        form.submit();
        form.target = '';
        form.removeChild(action);
    });
    {if $talario_preview_url}
        var updateUrl = {"talario_classes.update?product_id=`$talario_class.product_id`"|fn_url|json_encode nofilter};
        if (window.opener && !window.opener.closed) {
            window.opener.location.replace(updateUrl);
        }
        window.opener = null;
        window.location.replace({$talario_preview_url|json_encode nofilter});
    {/if}
}(Tygh, Tygh.$));
</script>
{/capture}
{include file="common/mainbox.tpl" title=$talario_class_page_title content=$smarty.capture.mainbox}
