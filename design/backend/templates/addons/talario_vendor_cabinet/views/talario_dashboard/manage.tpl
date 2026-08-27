{capture name="mainbox"}
<div class="talario-cabinet">
    <div class="talario-dashboard__header">
        <p class="muted">Управляйте центром, занятиями и бронированиями в одном месте.</p>
        {if $talario_has_branch}
            <a class="btn btn-primary btn-large" href="{"products.add"|fn_url}">+ Добавить занятие</a>
        {else}
            <a class="btn btn-primary btn-large" href="{"talario_locations.manage"|fn_url}">Настроить центр</a>
        {/if}
    </div>

    {if !$talario_onboarding_done}
        <section class="talario-todo">
            <h2>Настройте кабинет</h2>
            <p>✓ Регистрационные данные получены</p>
            <p>{if $talario_has_center_info}✓{else}1.{/if} <a href="{"talario_locations.manage"|fn_url}">Заполните информацию о центре</a></p>
            <p>{if $talario_has_branch}✓{else}2.{/if} <a href="{"talario_locations.manage"|fn_url}">Добавьте филиал</a></p>
            <p class="muted">После этого можно будет добавлять занятия и привязывать их к филиалам.</p>
        </section>
    {/if}

    <div class="talario-stats">
        <a href="{"talario_classes.manage?talario_status=active"|fn_url}" class="talario-stat"><strong>{$talario_counts.active}</strong><span>Активные занятия</span></a>
        <a href="{"talario_classes.manage?talario_status=pending"|fn_url}" class="talario-stat"><strong>{$talario_counts.pending}</strong><span>На проверке</span></a>
        <a href="{"talario_classes.manage?talario_status=disabled"|fn_url}" class="talario-stat"><strong>{$talario_counts.disabled}</strong><span>Черновики / выключенные</span></a>
        <a href="{"ec_table_booking_system.booked_orders"|fn_url}" class="talario-stat"><strong>{$talario_counts.bookings}</strong><span>Бронирования за 30 дней</span></a>
    </div>

    {if $talario_onboarding_done}
        <section class="talario-todo">
            <h2>Что нужно сделать</h2>
            {if $talario_counts.pending}
                <p>Дождаться проверки занятий: {$talario_counts.pending}</p>
            {elseif $talario_counts.disabled}
                <p>Проверить и опубликовать занятия: {$talario_counts.disabled}</p>
            {else}
                <p>Сейчас нет обязательных действий.</p>
            {/if}
        </section>
    {/if}

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
{include file="common/mainbox.tpl" title="Главная" content=$smarty.capture.mainbox}
