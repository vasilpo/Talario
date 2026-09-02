{capture name="mainbox"}
{include file="addons/talario_vendor_cabinet/components/white_shell.tpl"}
<div class="talario-cabinet">
    <section class="talario-todo">
    <p class="muted">Здесь будут доступны документы, принятые при регистрации партнёра.</p>
    <table class="table table-middle talario-document-list">
        <tbody>
            <tr><td><strong>Договор / оферта партнёра</strong></td><td class="right">Принято при регистрации</td></tr>
            <tr><td><strong>Лицензионное соглашение</strong></td><td class="right">Принято при регистрации</td></tr>
            <tr><td><strong>Требования к материалам</strong></td><td class="right">Принято при регистрации</td></tr>
            <tr><td><strong>Политика конфиденциальности</strong></td><td class="right">Принято при регистрации</td></tr>
        </tbody>
    </table>
    <p class="muted">Ссылки на конкретные версии документов подключим к данным регистрации, чтобы партнёр видел именно тот текст, который был принят.</p>
    </section>
</div>
{/capture}
{include file="common/mainbox.tpl" title="Документы" content=$smarty.capture.mainbox}
