{if "MULTIVENDOR"|fn_allowed_for && $store_mode != "plus" && $store_mode != "ultimate"}
    <div id="restriction_promo_dialog" class="restriction-promo">
        <p>{__("licensing.feature_not_allowed.message")}</p>
    </div>
{/if}
