{capture name="mainbox"}
<div class="talario-cabinet">
    <p class="muted">Здесь собраны системные уведомления Talario: новые бронирования, модерация занятий и важные изменения.</p>
    <p><strong>Всего уведомлений: {$talario_notifications_count}</strong></p>
    <p class="muted">Полный список пока доступен через значок колокольчика в верхней панели.</p>
</div>
{/capture}
{include file="common/mainbox.tpl" title="Уведомления" content=$smarty.capture.mainbox}
