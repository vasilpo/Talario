{if $talario_class.product_id}
    {$talario_class_page_title = "Редактировать занятие"}
{else}
    {$talario_class_page_title = "Новое занятие"}
{/if}
{capture name="mainbox"}
<div class="talario-cabinet talario-class-editor">
    {if $talario_class.talario_revision_comment}
        <div class="alert alert-warning"><strong>Занятие нужно доработать.</strong><br />Комментарий Talario: {$talario_class.talario_revision_comment}</div>
    {/if}
    <form action="{""|fn_url}" method="post" enctype="multipart/form-data" class="form-horizontal form-edit" name="talario_class_form">
        <input type="hidden" name="dispatch" value="talario_classes.save_class" />
        <input type="hidden" name="security_hash" value="{$security_hash}" />
        {if $talario_class.product_id}
            <input type="hidden" name="product_id" value="{$talario_class.product_id}" />
        {/if}

        <section class="talario-todo">
            <div class="control-group">
                <label class="control-label cm-required" for="elm_talario_class_name">
                    Название занятия
                    <span class="cm-tooltip talario-help" title="Как называется ваше занятие. Это название клиенты увидят на витрине.">?</span>:
                </label>
                <div class="controls">
                    <input type="text" id="elm_talario_class_name" name="class_data[product]" value="{$talario_class.product}" class="input-xxlarge" required />
                </div>
            </div>

            <div class="control-group talario-variants">
                <label class="control-label">Варианты занятия:</label>
                <div class="controls">
                    <p class="description">Добавьте варианты, если цена или время занятия зависят от возраста, уровня, предмета или другого параметра.</p>
                    <p class="muted description">Например: «3–5 лет — 2 000 ₽» и «6–9 лет — 2 000 ₽».</p>

                    {if $talario_class.variations}
                        <div class="talario-variation-list">
                            {foreach $talario_class.variations as $variation}
                                <div class="talario-variation-card">
                                    <div class="talario-variation-card__name">
                                        <span class="muted">Вариант</span>
                                        <strong>{$variation.talario_label|default:$variation.product}</strong>
                                    </div>
                                    <label class="talario-variation-card__price">
                                        <span>Цена, ₽</span>
                                        <input type="number" min="0" step="0.01" name="class_data[variation_prices][{$variation.product_id}]" value="{$variation.price}" class="input-small" required />
                                    </label>
                                    <a class="btn" href="{"talario_classes.schedule?product_id=`$variation.product_id`&parent_product_id=`$talario_class.product_id`"|fn_url}">Расписание</a>
                                    <label class="checkbox talario-variation-card__delete">
                                        <input type="checkbox" name="class_data[delete_variations][]" value="{$variation.product_id}" /> Удалить
                                    </label>
                                </div>
                            {/foreach}
                        </div>
                        <p class="muted description">У каждого варианта своя цена и расписание. Цена «от» рассчитывается автоматически.</p>
                    {/if}

                    {if $talario_class.product_id && $talario_variation_axes}
                        {if !$talario_variation_group_id}
                            <div class="talario-variation-axis">
                                <label for="talario_variation_axis"><strong>Чем отличаются варианты?</strong></label>
                                <select id="talario_variation_axis" class="input-xlarge">
                                    <option value="">Выберите один параметр</option>
                                    {foreach $talario_variation_axes as $axis}
                                        <option value="{$axis.feature_id}">{$axis.description|default:$axis.internal_name}</option>
                                    {/foreach}
                                </select>
                                <p class="muted description">Выберите то, что родитель будет переключать в карточке занятия.</p>
                            </div>
                        {else}
                            <p class="talario-variation-axis-summary">
                                <strong>Варианты отличаются по:</strong>
                                {foreach $talario_variation_axes as $axis}{$axis.description|default:$axis.internal_name}{if !$axis@last}, {/if}{/foreach}
                            </p>
                        {/if}

                        <div id="talario_new_variations" class="talario-variation-list"></div>
                        <script type="text/x-talario-template" id="talario_variation_row_template">
                            <div class="talario-variation-card talario-variation-card--new">
                                <div class="talario-variation-card__fields">
                                    {foreach $talario_variation_axes as $axis}
                                        <label class="talario-variation-field{if !$talario_variation_group_id} hidden{/if}" data-axis-id="{$axis.feature_id}">
                                            <span>{$axis.description|default:$axis.internal_name}</span>
                                            <select data-name="class_data[new_variations][__INDEX__][variants][{$axis.feature_id}]" class="input-medium" required{if !$talario_variation_group_id} disabled{/if}>
                                                <option value="">Выберите</option>
                                                {foreach $axis.variants as $variant}<option value="{$variant.variant_id}">{$variant.variant}</option>{/foreach}
                                            </select>
                                        </label>
                                    {/foreach}
                                </div>
                                <label class="talario-variation-card__price">
                                    <span>Цена, ₽</span>
                                    <input data-name="class_data[new_variations][__INDEX__][price]" type="number" min="0" step="0.01" class="input-small" required />
                                </label>
                                <button type="button" class="btn cm-talario-remove-variation">Удалить</button>
                            </div>
                        </script>
                        <button type="button" class="btn" id="talario_add_variation"{if !$talario_variation_group_id} disabled{/if}>Добавить вариант</button>
                    {elseif !$talario_class.product_id}
                        <p class="muted description">Варианты можно добавить после сохранения черновика.</p>
                    {else}
                        <p class="muted description">Для выбранной категории варианты пока не настроены. Обратитесь в Talario.</p>
                    {/if}
                </div>
            </div>

            <div class="control-group">
                <label class="control-label cm-required" for="elm_talario_class_location">Где проходит занятие:</label>
                <div class="controls">
                    {if $talario_class_locations|count == 1}
                        {foreach $talario_class_locations as $location}
                            <input type="hidden" name="class_data[location_id]" value="{$location.location_id}" />
                            <strong>{$location.address}</strong>
                        {/foreach}
                    {else}
                    <select id="elm_talario_class_location" name="class_data[location_id]" class="input-xxlarge" required>
                        {foreach $talario_class_locations as $location}
                            <option value="{$location.location_id}" {if $location.location_id == $talario_class_location_id}selected="selected"{/if}>
                                {if $location.name}{$location.name} — {/if}{$location.address}
                            </option>
                        {/foreach}
                    </select>
                    {/if}
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
                        {if $talario_class.variations}
                            <input type="hidden" name="class_data[price]" value="{$talario_class.price}" />
                            <strong>{$talario_class.price} ₽</strong>
                        {else}
                            <input type="number" min="0" step="0.01" id="elm_talario_class_price" name="class_data[price]" value="{$talario_class.price}" class="input-small" required />
                            <span class="add-on">₽</span>
                        {/if}
                    </div>
                    <p class="muted description">{if $talario_class.variations}Рассчитана автоматически по самому доступному варианту.{else}Укажите стоимость; бесплатное занятие — 0 ₽.{/if}</p>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="elm_talario_class_age">Короткая подпись в каталоге:</label>
                <div class="controls">
                    <input type="text" id="elm_talario_class_age" name="class_data[catalog_age]" value="{$talario_class.short_description|strip_tags}" class="input-xlarge" />
                    <p class="muted description">Кратко опишите главное: возраст, формат или пользу занятия. Этот текст виден в каталоге.</p>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="elm_talario_class_description">О занятии:</label>
                <div class="controls">
                    <textarea id="elm_talario_class_description" name="class_data[full_description]" rows="10" class="input-xxlarge">{$talario_class.full_description}</textarea>
                    <p class="muted description">Расскажите родителям, чем занимаются дети, как проходит занятие и что важно знать перед записью.</p>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="elm_talario_class_keywords">Ключевые слова для поиска:</label>
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
            {if $talario_class.product_id && !$talario_class.variations}
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
    var $axis = $('#talario_variation_axis');
    $axis.on('change', function () {
        $('#talario_add_variation').prop('disabled', !this.value);
        $('#talario_new_variations').empty();
    });
    $('#talario_add_variation').on('click', function () {
        var html = $('#talario_variation_row_template').html().replace(/__INDEX__/g, index++);
        var $row = $(html);
        if ($axis.length) {
            var axisId = String($axis.val());
            $row.find('.talario-variation-field').each(function () {
                var isSelected = String($(this).attr('data-axis-id')) === axisId;
                $(this).toggleClass('hidden', !isSelected).find('select').prop('disabled', !isSelected);
            });
        }
        $row.find('[data-name]').each(function () { this.name = $(this).data('name'); });
        $('#talario_new_variations').append($row);
    });
    $(document).on('click', '.cm-talario-remove-variation', function () { $(this).closest('.talario-variation-card').remove(); });
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
