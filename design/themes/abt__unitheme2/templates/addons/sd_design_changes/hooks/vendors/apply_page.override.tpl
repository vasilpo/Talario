<!-- The "{$smarty.template}" template was overriden by the "sd_design_changes" add-on -->
{include "views/profiles/components/profiles_scripts.tpl"}
<div class="sd-company-apply-page">
    <div class="sd-company-apply-page__description">
        <h2 class="sd-company-apply-page__title">
            {__("sd_design_changes.how_we_work")}
        </h2>

        {function name="step" step_number="" title="" description=""}
            {if $title|trim != "" && $description|trim != ""}
                <div class="sd-company-apply-page-text">
                    <p class="sd-company-apply-page-text__step">{__("sd_design_changes.step")} {$step_number}.</p>
                    <p class="sd-company-apply-page-text__title">{$title}</p>
                    <p class="sd-company-apply-page-text__description">{$description}</p>
                </div>
            {/if}
        {/function}

        {step step_number=1
            title=__("sd_design_changes.step_title_1")
            description=__("sd_design_changes.step_text_1")
        }

        {step step_number=2
            title=__("sd_design_changes.step_title_2")
            description=__("sd_design_changes.step_text_2")
        }

        {step step_number=3
            title=__("sd_design_changes.step_title_3")
            description=__("sd_design_changes.step_text_3")
        }

        {step step_number=4
            title=__("sd_design_changes.step_title_4")
            description=__("sd_design_changes.step_text_4")
        }
    </div>
    <div class="sd-company-apply-page__fields" id="apply_for_vendor_account">
        <h2 class="sd-company-apply-page__title">
            {__("sd_design_changes.become_partner")}
        </h2>

        <form class="sd-fields" action="{"companies.apply_for_vendor"|fn_url}" method="post" name="apply_for_vendor_form" enctype="multipart/form-data">
            {if $invitation_key}
                <input type="hidden" name="company_data[invitation_key]" value="{$invitation_key}" />
            {/if}
            {hook name="vendors:apply_fields"}
                {include "views/profiles/components/profile_fields.tpl"
                    section="C"
                    nothing_extra="Y"
                    default_data_name="company_data"
                    profile_data=$company_data
                    sd_label_position="after"
                    sd_check_storefront=true
                }

                {hook name="vendors:apply_description"}
                {/hook}

                <input type="hidden" name="company_data[lang_code]" value="{$smarty.const.CART_LANGUAGE}" />
            {/hook}

            {include file="common/image_verification.tpl" option="apply_for_vendor_account" align="left"}

            <div class="sd-company-apply-page__button">
                {include "buttons/button.tpl"
                    but_text=__("sd_design_changes.request")
                    but_name="dispatch[companies.apply_for_vendor]"
                    but_id="but_apply_for_vendor"
                    but_meta="ty-btn__primary"
                }
            </div>
        </form>
    </div>
</div>