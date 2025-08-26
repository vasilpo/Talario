<div class="control-group">
    <label class="control-label" for="search_booking_product">{__("ec_booking.search_booking_product")}</label>
    <div class="controls">
        <input type="hidden" name="search_booking_product" value="N" />
        <input type="checkbox" value="Y"{if $search.search_booking_product == "Y" } checked="checked"{/if} name="search_booking_product"  id="search_booking_product" />
    </div>
</div>