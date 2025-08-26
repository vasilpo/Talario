{$rand = rand()}
{$id=$id|default:"elm_ab_cm_time"}
{$id="`$id`_`$rand`"}
<div class="ab__cb-time">
<label for="{$id}" class="cm-ab-cb-time"></label>
<input type="time" name="{$input_name}" id="{$id}" value="{if $time}{if $grinvich}{"H:i"|gmdate:$time}{else}{"H:i"|date:$time}{/if}{/if}" size="3"
class="input-small input-hidden{if $no_failed_msg} cm-no-failed-msg{/if}" placeholder="00:00"
data-ca-error-message-target-node="#{$id}_error_message_target"/>
</div>
<span class="ab-cb-time-error" id="{$id}_error_message_target"></span>
