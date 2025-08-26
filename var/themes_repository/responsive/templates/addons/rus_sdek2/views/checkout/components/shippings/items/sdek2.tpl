<label for="office_{$group_key}_{$shipping_id}_{$store.code}"
       class="ty-one-store js-pickup-search-block-{$group_key} {if $old_office_id == $store.code || $store_count == 1}ty-sdek-office__selected{/if} "
>
    <input
        type="radio"
        name="select_office[{$group_key}][{$shipping_id}]"
        value="{$store.code}"
        {if $old_office_id == $store.code || $store_count == 1}
            checked="checked"
        {/if}
        class="cm-sdek-select-store ty-sdek-office__radio-{$group_key} ty-valign"
        id="office_{$group_key}_{$shipping_id}_{$store.code}"
        data-ca-pickup-select-office="true"
        data-ca-shipping-id="{$shipping_id}"
        data-ca-group-key="{$group_key}"
        data-ca-location-id="{$store.code}"
    />

    <div class="ty-sdek-store__label ty-one-store__label">
        <p class="ty-one-store__name">
            <span class="ty-one-store__name-text">{$store.code}, {$store.location.city}, {$store.location.address}</span>
        </p>

        <div class="ty-one-store__description">
            {if $store.location.address}
                <span class="ty-one-office__address">{$store.location.address}</span>
                <br />
            {/if}
            {if $store.work_time}
                <span class="ty-one-office__worktime">{$store.work_time}</span>
                <br />
            {/if}
            {if $store.nearest_station}
                <span class="ty-one-office__worktime">{__('lite_checkout.nearest_station')}: {$store.nearest_station}</span>
                <br />
            {/if}
            {if $store.phones.number}
                <span class="ty-one-office__worktime">{$store.phones.number}</span>
                <br />
            {/if}
        </div>
    </div>
</label>
