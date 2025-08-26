{if $addon}
{$menu = fn_ab__am_get_addon_menu($addon, $active_href)}
{if $menu}
{capture name="adv_buttons"}
{$smarty.capture.adv_buttons nofilter}
{capture name="tools_list"}
{foreach $menu as $m}
<li{if $m.active == "Y"} class="active"{/if}>{btn type="list" text=__($m@key) href=$m.href data=$m.attrs}</li>
{/foreach}
{if $addon != 'ab__addons_manager'}
<li class="divider"></li>
<li>{btn type="list" target="_blank" text=__('ab__am.addons') href='ab__am.addons' class="ab__am"}</li>
{/if}
{if $cs_addons.ab__addons_manager.show_subscription == 'Y'}
{$channels = fn_ab__am_get_channels()}
<li class="divider"></li>
<li>
<div class="ab-am-subscription">
<span class="title">{__("ab__am.subscription")}</span>
<span class="channels">
{foreach $channels as $channel}
{if $channel == 'email'}
{$extra_href="?email={$user_info.email}&firstname={$user_info.firstname}&lastname={$user_info.lastname}"}
{/if}
<a target="_blank" href="{__("ab__am.subscription.channels.variant.{$channel}.href")}{$extra_href}" title="{__("ab__am.subscription.channels.variant.{$channel}.title")}"><i class="flaticon-{$channel}"></i></a>
{/foreach}
</span>
</div>
</li>
{/if}
{/capture}
{dropdown content=$smarty.capture.tools_list icon='ab__icon' class='ab__am-menu' }
{/capture}
{/if}
{/if}
