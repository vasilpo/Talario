{capture name="mainbox"}
<div class="talario-cabinet">
    <p class="muted">Выберите, кому хотите написать.</p>
    <p>
        <a class="btn btn-primary" href="{"vendor_communication.threads?communication_type=vendor_to_admin"|fn_url}">Команде Таларио</a>
        <a class="btn" href="{"vendor_communication.threads?communication_type=vendor_to_customer"|fn_url}">Покупателям</a>
    </p>
</div>
{/capture}
{include file="common/mainbox.tpl" title="Сообщения" content=$smarty.capture.mainbox}
