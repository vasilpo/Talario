{include file="common/subheader.tpl" title=__("ab__vg.video_settings.additional") target="#ab__vg_video_r_settings_{$key}_{$type}"}
<div class="in collapse" id="ab__vg_video_r_settings_{$key}_{$type}">
<hr/>
<div class="control-group">
<label class="control-label" for="ab__vg_video_attributes_{$key}">{__("ab__vg.video_attributes")}:</label>
<div class="controls">
<input id="ab__vg_video_attributes_{$key}" type="text" name="product_data[ab__vg_videos][{$key}][settings][iframe_attributes]" class="input-large" value="{$video.settings.iframe_attributes}">
<p class="description muted">{__("ab__vg.video_attributes.description")}
</div>
</div>
</div>