{$id = 0}
{$name = ""}
{if $smarty.request.group_id}
{$id = $smarty.request.group_id}
{/if}
{if $smarty.request.group_name}
{$group_name = $smarty.request.group_name}
{/if}
<form action="{""|fn_url}"
method="post"
name="update_banner_group_form_{$id}"
class="{if $ajax_mode}cm-ajax {/if}form-horizontal form-edit cm-disable-empty-files {$hide_inputs_class}"
enctype="multipart/form-data"
{if $action_context}data-ca-ajax-done-event="ce.{$action_context}.banner_group_save"{/if}
>
<input type="hidden" class="cm-no-hide-input" name="banner_group_id" value="{$id}" />
{if !empty($smarty.request.return_url)}
<input type="hidden" class="cm-no-hide-input" name="return_url" value="{$smarty.request.return_url}" />
{/if}
<div class="control-group">
<label class="control-label" for="elm__group_name_{$id}">{__("title")}</label>
<div class="controls">
<input type="text" name="banner_group_name" value="{$group_name}" class="input-large" id="elm__group_name_{$id}" />
</div>
</div>
<div class="buttons-container">
{include file="buttons/save_cancel.tpl" but_name="dispatch[banners.update_group]" cancel_action="close" hide_second_button=$hide_first_button save=$id cancel_meta="bulkedit-unchanged"}
</div>
</form>