{$order_statuses = $smarty.const.STATUSES_ORDER|fn_get_simple_statuses:true}
{$selected_status = $search.status_filter|default:""}
{style src="addons/exikane_changes/styles.less"}
{if !$selected_status && $search.status}
    {if $search.status|@is_array}
        {foreach $search.status as $status_code}
            {$selected_status = $status_code}
            {break}
        {/foreach}
    {else}
        {$selected_status = $search.status}
    {/if}
{/if}

{$current_sort_by = $search.sort_by|default:"date"}
{$current_sort_order = $search.sort_order|default:"desc"}
{$selected_sort = $search.sort_token|default:"`$current_sort_by`_`$current_sort_order`"}
{$search_query = $search.query|default:""}

<div class="ty-bookings-page">
    <div class="ty-bookings-page__header">
        <h1 class="ty-bookings-page__title">{__("bookings.my_bookings")}</h1>
        <p class="ty-bookings-page__subtitle">{__("bookings.bookings_subtitle")}</p>
    </div>

    <form action="{"orders.search"|fn_url}" class="ty-bookings-filters" method="get" name="bookings_search_form">
        <label class="ty-bookings-filters__group" for="elm_bookings_status_filter">
            <span class="ty-bookings-filters__label">{__("status")}:</span>
            <select id="elm_bookings_status_filter" class="ty-bookings-filters__select" name="status_filter">
                <option value="">{__("all")}</option>
                {foreach $order_statuses as $status_code => $status_info}
                    {$status_title = $status_info}
                    {if $status_info|@is_array && $status_info.description}
                        {$status_title = $status_info.description}
                    {/if}
                    <option value="{$status_code}" {if $selected_status === $status_code}selected="selected"{/if}>{$status_title}</option>
                {/foreach}
            </select>
        </label>

        <label class="ty-bookings-filters__group" for="elm_bookings_sort_filter">
            <span class="ty-bookings-filters__label">{__("bookings.sorting")}:</span>
            <select id="elm_bookings_sort_filter" class="ty-bookings-filters__select" name="sort_token">
                <option value="date_asc" {if $selected_sort === "date_asc"}selected="selected"{/if}>{__("bookings.sort_nearest_first")}</option>
                <option value="date_desc" {if $selected_sort === "date_desc"}selected="selected"{/if}>{__("bookings.sort_latest_first")}</option>
                <option value="total_desc" {if $selected_sort === "total_desc"}selected="selected"{/if}>{__("bookings.sort_total_desc")}</option>
                <option value="total_asc" {if $selected_sort === "total_asc"}selected="selected"{/if}>{__("bookings.sort_total_asc")}</option>
                <option value="order_id_desc" {if $selected_sort === "order_id_desc"}selected="selected"{/if}>{__("bookings.sort_id_desc")}</option>
            </select>
        </label>

        <label class="ty-bookings-filters__search" for="elm_bookings_query">
            <input
                id="elm_bookings_query"
                class="ty-bookings-filters__input"
                name="query"
                type="text"
                value="{$search_query}"
                placeholder="{__("bookings.search_booking_placeholder")}" />
            <button
                class="ty-btn ty-btn__secondary ty-bookings-filters__submit"
                type="submit"
                name="dispatch[orders.search]"
            >{__("search")}</button>
        </label>
    </form>

    <div class="ty-bookings-list">
        {foreach from=$orders item="o"}
            <article class="ty-bookings-card">
                <div class="ty-bookings-card__main">
                    <h2 class="ty-bookings-card__title">
                        <a href="{"orders.details?order_id=`$o.order_id`"|fn_url}">{$o.booking_product_name}</a>
                    </h2>

                    {if $o.booking_address}
                        <div class="ty-bookings-card__meta-item ty-bookings-card__meta-item--address">
                            <i class="ut2-icon-location_on ty-bookings-card__meta-icon"></i>
                            <span>{$o.booking_address}</span>
                        </div>
                    {/if}

                    {if $o.booking_age}
                        <div class="ty-bookings-card__meta-item">
                            <i class="ut2-icon-outline-account-circle ty-bookings-card__meta-icon"></i>
                            <span class="ty-bookings-card__meta-label">{__("bookings.booking_age")}:</span>
                            <span>{$o.booking_age}</span>
                        </div>
                    {/if}
                </div>

                <div class="ty-bookings-card__details">
                    <div class="ty-bookings-card__date">{$o.timestamp|date_format:"%d %B %Y, %H:%M"}</div>

                    {if $o.booking_type_name}
                        <div class="ty-bookings-card__type">
                            <span class="ty-bookings-card__type-label">{__("bookings.booking_type")}:</span>
                            <span class="ty-bookings-card__type-value">{$o.booking_type_name}</span>
                        </div>
                    {/if}

                    {$status_title = $order_statuses[$o.status]|default:$o.status}
                    {if $status_title|@is_array && $status_title.description}
                        {$status_title = $status_title.description}
                    {/if}
                    <div class="ty-bookings-card__status">
                        <span class="ty-bookings-card__status-pill ty-bookings-card__status-pill--{$o.status|lower}">{$status_title}</span>
                    </div>

                    {hook name="orders:manage_data"}{/hook}

                    <div class="ty-bookings-card__totals">
                        <div class="ty-bookings-card__totals-row">
                            <span class="ty-bookings-card__totals-label">{__("bookings.booking_cost")}:</span>
                            <span class="ty-bookings-card__totals-value">{include file="common/price.tpl" value=$o.booking_products_total}</span>
                        </div>
                        <div class="ty-bookings-card__totals-row">
                            <span class="ty-bookings-card__totals-label ty-bookings-card__totals-label--accent">{__("bookings.points_spent")}:</span>
                            <span class="ty-bookings-card__totals-value ty-bookings-card__totals-value--accent">{include file="common/price.tpl" value=$o.booking_points_cost}</span>
                        </div>
                        <hr class="ty-bookings-card__totals-divider" />
                        <div class="ty-bookings-card__totals-row">
                            <span class="ty-bookings-card__totals-label">{__("bookings.total_paid")}:</span>
                            <span class="ty-bookings-card__totals-value">{include file="common/price.tpl" value=$o.booking_paid_total}</span>
                        </div>
                    </div>
                </div>

                <div class="ty-bookings-card__actions">
                    <a class="ty-btn ty-btn__primary" href="{"orders.details?order_id=`$o.order_id`"|fn_url}">{__("bookings.open_details")}</a>
                </div>
            </article>
        {foreachelse}
            <p class="ty-no-items">{__("text_no_orders")}</p>
        {/foreach}
    </div>

    {include file="common/pagination.tpl"}
</div>
