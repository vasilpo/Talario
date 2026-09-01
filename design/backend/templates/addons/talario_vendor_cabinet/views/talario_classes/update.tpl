{if $talario_class.product_id}
    {$talario_class_page_title = "Редактировать занятие"}
{else}
    {$talario_class_page_title = "Новое занятие"}
{/if}
{capture name="mainbox"}
{literal}<style>
/* Fallback for the vendor wizard: the backend stylesheet can be served from cache after a deployment. */
.talario-wizard{max-width:1040px;color:#292621}.talario-wizard__hero{display:flex;align-items:flex-end;justify-content:space-between;gap:24px;margin:0 0 18px}.talario-wizard__hero h2{margin:4px 0 0;font-size:26px;line-height:1.2}.talario-wizard__eyebrow,.talario-wizard__panel-heading>span{color:#947820;font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase}.talario-wizard__save-state{color:#6d6a64;font-size:13px}.talario-wizard__saved-dot{display:inline-block;width:8px;height:8px;margin:0 7px 1px 0;border-radius:50%;background:#4c995d}.talario-wizard__steps{display:flex;gap:8px;margin:0 0 20px;padding:14px;background:#fff;border:1px solid #e5e0d3;border-radius:12px;box-shadow:0 2px 10px rgba(64,51,23,.04)}.talario-wizard__step{display:flex;flex:1;align-items:center;gap:9px;min-width:0;padding:10px 12px;border-radius:9px;color:#77736d;text-decoration:none}.talario-wizard__step>span{display:flex;align-items:center;justify-content:center;width:25px;height:25px;border:1px solid #d7d2c7;border-radius:50%;font-size:13px;font-weight:700}.talario-wizard__step strong{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:14px}.talario-wizard__step.is-active{color:#765d12;background:#f6f0df}.talario-wizard__step.is-active>span{color:#fff;border-color:#9a7a1d;background:#9a7a1d}.talario-wizard__step.is-complete>span{color:#fff;border-color:#63936a;background:#63936a}.talario-wizard__panel{padding:30px;background:#fff;border:1px solid #e5e0d3;border-radius:12px;box-shadow:0 2px 10px rgba(64,51,23,.05)}.talario-wizard__panel-heading{max-width:720px;margin-bottom:26px}.talario-wizard__panel-heading h2{margin:7px 0 8px;font-size:28px;line-height:1.2}.talario-wizard__panel-heading p{margin:0;color:#736f68;font-size:15px;line-height:1.5}.talario-wizard__grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px 24px;max-width:900px}.talario-field{display:flex;flex-direction:column;gap:7px;min-width:0;margin:0;font-weight:600}.talario-field>span{color:#403c36;font-size:14px}.talario-field input,.talario-field select,.talario-field textarea,.talario-field__readonly{box-sizing:border-box;width:100%;min-height:42px;margin:0;padding:9px 11px;border:1px solid #d8d3c8;border-radius:8px;background:#fffdf8;box-shadow:none;font:inherit}.talario-field textarea{min-height:96px;resize:vertical;line-height:1.45}.talario-field small{color:#817c74;font-size:12px;font-weight:400;line-height:1.4}.talario-field__readonly{display:flex;align-items:center;color:#514c44;background:#f7f4ec}.talario-wizard__actions{display:flex;justify-content:space-between;gap:12px;margin-top:20px;padding:16px 0 0;border-top:1px solid #e7e1d7}.talario-wizard__actions .btn-primary,.talario-preview-step__card .btn-primary{border-color:#8d6f17;background:#9a7a1d;text-shadow:none}.talario-wizard__grid--two{grid-template-columns:minmax(0,1.5fr) minmax(220px,.7fr);margin-bottom:20px}.talario-group-card{margin:0 0 14px;padding:20px;border:1px solid #e2dccc;border-radius:10px;background:#fcfbf7}.talario-variation-list{display:flex;flex-direction:column;gap:10px;margin:14px 0}.talario-variation-card{display:flex;align-items:flex-end;gap:16px}.talario-variation-card__fields{display:flex;flex:1 1 390px;flex-wrap:wrap;gap:12px}.talario-variation-card__price{flex:0 0 130px}.talario-axis-options{display:flex;flex-wrap:wrap;gap:10px;margin-top:12px}.talario-axis-options label{display:inline-flex;align-items:center;gap:6px;margin:0;padding:8px 10px;border:1px solid #ded8cc;border-radius:7px;background:#fffdf8}.talario-variation-axis{margin:0 0 18px;padding:14px 16px;border:1px solid #e4ded1;border-radius:8px;background:#fbf8f0}.talario-schedule-slot{display:grid;grid-template-columns:minmax(170px,1.3fr) minmax(130px,.8fr) minmax(100px,.6fr) auto;align-items:end;gap:12px;padding:14px;border:1px solid #e6e0d4;border-radius:8px;background:#fff}.talario-preview-step__card{display:grid;grid-template-columns:auto minmax(0,1fr) auto;align-items:center;gap:16px;padding:24px;border:1px solid #e3dbc8;border-radius:10px;background:#fbf7eb}@media(max-width:980px){.talario-wizard__grid,.talario-wizard__grid--two{grid-template-columns:1fr}.talario-schedule-slot{grid-template-columns:1fr 1fr}}@media(max-width:600px){.talario-wizard__hero{align-items:flex-start;flex-direction:column}.talario-wizard__steps{overflow-x:auto;padding:8px}.talario-wizard__step{flex:0 0 auto}.talario-wizard__panel{padding:20px 16px}.talario-wizard__actions{margin:16px -16px 0;padding:12px 16px;background:#fff}.talario-variation-card{align-items:stretch;flex-direction:column}.talario-schedule-slot,.talario-preview-step__card{grid-template-columns:1fr}}
</style>{/literal}
{literal}<style>
.talario-photo-upload__frame{max-width:430px}.talario-photo-upload .file-uploader__files-container{width:100%}.talario-photo-upload .file-uploader__pickers{width:100%}.talario-photo-upload .file-uploader__file-square--no-files{box-sizing:border-box;width:100%;height:138px;min-height:138px;border:1px dashed #b8a66c;border-radius:10px;background:#fbf8ee}.talario-photo-upload .file-uploader__pickers-content{display:flex;flex-direction:column;align-items:flex-start;justify-content:center;height:100%;padding:0 24px}.talario-photo-upload .file-uploader__pickers-content p{display:none}.talario-photo-upload .file-uploader__pickers-buttons{margin:0}.talario-photo-upload .file-uploader__pickers-buttons-select{border-color:#9a7a1d;background:#fffdf8;color:#765d12;font-weight:700}.talario-photo-upload .file-uploader__file{width:132px}.talario-photo-upload .file-uploader__file-square{width:132px;height:132px}.talario-photo-upload .file-uploader__file-description-input{display:none}@media(max-width:600px){.talario-photo-upload__frame{max-width:none}.talario-photo-upload .file-uploader__file-square--no-files{height:118px;min-height:118px}}
</style>{/literal}
{literal}<style>
.talario-photo-upload__compact{display:flex;flex-direction:column;align-items:flex-start;gap:8px;box-sizing:border-box;max-width:430px;padding:20px 22px;border:1px dashed #b8a66c;border-radius:10px;background:#fbf8ee}.talario-photo-upload__compact strong{font-size:16px}.talario-photo-upload__compact>span{color:#817c74;font-size:13px;font-weight:400}.talario-photo-upload__compact .btn{margin:3px 8px 0 0}.talario-photo-upload__compact .btn:first-child{border-color:#9a7a1d;background:#fffdf8;color:#765d12;font-weight:700}.talario-photo-upload__manage{font-weight:400}.talario-photo-upload__native{max-width:430px;margin-top:12px}.talario-photo-upload__native.hidden{display:none}@media(max-width:600px){.talario-photo-upload__compact,.talario-photo-upload__native{max-width:none}.talario-photo-upload__manage{display:block;margin-top:10px!important}}
</style>{/literal}
{literal}<style>
/* Слой мастера рендерится в самом шаблоне: не зависит от собранного CSS. */
.talario-wizard{max-width:1280px!important;color:#2d3540!important;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Arial,sans-serif!important}.talario-wizard__hero h2,.talario-wizard__panel-heading h2{color:#2d3540!important;font-size:24px!important;letter-spacing:-.02em!important}.talario-wizard__eyebrow,.talario-wizard__panel-heading>span{color:#6b7890!important;font-size:11px!important}.talario-wizard__save-state,.talario-wizard__panel-heading p,.talario-field small,.talario-wizard__underhood{color:#818896!important}.talario-wizard__steps{padding:10px!important;border-color:#e8ebf1!important;box-shadow:0 3px 12px rgba(36,47,65,.04)!important}.talario-wizard__step{color:#6e7580!important}.talario-wizard__step>span{border-color:#d8dee7!important}.talario-wizard__step.is-active{color:#397abd!important;background:#edf3fb!important}.talario-wizard__step.is-active>span{border-color:#5b95d6!important;background:#5b95d6!important}.talario-wizard__panel{padding:24px!important;border-color:#e8ebf1!important;box-shadow:0 3px 12px rgba(36,47,65,.04)!important}.talario-field>span{color:#3d4652!important}.talario-field input,.talario-field select,.talario-field textarea,.talario-field__readonly{border-color:#dce2ea!important;background:#fff!important;color:#38414c!important}.talario-field__readonly{background:#f8fafc!important}.talario-axis-options label,.talario-variation-axis,.talario-group-card,.talario-variation-card{border-color:#e4e9f0!important;background:#fbfcfe!important}.talario-schedule-slot{border-color:#e4e9f0!important;background:#fff!important}.talario-preview-step__card{border-color:#e4e9f0!important;background:#fbfcfe!important}.talario-photo-upload .file-uploader__file-square--no-files,.talario-photo-upload__compact{border-color:#b9cfe7!important;background:#fbfdff!important}.talario-photo-upload .file-uploader__pickers-buttons-select,.talario-photo-upload__compact .btn:first-child{border-color:#dfe4eb!important;background:#fff!important;color:#397abd!important}.talario-wizard__actions{border-color:#e8ebf1!important}.talario-wizard__actions .btn-primary,.talario-preview-step__card .btn-primary{border:0!important;background:#f7c62f!important;color:#3f3210!important}
</style>{/literal}
{literal}<style>
.talario-wizard__actions{display:flex!important;justify-content:flex-end!important;align-items:center!important;gap:12px!important;margin-top:28px!important;padding:18px 0 0!important}.talario-wizard__actions .btn{flex:0 0 auto!important;min-width:104px!important;margin:0!important}.talario-wizard__actions .btn-primary{min-width:220px!important}.talario-photo-upload__compact{gap:10px!important;padding:18px!important}.talario-photo-upload__compact>div{display:flex!important;align-items:center!important;flex-wrap:wrap!important;gap:10px!important;margin-top:2px!important}.talario-photo-upload__compact .btn{margin:0!important;min-height:36px!important}.talario-photo-upload__compact .btn:first-child{border-color:#d7e2ee!important;background:#fff!important;color:#397abd!important;font-weight:600!important}.talario-photo-upload__compact .talario-photo-upload__manage{border-color:transparent!important;background:transparent!important;color:#6b7280!important;font-size:12px!important}.talario-photo-upload__compact .talario-photo-upload__manage:hover{background:#f5f7fa!important;color:#2d3540!important}@media(max-width:600px){.talario-wizard__actions{justify-content:stretch!important;flex-direction:column-reverse!important}.talario-wizard__actions .btn,.talario-wizard__actions .btn-primary{width:100%!important}.talario-photo-upload__compact>div{align-items:stretch!important;flex-direction:column!important}.talario-photo-upload__compact .btn{width:100%!important}}
</style>{/literal}
{literal}<style>
/* Neutral navigation for the class creation wizard. */
html body .cs-main-menu,
html body .cs-main-menu__header,
html body .cs-main-menu__outer,
html body .cs-main-menu__inner,
html body .cs-main-menu__primary,
html body .cs-main-menu__secondary,
html body .cs-main-menu .main-menu {
    background: #ffffff !important;
}
html body .cs-main-menu {
    border-right: 1px solid #e7eaee !important;
}
html body .cs-main-menu .main-menu-1__link,
html body .cs-main-menu .main-menu-2__link,
html body .cs-main-menu .main-menu-3__link {
    color: #3e4651 !important;
}
html body .cs-main-menu .main-menu-1__link--active,
html body .cs-main-menu .main-menu-1__toggle--active,
html body .cs-main-menu .main-menu-2__link--active,
html body .cs-main-menu .main-menu-3__link--active {
    background: #f3f4f6 !important;
    color: #2d3540 !important;
    box-shadow: none !important;
}
html body .cs-main-menu .main-menu-1__link:hover {
    background: #f8fafc !important;
}
</style>{/literal}

<div class="talario-cabinet talario-class-editor talario-wizard">
    {if $talario_class.talario_revision_comment}
        <div class="alert alert-warning"><strong>Занятие нужно доработать.</strong><br />Комментарий Talario: {$talario_class.talario_revision_comment}</div>
    {/if}

    <div class="talario-wizard__hero">
        <div><span class="talario-wizard__eyebrow">Добавление занятия</span><h2>Заполните данные без лишней переписки</h2></div>
        <div class="talario-wizard__save-state">
            {if $talario_class.product_id}<span class="talario-wizard__saved-dot"></span>Черновик сохранён{if $talario_class.timestamp} в {$talario_class.timestamp|date_format:"%H:%M"}{/if}{else}Сохранится после первого шага{/if}
        </div>
    </div>

    <nav class="talario-wizard__steps" aria-label="Шаги заполнения">
        {foreach [1 => "О занятии", 2 => "Группы и цены", 3 => "Расписание", 4 => "Предпросмотр"] as $step_number => $step_title}
            {if $talario_class.product_id}
                <a href="{"talario_classes.update?product_id=`$talario_class.product_id`&step=`$step_number`"|fn_url}" class="talario-wizard__step {if $talario_wizard_step == $step_number}is-active{elseif $talario_wizard_step > $step_number}is-complete{/if}"><span>{$step_number}</span><strong>{$step_title}</strong></a>
            {else}
                <span class="talario-wizard__step {if $step_number == 1}is-active{/if}"><span>{$step_number}</span><strong>{$step_title}</strong></span>
            {/if}
        {/foreach}
    </nav>

    {if $talario_wizard_step == 3 && $talario_class.product_id}
        <form action="{fn_url('talario_classes.save_schedules')}" method="post" name="talario_wizard_schedule_form">
            <input type="hidden" name="product_id" value="{$talario_class.product_id}" />
            <section class="talario-wizard__panel">
                <div class="talario-wizard__panel-heading"><span>Шаг 3 из 4</span><h2>Расписание</h2><p>У каждой группы может быть своё время. Можно добавить несколько занятий в один день.</p></div>
                {foreach $talario_schedule_groups as $schedule_group}
                    <article class="talario-group-card talario-schedule-group" data-product-id="{$schedule_group.product_id}">
                        <div class="talario-group-card__heading"><div><span class="talario-group-card__number">{$schedule_group@iteration}</span><h3>{$schedule_group.label}</h3></div></div>
                        <div class="talario-wizard__grid talario-wizard__grid--two">
                            <label class="talario-field"><span>Филиал</span><select name="schedules[{$schedule_group.product_id}][location_id]" required><option value="">Выберите филиал</option>{foreach $talario_class_locations as $location}<option value="{$location.location_id}" {if (int) $location.location_id === (int) $schedule_group.data.location_id}selected{/if}>{$location.name} — {$location.address}</option>{/foreach}</select></label>
                            <label class="talario-field"><span>Продолжительность, минут</span><input type="number" min="1" max="1440" step="1" name="schedules[{$schedule_group.product_id}][duration_minutes]" value="{$schedule_group.data.duration_minutes|default:45}" required /></label>
                        </div>
                        <div class="talario-schedule-slots" data-slot-list>
                            {foreach $schedule_group.data.slots as $slot}
                                <div class="talario-schedule-slot">
                                    <label class="talario-field"><span>День</span><select name="schedules[{$schedule_group.product_id}][slots][{$slot@iteration}][weekday]" required><option value="">Выберите день</option>{foreach $talario_weekdays as $weekday => $weekday_name}<option value="{$weekday}" {if (int) $slot.weekday === (int) $weekday}selected{/if}>{$weekday_name}</option>{/foreach}</select></label>
                                    <label class="talario-field"><span>Начало</span><input type="time" step="60" name="schedules[{$schedule_group.product_id}][slots][{$slot@iteration}][start_time]" value="{$slot.start_time}" required /></label>
                                    <label class="talario-field"><span>Мест</span><input type="number" min="1" max="10000" name="schedules[{$schedule_group.product_id}][slots][{$slot@iteration}][capacity]" value="{$slot.capacity}" required /></label>
                                    <button type="button" class="btn cm-talario-remove-slot">Удалить</button>
                                </div>
                            {/foreach}
                        </div>
                        <button type="button" class="btn talario-add-slot">+ Ещё день и время</button>
                    </article>
                {/foreach}
                <p class="talario-wizard__underhood">Период устанавливается автоматически: с сегодняшнего дня на один год.</p>
            </section>
            <div class="talario-wizard__actions"><a class="btn btn-large" href="{"talario_classes.update?product_id=`$talario_class.product_id`&step=2"|fn_url}">Назад</a><button class="btn btn-primary btn-large" type="submit">Сохранить и продолжить →</button></div>
        </form>
    {else}
        <form action="{""|fn_url}" method="post" enctype="multipart/form-data" name="talario_class_form">
            <input type="hidden" name="dispatch" value="talario_classes.save_class" />
            <input type="hidden" name="security_hash" value="{$security_hash}" />
            <input type="hidden" name="wizard_step" value="{$talario_wizard_step}" />
            {if $talario_class.product_id}<input type="hidden" name="product_id" value="{$talario_class.product_id}" />{/if}

            {if $talario_wizard_step == 1}
                <input type="hidden" name="wizard_next_step" value="2" /><input type="hidden" name="class_data[price]" value="{$talario_class.price|default:0}" />
                <section class="talario-wizard__panel">
                    <div class="talario-wizard__panel-heading"><span>Шаг 1 из 4</span><h2>Расскажите о занятии</h2><p>Здесь только общая информация. Цены и расписание заполняются дальше.</p></div>
                    <div class="talario-wizard__grid">
                        <label class="talario-field"><span>Название занятия</span><input type="text" name="class_data[product]" value="{$talario_class.product}" required /></label>
                        <label class="talario-field"><span>Категория</span><select name="class_data[category_id]" required><option value="">Выберите категорию</option>{foreach $talario_class_categories as $category}<option value="{$category.category_id}" {if $category.category_id == $talario_class_category_id}selected{/if}>{$category.category}</option>{/foreach}</select><small>По категории занятие попадёт в нужный раздел витрины.</small></label>
                        <label class="talario-field"><span>Где проходит занятие</span>{if $talario_class_locations|count == 1}{foreach $talario_class_locations as $location}<input type="hidden" name="class_data[location_id]" value="{$location.location_id}" /><div class="talario-field__readonly">{if $location.name}{$location.name} — {/if}{$location.address}</div>{/foreach}{else}<select name="class_data[location_id]" required>{foreach $talario_class_locations as $location}<option value="{$location.location_id}" {if $location.location_id == $talario_class_location_id}selected{/if}>{if $location.name}{$location.name} — {/if}{$location.address}</option>{/foreach}</select>{/if}<small>Адрес берётся из раздела «Центр».</small></label>
                        <label class="talario-field"><span>Коротко о занятии</span><textarea name="class_data[catalog_age]" rows="3">{$talario_class.short_description|strip_tags}</textarea><small>Короткая подпись для каталога: польза, формат или кому подходит.</small></label>
                        <label class="talario-field"><span>Описание</span><textarea name="class_data[full_description]" rows="10">{$talario_class.full_description}</textarea><small>Расскажите, чем занимаются дети, как проходит занятие и что важно знать перед записью.</small></label>
                        <label class="talario-field"><span>Ключевые слова для поиска</span><input type="text" name="class_data[meta_keywords]" value="{$talario_class.meta_keywords}" /><small>Например: биология, опыты, микроскоп. На витрине они не показываются.</small></label>
                        <div class="talario-field talario-photo-upload"><span>Фотографии</span><div class="talario-photo-upload__compact"><strong>Добавьте фотографии занятия</strong><span>Можно сделать это позже.</span><div><button type="button" class="btn btn-large" id="talario_add_photos">+ Добавить фотографии</button><button type="button" class="btn talario-photo-upload__manage" id="talario_manage_photos">Управлять фотографиями</button></div></div><div class="talario-photo-upload__native hidden">{include file="common/form_file_uploader.tpl" existing_pairs=(($talario_class.main_pair) ? [$talario_class.main_pair] : []) + $talario_class.image_pairs|default:[] file_name="file" image_pair_types=['N' => 'product_add_additional_image', 'M' => 'product_main_image', 'A' => 'product_additional_image'] image_object_id=$talario_class.product_id allow_update_files=true upload_file_text="Добавить фотографии"}</div><small>Главное фото будет видно в каталоге.</small></div>
                    </div>
                </section>
                <div class="talario-wizard__actions"><a class="btn btn-large" href="{"talario_classes.manage"|fn_url}">Отменить</a><button type="submit" name="save_action" value="draft" class="btn btn-primary btn-large">Сохранить и продолжить →</button></div>

            {elseif $talario_wizard_step == 2}
                <input type="hidden" name="wizard_next_step" value="3" /><input type="hidden" name="class_data[product]" value="{$talario_class.product}" /><input type="hidden" name="class_data[location_id]" value="{$talario_class_location_id}" /><input type="hidden" name="class_data[category_id]" value="{$talario_class_category_id}" /><input type="hidden" name="class_data[catalog_age]" value="{$talario_class.short_description|strip_tags}" /><textarea class="hidden" name="class_data[full_description]">{$talario_class.full_description}</textarea><input type="hidden" name="class_data[meta_keywords]" value="{$talario_class.meta_keywords}" />
                <section class="talario-wizard__panel">
                    <div class="talario-wizard__panel-heading"><span>Шаг 2 из 4</span><h2>Группы и цены</h2><p>Добавьте только реальные группы. Это может быть возраст, уровень подготовки, предмет или их сочетание.</p></div>
                    {if $talario_class.variations}<div class="talario-variation-list">{foreach $talario_class.variations as $variation}<article class="talario-group-card talario-variation-card"><div class="talario-variation-card__name"><span class="muted">Группа {$variation@iteration}</span><strong>{$variation.talario_label|default:$variation.product}</strong></div><label class="talario-field talario-variation-card__price"><span>Цена, ₽</span><input type="number" min="0" step="0.01" name="class_data[variation_prices][{$variation.product_id}]" value="{$variation.price}" required /></label><label class="checkbox talario-variation-card__delete"><input type="checkbox" name="class_data[delete_variations][]" value="{$variation.product_id}" /> Удалить</label></article>{/foreach}</div>{/if}
                    {if $talario_variation_axes}
                        {if !$talario_variation_group_id}<div class="talario-variation-axis"><strong>Чем отличаются группы?</strong><p>Можно выбрать несколько пунктов. Например, «Возраст» и «Уровень подготовки».</p><div class="talario-axis-options">{foreach $talario_variation_axes as $axis}<label><input type="checkbox" class="talario-axis-checkbox" value="{$axis.feature_id}" /> <span>{$axis.description|default:$axis.internal_name}</span></label>{/foreach}</div></div>{else}<p class="talario-variation-axis-summary"><strong>Группы отличаются по:</strong> {foreach $talario_variation_axes as $axis}{$axis.description|default:$axis.internal_name}{if !$axis@last}, {/if}{/foreach}</p>{/if}
                        <div id="talario_new_variations" class="talario-variation-list"></div>
                        <script type="text/x-talario-template" id="talario_variation_row_template"><article class="talario-group-card talario-variation-card talario-variation-card--new"><div class="talario-variation-card__fields">{foreach $talario_variation_axes as $axis}<label class="talario-field talario-variation-field{if !$talario_variation_group_id} hidden{/if}" data-axis-id="{$axis.feature_id}"><span>{$axis.description|default:$axis.internal_name}</span><select data-name="class_data[new_variations][__INDEX__][variants][{$axis.feature_id}]" required{if !$talario_variation_group_id} disabled{/if}><option value="">Выберите</option>{foreach $axis.variants as $variant}<option value="{$variant.variant_id}">{$variant.variant}</option>{/foreach}</select></label>{/foreach}</div><label class="talario-field talario-variation-card__price"><span>Цена, ₽</span><input data-name="class_data[new_variations][__INDEX__][price]" type="number" min="0" step="0.01" required /></label><button type="button" class="btn cm-talario-copy-variation">Скопировать</button><button type="button" class="btn cm-talario-remove-variation">Удалить</button></article></script>
                        <button type="button" class="btn btn-large talario-add-variation" id="talario_add_variation" {if !$talario_variation_group_id}disabled{/if}>+ Добавить группу</button>
                    {else}<div class="alert alert-warning">Для этой категории группы пока не настроены. Обратитесь в Talario.</div>{/if}
                    {if !$talario_class.variations}<label class="talario-field talario-base-price"><span>Цена, если группа одна, ₽</span><input type="number" min="0" step="0.01" name="class_data[price]" value="{$talario_class.price}" required /><small>Бесплатное занятие — 0 ₽. После добавления групп «цена от» рассчитается автоматически.</small></label>{else}<input type="hidden" name="class_data[price]" value="{$talario_class.price}" />{/if}
                </section>
                <div class="talario-wizard__actions"><a class="btn btn-large" href="{"talario_classes.update?product_id=`$talario_class.product_id`&step=1"|fn_url}">Назад</a><button type="submit" name="save_action" value="draft" class="btn btn-primary btn-large">Сохранить и продолжить →</button></div>

            {elseif $talario_wizard_step == 4}
                <input type="hidden" name="wizard_next_step" value="4" /><input type="hidden" name="class_data[product]" value="{$talario_class.product}" /><input type="hidden" name="class_data[location_id]" value="{$talario_class_location_id}" /><input type="hidden" name="class_data[category_id]" value="{$talario_class_category_id}" /><input type="hidden" name="class_data[price]" value="{$talario_class.price}" /><input type="hidden" name="class_data[catalog_age]" value="{$talario_class.short_description|strip_tags}" /><textarea class="hidden" name="class_data[full_description]">{$talario_class.full_description}</textarea><input type="hidden" name="class_data[meta_keywords]" value="{$talario_class.meta_keywords}" />{foreach $talario_class.variations as $variation}<input type="hidden" name="class_data[variation_prices][{$variation.product_id}]" value="{$variation.price}" />{/foreach}
                <section class="talario-wizard__panel talario-preview-step"><div class="talario-wizard__panel-heading"><span>Шаг 4 из 4</span><h2>Посмотрите занятие на витрине</h2><p>Откроется настоящая карточка Talario: с текущим шаблоном, вариантами, ценами, календарём и адресом.</p></div><div class="talario-preview-step__card"><div class="talario-preview-step__icon">✓</div><div><h3>{$talario_class.product}</h3><p>Все предыдущие шаги сохранены. Проверьте карточку и вернитесь сюда для отправки.</p></div><a class="btn btn-primary btn-large" target="_blank" rel="noopener" href="{"talario_classes.update?product_id=`$talario_class.product_id`&step=4&open_preview=1"|fn_url}">Открыть предпросмотр</a></div></section>
                <div class="talario-wizard__actions"><a class="btn btn-large" href="{"talario_classes.update?product_id=`$talario_class.product_id`&step=3"|fn_url}">Назад</a><button type="submit" name="save_action" value="submit" class="btn btn-primary btn-large">Отправить на проверку</button></div>
            {/if}
        </form>
    {/if}
</div>
<script>
(function (_, $) {
    var variationIndex = 0;
    function selectedAxes() { return $('.talario-axis-checkbox:checked').map(function () { return String(this.value); }).get(); }
    function prepareVariation($row) {
        var axes = selectedAxes();
        $row.find('.talario-variation-field').each(function () { var enabled = !$('.talario-axis-checkbox').length || axes.indexOf(String($(this).data('axis-id'))) !== -1; $(this).toggleClass('hidden', !enabled).find('select').prop('disabled', !enabled); });
        $row.find('[data-name]').each(function () { this.name = $(this).data('name'); }); return $row;
    }
    $('.talario-axis-checkbox').on('change', function () { $('#talario_add_variation').prop('disabled', !selectedAxes().length); $('#talario_new_variations').empty(); });
    $('#talario_add_variation').on('click', function () { var html = $('#talario_variation_row_template').html().replace(/__INDEX__/g, variationIndex++); $('#talario_new_variations').append(prepareVariation($(html))); });
    $(document).on('click', '.cm-talario-copy-variation', function () { var $source = $(this).closest('.talario-variation-card'); var html = $('#talario_variation_row_template').html().replace(/__INDEX__/g, variationIndex++); var $copy = prepareVariation($(html)); $source.find('select:enabled, input[type="number"]').each(function (index) { $copy.find('select:enabled, input[type="number"]').eq(index).val($(this).val()); }); $('#talario_new_variations').append($copy); });
    $(document).on('click', '.cm-talario-remove-variation', function () { $(this).closest('.talario-variation-card').remove(); });
    var slotIndex = 1000;
    $('.talario-add-slot').on('click', function () {
        var $group = $(this).closest('.talario-schedule-group'); var productId = String($group.data('product-id')); var prefix = 'schedules[' + productId + '][slots][' + (slotIndex++) + ']';
        var weekdays = '<option value="">Выберите день</option><option value="1">Понедельник</option><option value="2">Вторник</option><option value="3">Среда</option><option value="4">Четверг</option><option value="5">Пятница</option><option value="6">Суббота</option><option value="7">Воскресенье</option>';
        var html = '<div class="talario-schedule-slot"><label class="talario-field"><span>День</span><select name="' + prefix + '[weekday]" required>' + weekdays + '</select></label><label class="talario-field"><span>Начало</span><input type="time" step="60" name="' + prefix + '[start_time]" required></label><label class="talario-field"><span>Мест</span><input type="number" min="1" max="10000" name="' + prefix + '[capacity]" placeholder="8" required></label><button type="button" class="btn cm-talario-remove-slot">Удалить</button></div>';
        $group.find('[data-slot-list]').append(html);
    });
    $(document).on('click', '.cm-talario-remove-slot', function () { var $list = $(this).closest('[data-slot-list]'); if ($list.find('.talario-schedule-slot').length > 1) { $(this).closest('.talario-schedule-slot').remove(); } });
    $('#talario_add_photos').on('click', function () {
        var $native = $(this).closest('.talario-photo-upload').find('.talario-photo-upload__native');
        $native.removeClass('hidden');
        $native.find('[data-ca-fileupload-picker-local]').first().trigger('click');
    });
    $('#talario_manage_photos').on('click', function () {
        $(this).closest('.talario-photo-upload').find('.talario-photo-upload__native').toggleClass('hidden');
    });
    {if $talario_preview_url}window.location.replace({$talario_preview_url|json_encode nofilter});{/if}
}(Tygh, Tygh.$));
</script>
{/capture}
{include file="common/mainbox.tpl" title=$talario_class_page_title content=$smarty.capture.mainbox}
