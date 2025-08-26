{*{include file="common/subheader.tpl" title=__("ab__vg.video_settings.additional") target="#ab__vg_video_settings_{$key}_{$type}"}*}
{*<div class="in collapse" id="ab__vg_video_settings_{$key}_{$type}">*}
{*<hr/>*}
{*<div class="control-group">*}
{*<label class="control-label" for="ab__vg_vimeo_add_portrait_{$key}">{__("ab__vg.vimeo.add_portrait")}:</label>*}
{*<div class="controls">*}
{*<input type="hidden" name="product_data[ab__vg_videos][{$key}][settings][vimeo][add_portrait]" value="{"YesNo::NO"|enum}">*}
{*<input id="ab__vg_vimeo_add_portrait_{$key}" type="checkbox" name="product_data[ab__vg_videos][{$key}][settings][vimeo][add_portrait]" value="{"YesNo::YES"|enum}"{if $video.settings.vimeo.add_portrait|default:{"YesNo::YES"|enum} == "YesNo::YES"|enum} checked{/if}>*}
{*<p class="description muted">{__("ab__vg.vimeo.add_portrait.description")}</p>*}
{*</div>*}
{*</div>*}
{*<div class="control-group">*}
{*<label class="control-label" for="ab__vg_vimeo_add_title_{$key}">{__("ab__vg.vimeo.add_title")}:</label>*}
{*<div class="controls">*}
{*<input type="hidden" name="product_data[ab__vg_videos][{$key}][settings][vimeo][add_title]" value="{"YesNo::NO"|enum}">*}
{*<input id="ab__vg_vimeo_add_title_{$key}" type="checkbox" name="product_data[ab__vg_videos][{$key}][settings][vimeo][add_title]" value="{"YesNo::YES"|enum}"{if $video.settings.vimeo.add_title|default:{"YesNo::YES"|enum} == "YesNo::YES"|enum} checked{/if}>*}
{*<p class="description muted">{__("ab__vg.vimeo.add_title.description")}</p>*}
{*</div>*}
{*</div>*}
{*<div class="control-group">*}
{*<label class="control-label" for="ab__vg_vimeo_add_byline_{$key}">{__("ab__vg.vimeo.add_byline")}:</label>*}
{*<div class="controls">*}
{*<input type="hidden" name="product_data[ab__vg_videos][{$key}][settings][vimeo][add_byline]" value="{"YesNo::NO"|enum}">*}
{*<input id="ab__vg_vimeo_add_byline_{$key}" type="checkbox" name="product_data[ab__vg_videos][{$key}][settings][vimeo][add_byline]" value="{"YesNo::YES"|enum}"{if $video.settings.vimeo.add_byline|default:{"YesNo::YES"|enum} == "YesNo::YES"|enum} checked{/if}>*}
{*<p class="description muted">{__("ab__vg.vimeo.add_byline.description")}</p>*}
{*</div>*}
{*</div>*}
{*</div>*}
