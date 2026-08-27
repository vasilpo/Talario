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
        <a href="{"ec_table_booking_system.booked_orders"|fn_url}" class="talario-stat"><strong>{$talario_counts.bookings}</strong><span>{__("talario_vendor_cabinet.new_bookings")}</span></a>
    </div>
    <section class="talario-todo">
        <h2>{__("talario_vendor_cabinet.todo")}</h2>
        {if $talario_counts.pending}
            <p>{__("talario_vendor_cabinet.todo_pending", [$talario_counts.pending])}</p>
        {elseif $talario_counts.disabled}
            <p>{__("talario_vendor_cabinet.todo_disabled", [$talario_counts.disabled])}</p>
        {else}
            <p>{__("talario_vendor_cabinet.todo_empty")}</p>
        {/if}
    </section>
</div>
{/capture}
{include file="common/mainbox.tpl" title=__("talario_vendor_cabinet.home") content=$smarty.capture.mainbox}
