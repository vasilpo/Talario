{capture name="mainbox"}
<div class="talario-cabinet">
    <h3>Уведомления</h3>
    <p class="muted">Здесь собраны системные уведомления Talario: новые бронирования, модерация занятий и важные изменения.</p>
    <p><strong>Всего уведомлений: {$talario_notifications_count}</strong></p>
    <p class="muted">Полный список пока доступен через значок колокольчика в верхней панели. На следующем этапе вынесем его сюда целиком.</p>
</div>
{/capture}
{include file="common/mainbox.tpl" title=__("talario_vendor_cabinet.notifications") content=$smarty.capture.mainbox}
