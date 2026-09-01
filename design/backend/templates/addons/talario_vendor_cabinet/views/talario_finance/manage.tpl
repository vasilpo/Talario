{capture name="mainbox"}
{include file="addons/talario_vendor_cabinet/components/cabinet_style.tpl"}
<div class="talario-cabinet">
    <div class="talario-stats talario-stats--two">
        <div class="talario-stat">
            <strong>{include file="common/price.tpl" value=$talario_finance_to_pay}</strong>
            <span>К выплате</span>
        </div>
        <div class="talario-stat">
            <strong>{include file="common/price.tpl" value=$talario_finance_paid_total}</strong>
            <span>Выплачено</span>
        </div>
    </div>

    <section class="talario-todo">
        <h2>Оплаченные занятия</h2>
        <p class="muted">Здесь показано, сколько оплатил клиент, комиссия Таларио и сумма партнёру.</p>
        {if $talario_finance_orders}
            <div class="table-responsive-wrapper">
                <table class="table table-middle table-responsive">
                    <thead>
                        <tr>
                            <th>Заказ</th>
                            <th>Клиент</th>
                            <th>Дата</th>
                            <th class="right">Оплачено клиентом</th>
                            <th class="right">Комиссия Таларио</th>
                            <th class="right">Партнёру</th>
                        </tr>
                    </thead>
                    <tbody>
                    {foreach $talario_finance_orders as $order}
                        <tr>
                            <td data-th="Заказ"><a href="{"orders.details?order_id=`$order.order_id`"|fn_url}">#{$order.order_id}</a></td>
                            <td data-th="Клиент">{$order.firstname} {$order.lastname}</td>
                            <td data-th="Дата">{$order.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
                            <td class="right" data-th="Оплачено клиентом">{include file="common/price.tpl" value=$order.total}</td>
                            <td class="right" data-th="Комиссия Таларио">{include file="common/price.tpl" value=$order.commission_amount}</td>
                            <td class="right" data-th="Партнёру"><strong>{include file="common/price.tpl" value=$order.partner_amount}</strong></td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
        {else}
            <p class="muted">Оплаченных занятий пока нет.</p>
        {/if}
    </section>

    <section class="talario-todo">
        <h2>Выплаты от Таларио</h2>
        <p class="muted">История перечислений партнёру. Запрос на вывод средств не требуется.</p>
        {if $talario_finance_payouts}
            <div class="table-responsive-wrapper">
                <table class="table table-middle table-responsive">
                    <thead>
                        <tr>
                            <th>Дата</th>
                            <th>За период</th>
                            <th>Статус</th>
                            <th class="right">Сумма</th>
                        </tr>
                    </thead>
                    <tbody>
                    {foreach $talario_finance_payouts as $payout}
                        <tr>
                            <td data-th="Дата">{$payout.payout_date|date_format:$settings.Appearance.date_format}</td>
                            <td data-th="За период">
                                {$payout.start_date|date_format:$settings.Appearance.date_format} – {$payout.end_date|date_format:$settings.Appearance.date_format}
                                {if $payout.details_text}<div class="muted">{$payout.details_text}</div>{/if}
                            </td>
                            <td data-th="Статус">{$payout.status_text}</td>
                            <td class="right" data-th="Сумма"><strong>{include file="common/price.tpl" value=$payout.payout_amount}</strong></td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
        {else}
            <p class="muted">Выплат пока нет.</p>
        {/if}
    </section>
</div>
{/capture}
{include file="common/mainbox.tpl" title="Финансы" content=$smarty.capture.mainbox}
