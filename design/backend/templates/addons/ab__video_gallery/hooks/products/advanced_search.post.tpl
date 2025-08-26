<div class="row-fluid">
<div class="group span6 ">
<a href="javascript:void(0)" id="sw_ab__vg_products_search" class="search-link cm-combination cm-save-state">
<span id="on_ab__vg_products_search" class="icon-caret-right{if $smarty.cookies.ab__vg_products_search} hidden{/if}"></span>
<span id="off_ab__vg_products_search" class="icon-caret-down{if !$smarty.cookies.ab__vg_products_search} hidden{/if}"></span>
{__('ab__video_gallery')}
</a>
<div class="form-horizontal{if !$smarty.cookies.ab__vg_products_search} hidden{/if}" id="ab__vg_products_search">
<div class="control-group" style="margin-top: 15px;">
<label for="ab__vg_products_with_video" class="control-label">{__("ab__vg.products_search.with_video")}</label>
<div class="controls">
<input type="hidden" name="ab__vg_products_with_video" value="N" />
<input type="checkbox" value="Y"{if $search.ab__vg_products_with_video == "Y"} checked="checked"{/if} name="ab__vg_products_with_video" id="ab__vg_products_with_video" />
</div>
</div>
</div>
</div>
</div>