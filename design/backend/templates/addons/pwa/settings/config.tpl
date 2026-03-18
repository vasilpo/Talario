{$current_storefront_id = $current_storefront_id|default:null}
{$main_domain = $main_domain|default:""}
{$current_name = $current_name|default:""}
{$active_name = $active_name|default:""}
{$current_url = $current_url|default:""}
{$subfolder = $subfolder|default:"example"}
{$relative_storefront_list_html = $relative_storefront_list_html|default:""}

{if $has_relatives_but_no_active_apps}
    {$status_confirm_on_message = __('pwa.manifest_status_confirm_warning_first_app_for_domain', ["[main_domain]" => $main_domain])}
    {$pwa_warning_message = __("pwa.warning_relative_storefronts_with_no_active_apps", ["[main_domain]" => $main_domain, "[items]" => $relative_storefront_list_html, "[subfolder]" => $subfolder, "[current_name]" => $current_name, "[current_url]" => $current_url])}
{else}
    {if $has_active_app_on_rel_storefront}
        {$pwa_warning_message = __("pwa.warning_relative_storefront_has_active_app", ["[main_domain]" => $main_domain, "[subfolder]" => $subfolder, "[current_name]" => $current_name, "[current_url]" => $current_url, "[active_name]" => $active_name, "[active_url]" => $active_url])}
    {/if}
    {$status_confirm_on_message = __("pwa.manifest_status_confirm_on_warning")}
{/if}

<div id="manifest_general" class="collapse in collapse-visible pwa-config">
    <div id="elm_status_warning_wrapper">
        {if $pwa_warning_message && $pwa.config.manifest_status !== "YesNo::YES"|enum}
            <div class="alert alert-warning">
                {$pwa_warning_message nofilter}
            </div>
        {/if}
    <!--elm_status_warning_wrapper--></div>
    <div class="control-group">
        <label for="elm_pwa_status" class="control-label">{__("pwa.manifest_status")}:</label>
        <div class="controls">
            <div
                data-ca-pwa-config="elmPwaStatusWrapper"
                data-ca-pwa-config-confirm-on-text="{$status_confirm_on_message}"
                data-ca-pwa-config-confirm-off-text="{__("pwa.manifest_status_confirm_off_warning")}"
                data-ca-pwa-config-submit-url="{"pwa.update_manifest_status?return_url=`$config.current_url|escape:'url'`"}"
                data-ca-pwa-config-result-ids="elm_status_warning_wrapper"
                data-ca-storefront-id={$current_storefront_id}
            >
                <input type="hidden" name="pwa[manifest_status]" value="N" />
                {include file="common/switcher.tpl"
                    checked=$pwa.config.manifest_status === "YesNo::YES"|enum
                    input_id="elm_pwa_status"
                    input_name="pwa[manifest_status]"
                    input_value="YesNo::YES"|enum
                }
            </div>
            <p class="muted description">{__("pwa.manifest_status_tooltip")}</p>
        </div>
    </div>
    <div class="control-group">
        <label for="elm_pwa_manifest_app_name" class="control-label cm-trim">{__("pwa.manifest_app_name")}:</label>
        <div class="controls">
            <input type="text" name="pwa[manifest_app_name]" id="elm_pwa_manifest_app_name" value="{$pwa.config.manifest_app_name}" class="input-small" />
            <p class="muted description">{__("pwa.manifest_app_name_tooltip")}</p>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label">{__("pwa.manifest_icon")}:</label>
        <div class="controls">
            {include file="common/attach_images.tpl"
                image_name="manifest_icon"
                image_object_type="manifest_icon"
                image_pair=$pwa.manifest_icon
                image_object_id=$id
                icon_text=__("pwa.manifest_icon")
                detailed_text=__("pwa.manifest_icon")
                no_thumbnail=true
                hide_alt=true
            }
            <p class="muted description">{__("pwa.manifest_icon_tooltip")}</p>
        </div>
    </div>
<!--manifest_general--></div>
