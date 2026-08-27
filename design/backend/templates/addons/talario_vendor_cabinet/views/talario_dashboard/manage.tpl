{capture name="mainbox"}
<div class="talario-cabinet">
    <div class="talario-dashboard__header">
        <p class="muted">{__("talario_vendor_cabinet.dashboard_intro")}</p>
        <a class="btn btn-primary btn-large" href="{"products.add"|fn_url}">+ {__("talario_vendor_cabinet.add_class")}</a>
    </div>

    <div class="talario-stats">
        <a href="{"talario_classes.manage?talario_status=active"|fn_url}" class="talario-stat"><strong>{$talario_counts.active}</strong><span>{__("talario_vendor_cabinet.active_classes")}</span></a>
        <a href="{"talario_classes.manage?talario_status=pending"|fn_url}" class="talario-stat"><strong>{$talario_counts.pending}</strong><span>{__("talario_vendor_cabinet.pending")}</span></a>
        <a href="{"talario_classes.manage?talario_status=disabled"|fn_url}" class="talario-stat"><strong>{$talario_counts.disabled}</strong><span>{__("talario_vendor_cabinet.disabled_classes")}</span></a>
        <a href="{"ec_table_booking_system.booked_orders"|fn_url}" class="talario-stat"><strong>{$talario_counts.bookings}</strong><span>Бронирования за 30 дней</span></a>
    </div>

    <section class="talario-todo">
        <h2>{__("talario_vendor_cabinet.todo")}</h2>
        {if $talario_counts.pending}
            <p>Дождаться проверки занятий: {$talario_counts.pending}</p>
        {elseif $talario_counts.disabled}
            <p>Проверить и опубликовать занятия: {$talario_counts.disabled}</p>
        {else}
            <p>{__("talario_vendor_cabinet.todo_empty")}</p>
        {/if}
    </section>

    <section class="talario-todo">
        <h2>Аналитика</h2>
        <div class="talario-stats">
            <a href="{"companies.balance"|fn_url}" class="talario-stat">
                <strong>{include file="common/price.tpl" value=$talario_current_balance}</strong>
                <span>Текущий баланс</span>
            </a>
            <div class="talario-stat">
                <strong>{include file="common/price.tpl" value=$talario_sales_30_days}</strong>
                <span>Продажи за 30 дней</span>
            </div>
            <div class="talario-stat">
                <strong>{$talario_counts.bookings}</strong>
                <span>Оплаченные бронирования за 30 дней</span>
            </div>
        </div>
    </section>

    <section class="talario-todo">
        <h2>Последние заказы</h2>
        {if $talario_recent_orders}
            <table class="table table-middle">
                <thead>
                    <tr>
                        <th>Заказ</th>
                        <th>Клиент</th>
                        <th>Дата</th>
                        <th class="right">Сумма</th>
                    </tr>
                </thead>
                <tbody>
                {foreach $talario_recent_orders as $order}
                    <tr>
                        <td><a href="{"orders.details?order_id=`$order.order_id`"|fn_url}">#{$order.order_id}</a></td>
                        <td>{$order.firstname} {$order.lastname}</td>
                        <td>{$order.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
                        <td class="right">{include file="common/price.tpl" value=$order.total}</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        {else}
            <p class="muted">Заказов пока нет.</p>
        {/if}
    </section>
</div>
{/capture}
{include file="common/mainbox.tpl" title=__("talario_vendor_cabinet.home") content=$smarty.capture.mainbox}
