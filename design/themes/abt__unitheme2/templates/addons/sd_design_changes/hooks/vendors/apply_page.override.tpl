<!-- The "{$smarty.template}" template was overriden by the "sd_design_changes" add-on -->
{include "views/profiles/components/profiles_scripts.tpl"}
<div class="sd-company-apply-page">
    <div class="sd-company-apply-page__description">
        <h2 class="sd-company-apply-page__title">
            Как мы работаем
        </h2>

        <div class="sd-company-apply-page-text">
            <p class="sd-company-apply-page-text__step">
                ШАГ 1.
            </p>

            <p class="sd-company-apply-page-text__title">
                Подайте заявку
            </p>

            <p class="sd-company-apply-page-text__description">
                Заполните форму на сайте — укажите название организации и контакты. Это займет 1 минуту
            </p>
        </div>
    </div>
    <div class="sd-company-apply-page__fields" id="apply_for_vendor_account">
        <h2 class="sd-company-apply-page__title">
            Стать партнером
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