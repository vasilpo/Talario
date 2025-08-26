
<form action="{""|fn_url}" class="cm-ajax cm-post cm-ajax-full-render" id="banner-preview">
    <div id="banner-panel">
        <div class="span3 {if $banner_device == 'mobile'}hidden{/if}">
            <input type="hidden" name="result_ids" value="preview{$banner.banner_id}" >
            <input type="hidden" name="banner_id" value="{$banner.banner_id}">
            <input type="hidden" name="device" value="{$banner_device}" >
            <input type="hidden" name="dispatch" value="banners.preview" >
            <input type="hidden" name="return_url" value="{$smarty.request.return_url|default:$config.current_url}"/>

            <label for="banner-width"><span style="display: block;width: 24px;height: 24px;"><svg class="cs-icon__svg" focusable="false" aria-hidden="true" viewBox="0 0 24 24" fill="currentColor"><path d="M 4 20 C 3.45 20 2.979167 19.804167 2.5875 19.4125 C 2.195833 19.020834 2 18.550001 2 18 L 2 6 C 2 5.449999 2.195833 4.979166 2.5875 4.5875 C 2.979167 4.195833 3.45 4 4 4 L 20 4 C 20.550001 4 21.020834 4.195833 21.4125 4.5875 C 21.804167 4.979166 22 5.449999 22 6 L 22 18 C 22 18.550001 21.804167 19.020834 21.4125 19.4125 C 21.020834 19.804167 20.550001 20 20 20 L 4 20 Z M 4 18 L 6 18 L 6 6 L 4 6 L 4 18 Z M 8 18 L 16 18 L 16 6 L 8 6 L 8 18 Z M 18 18 L 20 18 L 20 6 L 18 6 L 18 18 Z M 8 6 L 8 18 L 8 6 Z"/></svg></span>{__("abt__ut2.banner.preview.width_section")}</label>
            <select id="banner-width" name="width" form="banner-preview">
                {for $i=4 to 16}
                    <option value="{$i}" {if $i == $banner_width}selected{/if} label="{$i}"></option>
                {/for}
                <input type="submit" class="hidden cm-skip-validation">
            </select>
        </div>
        <div class="span3">
            <label for="banner-height"><span style="display: block;width: 24px;height: 24px;"><svg class="cs-icon__svg" focusable="false" aria-hidden="true" viewBox="0 0 24 24" fill="currentColor"><path d="M 6 22 C 5.45 22 4.979167 21.804167 4.5875 21.4125 C 4.195833 21.020834 4 20.550001 4 20 L 4 4 C 4 3.449999 4.195833 2.979166 4.5875 2.5875 C 4.979167 2.195833 5.45 2 6 2 L 18 2 C 18.550001 2 19.020834 2.195833 19.4125 2.5875 C 19.804167 2.979166 20 3.449999 20 4 L 20 20 C 20 20.550001 19.804167 21.020834 19.4125 21.4125 C 19.020834 21.804167 18.550001 22 18 22 L 6 22 Z M 6 20 L 18 20 L 18 4 L 6 4 L 6 20 Z M 7 18 L 17 18 L 13.55 13.5 L 11.25 16.5 L 9.7 14.5 L 7 18 Z M 6 20 L 6 4 L 6 20 Z"/></span>{__("abt__ut2.banner.preview.height_banner")}</label>
            <input id="banner-height" type="number" value="{$banner_height}" placeholder="Height in px" name="height" form="banner-preview"/>
        </div>
        <div class="span3">
            <label for="banner-device"><span style="display: block;width: 24px;height: 24px;"><svg class="cs-icon__svg" focusable="false" aria-hidden="true" viewBox="0 0 20 20" fill="currentColor"><path d="m3.33464 3.25c-.50627 0-.91667.41041-.91667.91667v8.33333c0 .5063.4104.9167.91667.9167h13.33336c.5062 0 .9166-.4104.9166-.9167v-8.33333c0-.50626-.4104-.91667-.9166-.91667zm-2.416671.91667c0-1.33469 1.081981-2.41667 2.416671-2.41667h13.33336c1.3347 0 2.4166 1.08198 2.4166 2.41667v8.33333c0 1.3347-1.0819 2.4167-2.4166 2.4167h-5.9175v1.8333h2.5841c.4142 0 .75.3358.75.75s-.3358.75-.75.75h-3.2988c-.0117.0005-.0235.0008-.0353.0008-.01183 0-.02359-.0003-.03528-.0008h-3.29725c-.41421 0-.75-.3358-.75-.75s.33579-.75.75-.75h2.58252v-1.8333h-5.91585c-1.33469 0-2.416671-1.082-2.416671-2.4167z"></path></svg></span>{__("abt__ut2.banner.preview.devices")}</label>
            <select id="banner-device" form="banner-preview" onchange="location = this.value;">
                <option value="index.php?dispatch=banners.preview&banner_id={$banner.banner_id}&device=desktop" {if $banner_device == 'desktop'}selected{/if} label="{__("block_manager.availability.desktop")}"></option>
                <option value="index.php?dispatch=banners.preview&banner_id={$banner.banner_id}&device=tablet" {if $banner_device == 'tablet'}selected{/if} label="{__("block_manager.availability.tablet")}"></option>
                <option value="index.php?dispatch=banners.preview&banner_id={$banner.banner_id}&device=mobile" {if $banner_device == 'mobile'}selected{/if} label="{__("block_manager.availability.phone")}"></option>
                <input type="submit" class="hidden cm-skip-validation">
            </select>
        </div>
        {if $banner_device == 'mobile'}
            <div class="span3" style="gap:0">
                <input type="hidden" name="hr_device_orientation_landscape" value="N" form="banner-preview"/>
                <input id="hr-device-orientation" type="checkbox" value="Y" name="hr_device_orientation_landscape" {if $hr_device_orientation_landscape == "Y"} checked="checked" {/if}form="banner-preview"/>
                <label>{__("abt__ut2.banner.preview.landscape_orientation")}</label>
            </div>
        {/if}
    </div>
</form>
<br>

{assign 'block' $banner_block|array_merge_recursive:$block}

<div id="preview{$banner.banner_id}">
    <div class="span{$banner_width|default:16} preview_{$banner_device} {if $hr_device_orientation_landscape == 'Y'}landscape{/if}">
        {include file="addons/abt__unitheme2/blocks/components/abt__ut2_banner.tpl" block=$block b=$banner}
    </div>
    <!--preview{$banner.banner_id}--></div>

<script>
    (function (_, $) {
        $('#banner-preview input').on('input', $.debounce(function () {
            $('#banner-preview').submit();
        }))
        $('#banner-preview select').on('change', $.debounce(function () {
            $('#banner-preview').submit();
        }))
    }(Tygh, Tygh.$));
</script>