{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="cdn_form" class="form-horizontal form-edit">

{assign var="cdn_service" value=$cdn_data.service|default:"amazon"}

{include file="common/subheader.tpl" title=__("general") target="#acc_general"}
<div id="acc_general" class="collapsed in">
    <div class="control-group">
        <label for="elm_enable_cdn" class="control-label">{__("enable_cdn")}:</label>
        <div class="controls">
            <input type="hidden" name="cdn_data[is_enabled]" value="0" />
            <input type="checkbox" name="cdn_data[is_enabled]" id="elm_enable_cdn" value="1" {if $cdn_data.is_enabled}checked="checked"{/if} />
        </div>
    </div>

    {if $cdn_test_url}
        <div class="control-group">
            <div class="controls">
                <span class="shift-input">{__("text_cdn_check", ["[url]" => $cdn_test_url]) nofilter}</span>
            </div>
        </div>
    {/if}

    <div class="control-group">
        <label for="elm_cdn_service" class="control-label">{__("lt_yandex_cdn_static.service")}:</label>
        <div class="controls">
            <select name="cdn_data[service]" id="elm_cdn_service">
                <option value="amazon" {if $cdn_service == "amazon"}selected="selected"{/if}>{__("lt_yandex_cdn_static.service_amazon")}</option>
                <option value="yandex" {if $cdn_service == "yandex"}selected="selected"{/if}>{__("lt_yandex_cdn_static.service_yandex")}</option>
            </select>
        </div>
    </div>

    <div id="elm_cdn_amazon_settings"{if $cdn_service != "amazon"} class="hidden"{/if}>
        <div class="control-group">
            <label for="elm_cf_key" class="control-label">{__("key")}:</label>
            <div class="controls">
                <input type="text" name="cdn_data[key]" id="elm_cf_key" size="55" value="{$cdn_data.key}" class="input-large" />
                <p class="muted description">{__("tt_views_storage_cdn_key") nofilter}</p>
            </div>
        </div>

        <div class="control-group">
            <label for="elm_cf_secret" class="control-label">{__("secret_key")}:</label>
            <div class="controls">
                <input type="text" name="cdn_data[secret]" id="elm_cf_secret" size="55" value="{$cdn_data.secret}" class="input-large" />
            </div>
        </div>
    </div>

    <div id="elm_cdn_yandex_settings"{if $cdn_service != "yandex"} class="hidden"{/if}>
        <div class="control-group">
            <label for="elm_cdn_cname" class="control-label">{__("cname")}:</label>
            <div class="controls">
                <input type="text" name="cdn_data[cname]" id="elm_cdn_cname" size="55" value="{$cdn_data.cname}" class="input-large" />
                <p class="muted description">{__("lt_yandex_cdn_static.yandex_cname_description")}</p>
            </div>
        </div>

        <div class="control-group">
            <div class="controls">
                <div class="alert alert-info">
                    {__("lt_yandex_cdn_static.yandex_cdn_instruction") nofilter}
                </div>
            </div>
        </div>
    </div>
</div>

{capture name="buttons"}
    {include file="buttons/save_cancel.tpl" but_name="dispatch[storage.update_cdn]" but_role="submit-link" but_target_form="cdn_form" save=true hide_second_button=true but_meta="nav__actions-btn-save"}
{/capture}

</form>

{literal}
<script>
(function(_, $) {
    $.ceEvent('on', 'ce.commoninit', function() {
        var service_selector = $('#elm_cdn_service');

        if (!service_selector.length) {
            return;
        }

        var toggleServiceFields = function() {
            var is_yandex = service_selector.val() === 'yandex';

            $('#elm_cdn_amazon_settings').toggleClass('hidden', is_yandex);
            $('#elm_cdn_yandex_settings').toggleClass('hidden', !is_yandex);
        };

        service_selector.off('change.ltYandexCdnStatic').on('change.ltYandexCdnStatic', toggleServiceFields);
        toggleServiceFields();
    });
}(Tygh, Tygh.$));
</script>
{/literal}

{/capture}
{include file="common/mainbox.tpl" sidebar=$smarty.capture.sidebar title=__("cdn_settings") content=$smarty.capture.mainbox buttons=$smarty.capture.buttons}
