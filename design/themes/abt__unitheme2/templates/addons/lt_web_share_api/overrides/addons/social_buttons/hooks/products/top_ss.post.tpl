{if $provider_settings && $settings.abt__ut2.products.addon_social_buttons.view[$settings.ab__device] === "YesNo::YES"|enum}
    <div class="ut2-pb__share">
        <button
            type="button"
            class="ut2-share-buttons-link label"
            data-ca-web-share="trigger"
        >
            <i class="ut2-icon-share"></i>
            <span>{__("abt__ut2.addon_social_buttons.share")}</span>
        </button>
    </div>
{/if}
