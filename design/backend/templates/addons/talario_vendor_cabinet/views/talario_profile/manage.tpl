{capture name="mainbox"}
{include file="addons/talario_vendor_cabinet/components/cabinet_style.tpl"}
<div class="talario-cabinet">
    <section class="talario-todo">
        <h2>Данные партнёра</h2>
        <p class="muted">Эти данные были получены при регистрации. Здесь они доступны для проверки, повторно заполнять их не нужно.</p>

        <div class="form-horizontal form-edit cm-hide-inputs">
            <div class="control-group">
                <label class="control-label">Юридическое название / ФИО:</label>
                <div class="controls"><input type="text" value="{$talario_company.company}" disabled="disabled" class="input-xlarge" /></div>
            </div>
            <div class="control-group">
                <label class="control-label">ИНН:</label>
                <div class="controls"><input type="text" value="{$talario_company.tax_number}" disabled="disabled" class="input-xlarge" /></div>
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
        </div>
        <p class="muted">Если регистрационные данные изменились, напишите администратору Таларио через раздел «Сообщения».</p>
    </section>
</div>
{/capture}
{include file="common/mainbox.tpl" title="Профиль" content=$smarty.capture.mainbox}
