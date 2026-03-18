{if "ULTIMATE"|fn_allowed_for && $store_mode != "ultimate"}
    <div id="restriction_promo_dialog" class="restriction-promo restriction-promo--ult">
        <p>{__("licensing.feature_not_allowed.message")}</p>
    </div>
{/if}