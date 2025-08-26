{if 'ab__video_gallery.view'|fn_check_view_permissions && $product_data.product_id}
{$product_data.ab__vg_videos = $product_data.product_id|fn_ab__vg_get_videos:[]:$smarty.const.DESCR_SL}
{$product_data.settings = $product_data.product_id|fn_ab__vg_get_setting}
<div class="hidden{if !'ab__video_gallery.manage'|fn_check_view_permissions} cm-hide-inputs{/if}" id="content_ab__video_gallery">
<p class="muted">{__("ab__vg.added_by_addon")}</p>
{if $addons.ab__video_gallery.enable_microdata == "YesNo::YES"|enum}
<div class="alert alert-warning"><strong>{__("warning")}.</strong>{__("ab__vg.json_ld.warning")}</div>
{/if}
{include file="common/subheader.tpl" title=__("ab__vg.form.product_settings") target="#ab__vg-product_settings"}
<div id="ab__vg-product_settings" class="in collapse {$no_hide_input_if_shared_product}">
<div class="control-group">
<label class="control-label" for="ab__vg__replace_image">{__("ab__vg.form.replace_image")}</label>
<div class="controls">
<input type="hidden" name="product_data[ab__vg_settings][replace_image]" value="N" />
<input type="checkbox" name="product_data[ab__vg_settings][replace_image]" id="ab__vg__replace_image" value="{"YesNo::YES"|enum}"{if $product_data.settings.replace_image == "YesNo::YES"|enum} checked{/if} />
</div>
</div>
</div>
{include file="common/subheader.tpl" title=__("ab__vg.form.product_videos") target="#ab__vg-product_videos"}
<div id="ab__vg-product_videos" class="in collapse">
<div class="table-responsive-wrapper">
<table class="table table-middle table--relative table-responsive" width="100%">
<thead class="cm-first-sibling">
<tr>
{hook name="ab__video_gallery:videos_list_head"}
<th width="2%">
<span id="on_video_extra" title="{__("expand_collapse_list")}" class="hand cm-combinations-video_extra hidden"><span class="exicon-expand"></span></span>
<span id="off_video_extra" title="{__("expand_collapse_list")}" class="hand cm-combinations-video_extra"><span class="exicon-collapse"></span></span>
</th>
<th width="5%">{__("ab__vg.form.pos")}</th>
<th width="10%">{__("ab__vg.form.product_pos")}</th>
<th width="35%">{__("ab__vg.form.title")}</th>
<th width="20%">{__("ab__vg.form.type")}</th>
<th width="20%"{if ($runtime.simple_ultimate || $runtime.storefronts_count <= 1)} class="hidden" {/if}>{__("storefront")}</th>
<th width="10%">{__("ab__vg.form.status")}</th>
{/hook}
<th width="15%">&nbsp;</th>
</tr>
</thead>
{foreach $product_data.ab__vg_videos as $video}
{$ind = $video@index}
{include file="addons/ab__video_gallery/views/ab__video_gallery/components/video_settings.tpl" key=$ind}
{/foreach}
{math equation="x+1" x=$ind|default:0 assign="new_key"}
{include file="addons/ab__video_gallery/views/ab__video_gallery/components/video_settings.tpl" key=$new_key new=true video=[]}
</table>
</div>
</div>
</div>
{script src="js/addons/ab__video_gallery/admin.js"}
{/if}