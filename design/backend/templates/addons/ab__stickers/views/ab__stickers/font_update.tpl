{$allow_save = $font|fn_allow_save_object:"product_features"}
{strip}
{capture name="mainbox"}
<form action="{""|fn_url}" method="post" class="form-horizontal form-edit" name="ab__stickers_font_data_form" enctype="multipart/form-data">
{capture name="tabsbox"}
<div id="content_general">
<div class="control-group">
<label class="control-label" for="ab__stickers_img">
{__("ab__stcikers.pictogram.font_download")}
<a class="cm-tooltip" title="{__("ab__stcikers.pictogram.font_download.help")}">{include_ext file="common/icon.tpl" class="icon-question-sign"}</a>:
</label>
<div class="controls">
{include file="common/fileuploader.tpl" var_name="font[]" hide_server=true allowed_ext="ttf,zip"}
</div>
</div>
<!--content_general--></div>
{/capture}
{include file="common/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section track=true}
<div class="buttons-container">
{include file="buttons/save_cancel.tpl" but_name="dispatch[ab__stickers.font_update]" but_text=__("upload") cancel_action="close" hide_first_button=false}
</div>
</form>
{/capture}
{$smarty.capture.mainbox nofilter}
{/strip}