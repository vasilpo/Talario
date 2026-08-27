{capture name="mainbox"}
<div class="talario-cabinet">
    <p class="muted">Здесь будут доступны документы, принятые при регистрации партнёра.</p>
    <table class="table table-middle">
        <tbody>
            <tr><td><strong>Договор / оферта партнёра</strong></td><td class="right">Принято при регистрации</td></tr>
            <tr><td><strong>Лицензионное соглашение</strong></td><td class="right">Принято при регистрации</td></tr>
            <tr><td><strong>Требования к материалам</strong></td><td class="right">Принято при регистрации</td></tr>
            <tr><td><strong>Политика конфиденциальности</strong></td><td class="right">Принято при регистрации</td></tr>
        </tbody>
    </table>
    <p class="muted">Ссылки на конкретные версии документов подключим к данным регистрации, чтобы партнёр видел именно тот текст, который был принят.</p>
</div>
{/capture}
{include file="common/mainbox.tpl" title="Документы" content=$smarty.capture.mainbox}
