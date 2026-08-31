{capture name="mainbox"}
{literal}
<style>
.talario-home{max-width:1280px;color:#252b33;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Arial,sans-serif}.talario-page-heading{display:flex;align-items:flex-end;justify-content:space-between;gap:24px;margin:2px 0 22px}.talario-page-heading__eyebrow{display:block;margin-bottom:7px;color:#b58a13;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase}.talario-page-heading h1{margin:0;color:#252b33;font-size:29px;line-height:1.2}.talario-page-heading p{margin:6px 0 0;color:#818896;font-size:14px}.talario-button-primary{border:0!important;border-radius:8px!important;background:#f7c62f!important;color:#3f3210!important;font-weight:700;text-shadow:none!important;box-shadow:0 4px 10px rgba(196,150,20,.16)}.talario-metrics{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:18px}.talario-metric{display:flex;align-items:center;gap:14px;min-height:72px;padding:15px 17px;border:1px solid #e8ebf1;border-radius:12px;background:#fff;box-shadow:0 3px 12px rgba(36,47,65,.04);color:#252b33;text-decoration:none}.talario-metric i{display:flex;align-items:center;justify-content:center;flex:0 0 42px;width:42px;height:42px;border-radius:50%;font-size:22px;font-style:normal;font-weight:700}.talario-metric span{display:flex;flex-direction:column}.talario-metric strong{font-size:25px;line-height:1}.talario-metric small{margin-top:6px;color:#77808d;font-size:12px}.talario-metric--green i{background:#e7f6ec;color:#4caa6a}.talario-metric--blue i{background:#eaf3ff;color:#5091dd}.talario-metric--gold i{background:#fff4d9;color:#e0a415}.talario-metric--violet i{background:#f0ebff;color:#8465dc}.talario-home__grid{display:grid;grid-template-columns:minmax(0,1.52fr) minmax(310px,.82fr);gap:18px}.talario-home__grid--bottom{grid-template-columns:minmax(310px,.84fr) minmax(0,1.5fr);margin-top:18px}.talario-panel{padding:20px;border:1px solid #e8ebf1;border-radius:12px;background:#fff;box-shadow:0 3px 12px rgba(36,47,65,.04)}.talario-panel__heading{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:14px}.talario-panel__heading h2,.talario-promo-card h2{margin:0;color:#2a3038;font-size:18px;line-height:1.3}.talario-panel__heading p{margin:4px 0 0;color:#818896;font-size:12px}.talario-panel__heading>a{color:#5e95d5;font-size:12px;font-weight:600}.talario-checklist{margin:0;padding:0;list-style:none}.talario-checklist li{display:grid;grid-template-columns:36px minmax(0,1fr) auto;align-items:center;gap:11px;padding:13px 0;border-top:1px solid #eff1f4}.talario-checklist li:first-child{border-top:0}.talario-checklist strong{display:block;font-size:13px}.talario-checklist p{margin:3px 0 0;color:#88909b;font-size:12px}.talario-checklist .btn{min-width:82px;border-color:#e3e7ed;border-radius:7px;background:#fff;color:#4d5561;font-size:12px;font-weight:600;text-shadow:none}.talario-checklist__icon{display:flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:9px;font-size:17px;font-weight:700}.talario-checklist__icon--blue{background:#eaf3ff;color:#5591d8}.talario-checklist__icon--green{background:#e7f6ec;color:#4eaa6a}.talario-checklist__icon--gold{background:#fff3d7;color:#e5a614}.talario-promo-card{position:relative;overflow:hidden;padding:25px 27px;border:1px solid #f3e5bc;border-radius:12px;background:linear-gradient(135deg,#fffdf6,#fff6df);box-shadow:0 3px 12px rgba(110,82,15,.04)}.talario-promo-card h2{max-width:220px;font-size:20px}.talario-promo-card p{position:relative;z-index:1;max-width:290px;margin:12px 0 20px;color:#766d59;font-size:13px;line-height:1.55}.talario-promo-card .close{position:absolute;z-index:2;top:10px;right:12px;border:0;background:transparent;color:#9d978b;font-size:20px}.talario-promo-card__spark{position:absolute;right:34px;top:36px;color:#efbb23;font-size:32px}.talario-finance-summary{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}.talario-finance-summary>*{display:flex;flex-direction:column;min-height:76px;padding:13px;border-radius:9px;background:#f7f9fc;color:#2c333c;text-decoration:none}.talario-finance-summary small{color:#858d99;font-size:11px}.talario-finance-summary strong{margin-top:auto;font-size:17px}.talario-table{margin:0}.talario-table th{color:#88909b;font-size:11px}.talario-table td{color:#48515e;font-size:12px}.talario-empty{padding:20px 0 4px;color:#565f6c}.talario-empty p{margin:5px 0 0;color:#89919c;font-size:13px}@media(max-width:980px){.talario-metrics{grid-template-columns:repeat(2,1fr)}.talario-home__grid,.talario-home__grid--bottom{grid-template-columns:1fr}}@media(max-width:600px){.talario-page-heading{align-items:stretch;flex-direction:column}.talario-metrics{grid-template-columns:1fr}.talario-checklist li{grid-template-columns:36px minmax(0,1fr)}.talario-checklist .btn{grid-column:2}.talario-finance-summary{grid-template-columns:1fr}}
</style>
{/literal}
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
