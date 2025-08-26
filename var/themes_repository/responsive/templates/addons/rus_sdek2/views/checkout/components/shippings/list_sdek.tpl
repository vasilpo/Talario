{$_max_desktop_items = $max_desktop_items|default:5}
{$sdek_map_container = "sdek_map_container_`$shipping.shipping_id`"}

<div class="ty-checkout-select-store__map-full-div pickup pickup--list">

    {* For mobiles; List wrapper with selected pickup item *}
    {foreach $shipping.data.offices as $store}
        {capture name="marker_content"}
            <div class="litecheckout-ya-baloon">
                <strong class="litecheckout-ya-baloon__store-name">{$store.code}, {$store.location.city}, {$store.location.address}</strong>

                {if $store.location.address}<p class="litecheckout-ya-baloon__store-address">{$store.location.address nofilter}</p>{/if}

                <p class="litecheckout-ya-baloon__select-row">
                    <a data-ca-shipping-id="{$shipping.shipping_id}"
                       data-ca-group-key="{$group_key}"
                       data-ca-location-id="{$store.code}"
                       data-ca-target-map-id="{$sdek_map_container}"
                       class="cm-sdek-select-location ty-btn ty-btn__primary text-button ty-width-full"
                    >{__("select")}</a>
                </p>

                {if $store.phones.number}<p class="litecheckout-ya-baloon__store-phone"><a href="tel:{$store.phones.number nofilter}">{$store.phones.number nofilter}</a></p>{/if}
                {if $store.work_time}<p class="litecheckout-ya-baloon__store-time">{$store.work_time nofilter}</p>{/if}
                {if $store.address_comment}<div class="litecheckout-ya-baloon__store-description">{$store.address_comment nofilter}</div>{/if}
            </div>
        {/capture}

        <div class="cm-rus-sdek-map-marker-{$shipping.shipping_id} hidden"
             data-ca-geo-map-marker-lat="{$store.location.latitude}"
             data-ca-geo-map-marker-lng="{$store.location.longitude}"
                {if $old_office_id == $store.code || $store_count == 1}
                    data-ca-geo-map-marker-selected="true"
                {/if}
        >{$smarty.capture.marker_content nofilter}</div>

        {if $old_office_id == $store.code}
        <div class="ty-checkout-select-store pickup__offices-wrapper visible-phone pickup__offices-wrapper--near-map">
            {* List *}
            <div class="litecheckout__fields-row litecheckout__fields-row--wrapped pickup__offices pickup__offices--list pickup__offices--list-no-height">
                {include file="addons/rus_sdek2/views/checkout/components/shippings/items/sdek2.tpl" store=$store}
            </div>
            {* End of List *}
        </div>
        {/if}
    {/foreach}
    {* For mobiles; List wrapper with selected pickup item *}

    {* For mobiles; button for popup with pickup points *}
    <button class="ty-btn ty-btn__secondary cm-open-pickups pickup__open-pickupups-btn visible-phone"
        data-ca-title="{__('lite_checkout.choose_from_list')}"
        data-ca-target="[data-ca-shipping='pickup_offices_wrapper_open_{$group_key}_{$shipping.shipping_id}']"
        type="button"
    >{__('lite_checkout.choose_from_list')}</button>
    <span class="visible-phone cm-open-pickups-msg"></span>
    {* For mobiles; button for popup with pickup points *}

    {* List wrapper *}
    <div data-ca-shipping="pickup_offices_wrapper_open_{$group_key}_{$shipping.shipping_id}" class="ty-checkout-select-store pickup__offices-wrapper hidden-phone">

        {* Search *}
        {if $store_count >= $_max_desktop_items}
        <div class="pickup__search">
            <div class="pickup__search-field litecheckout__field">
                <input type="text"
                       id="pickup-search"
                       class="litecheckout__input js-pickup-search-input"
                       placeholder=" "
                       value=""
                       data-ca-pickup-group-key="{$group_key}"
                />
                <label class="litecheckout__label" for="pickup-search">{__("storefront_search_label")}</label>
            </div>
        </div>
        {/if}
        {* End of Search *}

        {* List *}
        <label for="pickup_office_list_{$group_key}"
               class="cm-required cm-multiple-radios hidden"
               data-ca-validator-error-message="{__("pickup_point_not_selected")}"></label>
        <div class="litecheckout__fields-row litecheckout__fields-row--wrapped pickup__offices pickup__offices--list pickup__offices--list-{$group_key}"
             id="pickup_office_list_{$group_key}"
             data-ca-error-message-target-node-change-on-screen="xs,xs-large,sm"
             data-ca-error-message-target-node-after-mode="true"
             data-ca-error-message-target-node-on-screen=".cm-open-pickups-msg"
             data-ca-error-message-target-node=".pickup__offices--list-{$group_key}"
        >
            {foreach $shipping.data.offices as $store}
                {include file="addons/rus_sdek2/views/checkout/components/shippings/items/sdek2.tpl" store=$store}
            {/foreach}
        </div>
        {* End of List *}

    </div>
    {* End of List wrapper *}

</div>
