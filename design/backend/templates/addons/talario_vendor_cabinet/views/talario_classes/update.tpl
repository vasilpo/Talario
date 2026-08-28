{capture name="mainbox"}
<div class="talario-cabinet talario-class-editor">
    <form action="{""|fn_url}" method="post" enctype="multipart/form-data" class="form-horizontal form-edit" name="talario_class_form">
        <input type="hidden" name="dispatch" value="talario_classes.save_class" />
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
                    <input type="text" id="elm_talario_class_name" name="class_data[product]" value="{$talario_class.product}" class="input-xxlarge" />
                </div>
            </div>

            <div class="control-group">
                <label class="control-label cm-required" for="elm_talario_class_location">Адрес занятия:</label>
                <div class="controls">
                    <select id="elm_talario_class_location" name="class_data[location_id]" class="input-xxlarge">
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
                    <select id="elm_talario_class_category" name="class_data[category_id]" class="input-xlarge">
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
                        <input type="text" id="elm_talario_class_price" name="class_data[price]" value="{$talario_class.price}" class="input-small" />
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
                    {include file="common/attach_images.tpl"
                        image_key="0"
                        image_name="product_main"
                        image_object_type="product"
                        image_object_id=$talario_class.product_id
                        image_pair=$talario_class.main_pair
                        image_type="M"
                        hide_titles=true
                    }
                    <p class="muted description">Добавьте основное фото, которое будет видно на витрине.</p>
                </div>
            </div>
        </section>

        <div class="buttons-container talario-class-editor__buttons">
            <a class="btn" href="{"talario_classes.manage"|fn_url}">Отменить</a>
            {if $talario_class.product_id}
                <a class="btn" href="{"talario_classes.schedule?product_id=`$talario_class.product_id`"|fn_url}">Расписание</a>
            {/if}
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </div>
    </form>
</div>
{/capture}
{include file="common/mainbox.tpl" title={if $talario_class.product_id}"Редактировать занятие"{else}"Новое занятие"{/if} content=$smarty.capture.mainbox}
