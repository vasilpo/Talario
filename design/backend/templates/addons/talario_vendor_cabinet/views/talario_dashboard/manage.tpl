{capture name="mainbox"}
{include file="addons/talario_vendor_cabinet/components/white_shell.tpl"}
<div class="talario-cabinet talario-home">
    <header class="talario-page-heading">
        <div><p>Добро пожаловать в кабинет партнёра.</p></div>
        <a class="btn btn-primary talario-button-primary" href="{"talario_classes.update"|fn_url}">+ Добавить занятие</a>
    </header>
    <div class="talario-metrics">
        <a href="{"talario_classes.manage?talario_status=active"|fn_url}" class="talario-metric talario-metric--green"><i>◷</i><span><strong>{$talario_counts.active}</strong><small>Активные занятия</small></span></a>
        <a href="{"talario_classes.manage?talario_status=pending"|fn_url}" class="talario-metric talario-metric--blue"><i>◌</i><span><strong>{$talario_counts.pending}</strong><small>На проверке</small></span></a>
        <a href="{"talario_classes.manage?talario_status=disabled"|fn_url}" class="talario-metric talario-metric--gold"><i>Ⅱ</i><span><strong>{$talario_counts.drafts}</strong><small>Черновики</small></span></a>
        <a href="{"ec_table_booking_system.booked_orders"|fn_url}" class="talario-metric talario-metric--violet"><i>♙</i><span><strong>{$talario_counts.bookings}</strong><small>Бронирования за 30 дней</small></span></a>
    </div>
    <div class="talario-home__grid talario-home__grid--bottom">
        <section class="talario-panel"><div class="talario-panel__heading"><div><h2>Финансы за 30 дней</h2><p>Сводные показатели по оплатам и бронированиям.</p></div><a href="{"talario_finance.manage"|fn_url}">Все финансы</a></div><div class="talario-finance-summary"><a href="{"talario_finance.manage"|fn_url}"><small>К выплате</small><strong>{include file="common/price.tpl" value=$talario_current_balance}</strong></a><div><small>Оплачено клиентами</small><strong>{include file="common/price.tpl" value=$talario_sales_30_days}</strong></div><div><small>Бронирований</small><strong>{$talario_counts.bookings}</strong></div></div></section>
        <section class="talario-panel talario-orders"><div class="talario-panel__heading"><div><h2>Последние бронирования</h2><p>Новые записи ваших клиентов.</p></div><a href="{"ec_table_booking_system.booked_orders"|fn_url}">Все бронирования</a></div>{if $talario_recent_orders}<table class="table table-middle talario-table"><thead><tr><th>Бронирование</th><th>Клиент</th><th>Дата</th><th class="right">Сумма</th></tr></thead><tbody>{foreach $talario_recent_orders as $order}<tr><td><a href="{"orders.details?order_id=`$order.order_id`"|fn_url}">#{$order.order_id}</a></td><td>{$order.firstname} {$order.lastname}</td><td>{$order.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td><td class="right">{include file="common/price.tpl" value=$order.total}</td></tr>{/foreach}</tbody></table>{else}<div class="talario-empty"><strong>Бронирований пока нет</strong><p>Когда клиент запишется, информация появится здесь.</p></div>{/if}</section>
    </div>
    {if $talario_attention_items}<section class="talario-attention" aria-labelledby="talario-attention-heading"><h2 id="talario-attention-heading">Требуют внимания</h2><div class="talario-attention__items">{foreach $talario_attention_items as $item}<a class="talario-attention__item" href="{$item.url}"><span class="talario-attention__icon" aria-hidden="true">Ⅱ</span><span class="talario-attention__content"><strong>{$item.title}</strong><small>{$item.description}</small></span><span class="talario-attention__action">{$item.action} →</span></a>{/foreach}</div></section>{/if}
</div>
{/capture}
{include file="common/mainbox.tpl" title="Главная" content=$smarty.capture.mainbox}
