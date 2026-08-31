{capture name="mainbox"}
<div class="talario-cabinet talario-home">
    <header class="talario-page-heading">
        <div>
            <span class="talario-page-heading__eyebrow">Кабинет партнёра</span>
            <h1>Главная</h1>
            <p>Здесь собраны занятия, бронирования и важные задачи.</p>
        </div>
        <a class="btn btn-primary talario-button-primary" href="{"talario_classes.update"|fn_url}">+ Добавить занятие</a>
    </header>

    <div class="talario-metrics">
        <a href="{"talario_classes.manage?talario_status=active"|fn_url}" class="talario-metric talario-metric--green"><i>◷</i><span><strong>{$talario_counts.active}</strong><small>Активные занятия</small></span></a>
        <a href="{"talario_classes.manage?talario_status=pending"|fn_url}" class="talario-metric talario-metric--blue"><i>◌</i><span><strong>{$talario_counts.pending}</strong><small>На проверке</small></span></a>
        <a href="{"talario_classes.manage?talario_status=disabled"|fn_url}" class="talario-metric talario-metric--gold"><i>Ⅱ</i><span><strong>{$talario_counts.disabled}</strong><small>Черновики</small></span></a>
        <a href="{"ec_table_booking_system.booked_orders"|fn_url}" class="talario-metric talario-metric--violet"><i>♙</i><span><strong>{$talario_counts.bookings}</strong><small>Бронирования за 30 дней</small></span></a>
    </div>

    <div class="talario-home__grid">
        <section class="talario-panel talario-home__next">
            <div class="talario-panel__heading"><div><h2>Что стоит сделать</h2><p>Несколько шагов, которые помогут получить больше записей.</p></div><a href="{"talario_classes.manage"|fn_url}">Все занятия</a></div>
            <ul class="talario-checklist">
                <li><span class="talario-checklist__icon talario-checklist__icon--blue">▣</span><div><strong>Добавьте фотографии занятий</strong><p>Яркие фото помогают родителям сделать выбор.</p></div><a class="btn" href="{"talario_classes.manage"|fn_url}">Добавить</a></li>
                <li><span class="talario-checklist__icon talario-checklist__icon--green">☷</span><div><strong>Проверьте описания</strong><p>Расскажите, как проходит занятие и кому оно подходит.</p></div><a class="btn" href="{"talario_classes.manage"|fn_url}">Проверить</a></li>
                <li><span class="talario-checklist__icon talario-checklist__icon--gold">☆</span><div><strong>Следите за бронированиями</strong><p>Подтверждайте заявки вовремя, чтобы не терять клиентов.</p></div><a class="btn" href="{"ec_table_booking_system.booked_orders"|fn_url}">Открыть</a></li>
            </ul>
        </section>

        <aside class="talario-promo-card">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <span class="talario-promo-card__spark">✦</span>
            <h2>Сделайте занятия заметнее</h2>
            <p>Полная карточка с фото, расписанием и ценами помогает получать больше бронирований.</p>
            <a class="btn talario-button-primary" href="{"talario_classes.manage"|fn_url}">Посмотреть занятия</a>
        </aside>
    </div>

    <div class="talario-home__grid talario-home__grid--bottom">
        <section class="talario-panel">
            <div class="talario-panel__heading"><div><h2>Финансы за 30 дней</h2><p>Сводные показатели по оплатам и бронированиям.</p></div><a href="{"talario_finance.manage"|fn_url}">Все финансы</a></div>
            <div class="talario-finance-summary">
                <a href="{"talario_finance.manage"|fn_url}"><small>К выплате</small><strong>{include file="common/price.tpl" value=$talario_current_balance}</strong></a>
                <div><small>Оплачено клиентами</small><strong>{include file="common/price.tpl" value=$talario_sales_30_days}</strong></div>
                <div><small>Бронирований</small><strong>{$talario_counts.bookings}</strong></div>
            </div>
        </section>

        <section class="talario-panel talario-orders">
            <div class="talario-panel__heading"><div><h2>Последние бронирования</h2><p>Новые записи ваших клиентов.</p></div><a href="{"ec_table_booking_system.booked_orders"|fn_url}">Все бронирования</a></div>
        {if $talario_recent_orders}
            <table class="table table-middle talario-table">
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
            <div class="talario-empty"><strong>Бронирований пока нет</strong><p>Когда клиент запишется, информация появится здесь.</p></div>
        {/if}
    </section>
    </div>
</div>
{/capture}
{include file="common/mainbox.tpl" title="Главная" content=$smarty.capture.mainbox}
