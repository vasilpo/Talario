{capture name="mainbox"}
{include file="addons/talario_vendor_cabinet/components/cabinet_style.tpl"}
<div class="talario-cabinet">
    <section class="talario-todo talario-message-card">
    <p class="muted">Выберите, кому хотите написать.</p>
    <p>
        <a class="btn btn-primary" href="{"vendor_communication.threads?communication_type=vendor_to_admin"|fn_url}">Команде Таларио</a>
        <a class="btn" href="{"vendor_communication.threads?communication_type=vendor_to_customer"|fn_url}">Покупателям</a>
    </p>
    </section>
</div>
{/capture}
{include file="common/mainbox.tpl" title="Сообщения" content=$smarty.capture.mainbox}
