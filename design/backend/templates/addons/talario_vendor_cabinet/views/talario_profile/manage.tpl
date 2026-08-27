{capture name="mainbox"}
<div class="talario-cabinet">
    <section class="talario-todo">
        <h2>Данные партнёра</h2>
        <p class="muted">Эти данные были получены при регистрации. Здесь они доступны для проверки, повторно заполнять их не нужно.</p>

        <div class="form-horizontal form-edit cm-hide-inputs">
            <div class="control-group">
                <label class="control-label">Название / ФИО:</label>
                <div class="controls"><input type="text" value="{$talario_company.company}" disabled="disabled" class="input-xlarge" /></div>
            </div>
            <div class="control-group">
                <label class="control-label">E-mail:</label>
                <div class="controls"><input type="text" value="{$talario_company.email}" disabled="disabled" class="input-xlarge" /></div>
            </div>
            <div class="control-group">
                <label class="control-label">Телефон:</label>
                <div class="controls"><input type="text" value="{$talario_company.phone}" disabled="disabled" class="input-xlarge" /></div>
            </div>
            <div class="control-group">
                <label class="control-label">Адрес:</label>
                <div class="controls"><input type="text" value="{$talario_company.address}" disabled="disabled" class="input-xlarge" /></div>
            </div>
            <div class="control-group">
                <label class="control-label">Город:</label>
                <div class="controls"><input type="text" value="{$talario_company.city}" disabled="disabled" class="input-xlarge" /></div>
            </div>

            {include file="views/profiles/components/profile_fields.tpl"
                section="C"
                default_data_name="company_data"
                profile_data=$talario_company
                nothing_extra=true
                hide_inputs=true
            }
        </div>
        <p class="muted">Если регистрационные данные изменились, напишите администратору Talario.</p>
    </section>
</div>
{/capture}
{include file="common/mainbox.tpl" title="Профиль" content=$smarty.capture.mainbox}
