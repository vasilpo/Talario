{capture name="mainbox"}
<div class="talario-cabinet">
    <div class="talario-stats">
        <a href="{"talario_classes.manage?talario_status=active"|fn_url}" class="talario-stat"><strong>{$talario_counts.active}</strong><span>Активные занятия</span></a>
        <a href="{"talario_classes.manage?talario_status=pending"|fn_url}" class="talario-stat"><strong>{$talario_counts.pending}</strong><span>На проверке</span></a>
        <a href="{"talario_classes.manage?talario_status=disabled"|fn_url}" class="talario-stat"><strong>{$talario_counts.disabled}</strong><span>Черновики / выключенные</span></a>
        <a href="{"ec_table_booking_system.booked_orders"|fn_url}" class="talario-stat"><strong>{$talario_counts.bookings}</strong><span>Бронирования за 30 дней</span></a>
    </div>

    <section class="talario-todo">
        <h2>Аналитика</h2>
        <div class="talario-stats">
            <a href="{"talario_finance.manage"|fn_url}" class="talario-stat">
                <strong>{include file="common/price.tpl" value=$talario_current_balance}</strong>
                <span>К выплате</span>
            </a>
            <div class="talario-stat">
                <strong>{include file="common/price.tpl" value=$talario_sales_30_days}</strong>
                <span>Оплачено клиентами за 30 дней</span>
            </div>
            <div class="talario-stat">
                <strong>{$talario_counts.bookings}</strong>
                <span>Оплаченные бронирования за 30 дней</span>
            </div>
        </div>
    </section>

    <section class="talario-todo">
        <h2>Последние бронирования</h2>
        {if $talario_recent_orders}
            <table class="table table-middle">
                <thead>
                    <tr>
                        <th>Бронирование</th>
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
            <p class="muted">Бронирований пока нет.</p>
        {/if}
    </section>
</div>
{/capture}
{include file="common/mainbox.tpl" title="Главная" content=$smarty.capture.mainbox}
