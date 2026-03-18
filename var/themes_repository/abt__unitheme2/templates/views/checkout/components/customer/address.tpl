{$element_identifier = "address-group"}
{$group_meta = "hidden"}

{if $show_profiles_on_checkout}
    {$element_identifier = "user-profiles"}
    {$group_meta = ""}
{/if}

<div class="{$group_meta} litecheckout__group" data-ca-lite-checkout-element="{$element_identifier}" data-ca-address-position="{$settings.Checkout.address_position}">
    {if $auth.user_id && $allow_multiple_profiles}
        <div class="litecheckout__item litecheckout__item--center">
            <a
                    class="cm-dialog-opener cm-dialog-auto-size cm-dialog-destroy-on-close litecheckout__link"
                    href="{"checkout.update_profile"|fn_url}"
                    data-ca-target-id="create_user_profile"
                    data-ca-dialog-title="{__("create_profile")}"
            >{__("create_profile")}</a>
        </div>
    {/if}

    {if $show_profiles_on_checkout}
        {include file="views/checkout/components/user_profiles.tpl"}
    {else}
        {include
            file="views/checkout/components/profile_fields.tpl"
            profile_fields=$profile_fields
            section="ProfileFieldSections::SHIPPING_ADDRESS"|enum
            exclude=["s_city", "s_country", "s_state", "customer_notes"]
        }
    {/if}
</div>
