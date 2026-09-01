{capture name="mainbox"}
{include file="addons/talario_vendor_cabinet/components/cabinet_style.tpl"}
<div class="talario-cabinet">
    <p class="muted">Системные уведомления Таларио: бронирования, модерация занятий и важные изменения.</p>

    {if $talario_notifications}
        <div class="talario-notifications-list">
            {foreach $talario_notifications as $notification}
                <div class="talario-notification{if !$notification.is_read} talario-notification--unread{/if}">
                    <div class="talario-notification__content">
                        <strong>{$notification.title nofilter}</strong>
                        {if $notification.message}<div class="muted">{$notification.message nofilter}</div>{/if}
                        <div class="talario-notification__date">{$notification.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</div>
                    </div>
                    {if $notification.action_url}
                        <a class="btn" href="{$notification.action_url}">Открыть</a>
                    {/if}
                </div>
            {/foreach}
        </div>
    {else}
        <p class="no-items">Уведомлений пока нет.</p>
    {/if}
</div>
{/capture}
{include file="common/mainbox.tpl" title="Уведомления" content=$smarty.capture.mainbox}
