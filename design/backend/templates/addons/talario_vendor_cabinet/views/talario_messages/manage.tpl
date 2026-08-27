{capture name="mainbox"}
<div class="talario-cabinet">
    <h3>Сообщения</h3>
    <p class="muted">Выберите, кому хотите написать.</p>
    <p>
        <a class="btn btn-primary" href="{"vendor_communication.threads?communication_type=vendor_to_admin"|fn_url}">Администратору Talario</a>
        <a class="btn" href="{"vendor_communication.threads?communication_type=vendor_to_customer"|fn_url}">Покупателям</a>
    </p>
</div>
{/capture}
{include file="common/mainbox.tpl" title=__("talario_vendor_cabinet.messages") content=$smarty.capture.mainbox}
