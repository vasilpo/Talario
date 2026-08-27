{capture name="mainbox"}
<div class="talario-cabinet">
    <div class="talario-dashboard__header">
        <div>
            <h3>Поддержка Talario</h3>
            <p class="muted">Напишите администратору Talario прямо из личного кабинета или отправьте письмо на partners@talario.ru.</p>
            <p>
                <a class="btn btn-primary" href="{"vendor_communication.threads?communication_type=vendor_to_admin"|fn_url}">Написать администратору</a>
                <a class="btn" href="mailto:partners@talario.ru">partners@talario.ru</a>
            </p>
        </div>
    </div>
</div>
{/capture}
{include file="common/mainbox.tpl" title=__("talario_vendor_cabinet.support") content=$smarty.capture.mainbox}
