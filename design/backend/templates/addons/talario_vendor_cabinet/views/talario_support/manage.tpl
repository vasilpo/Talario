{capture name="mainbox"}
<div class="talario-cabinet">
    <div class="talario-dashboard__header">
        <div>
            <p class="muted">Напишите администратору Talario прямо из личного кабинета или отправьте письмо на partners@talario.ru.</p>
            <p>
                <a class="btn btn-primary" href="{"vendor_communication.threads?communication_type=vendor_to_admin"|fn_url}">Написать администратору</a>
                <a class="btn" href="mailto:partners@talario.ru">partners@talario.ru</a>
            </p>
        </div>
    </div>
</div>
{/capture}
{include file="common/mainbox.tpl" title="Поддержка" content=$smarty.capture.mainbox}
