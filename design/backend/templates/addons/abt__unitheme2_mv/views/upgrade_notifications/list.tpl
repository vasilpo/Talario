<div class="abt-ut2-mv-upgrade-text">{__('abt__ut2_mv.upgrade_notifications.text', ['[ver]' => $ver]) nofilter}</div>
{if $notifications}
<div class="abt-ut2-mv-upgrade-notifications">
{foreach $notifications as $n}
<div class="abt-ut2-mv-upgrade-notification">
<div class="abt-ut2-mv-upgrade-notification-title">{$n.title nofilter}</div>
<div class="abt-ut2-mv-upgrade-notification-text">{$n.text nofilter}</div>
</div>
{/foreach}
</div>
{/if}
