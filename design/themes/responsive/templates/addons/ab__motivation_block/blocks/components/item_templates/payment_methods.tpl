{$payment_methods = fn_get_payments(['status' => "ObjectStatuses::ACTIVE"|enum, 'usergroup_ids' => $auth.usergroup_ids])}

{if $payment_methods}
    <div class="ty-wysiwyg-content{if $addons.ab__motivation_block.use_style_presets == {"YesNo::YES"|enum}} ab-mb-style-presets{/if}">
        <ul>
            {foreach $payment_methods as $payment}
                <li>
                    — {$payment.payment}{if $payment.description}<span{if !$runtime.customization_mode.live_editor} class="cm-tooltip"{/if} title="{$payment.description}">{include file="addons/ab__motivation_block/blocks/components/info_icon.tpl"}</span>{/if}
                </li>
            {/foreach}
        </ul>
    </div>
{/if}