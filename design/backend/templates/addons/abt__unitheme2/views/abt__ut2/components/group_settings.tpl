{strip}
{foreach $settings as $s}
{if $s.is_group and $s.is_group == 'Y' and $s.items}
{$meta = ""}
{$group_styles = "overflow: hidden;"}
{$group_meta = "collapsed in"}
{if $s.is_collapsed}
{$meta = "collapsed"}
{$group_meta = "collapsed collapse"}
{$group_styles = "overflow: hidden; height: 0px;"}
{/if}
{include file="common/subheader.tpl" title=__("`$ls`.`$section`.`$s@key`_group") target="#`$section`_`$s@key`_group" meta=$meta}
<div id="{"`$section`_`$s@key`_group"}" class="{$group_meta}" style="{$group_styles}">
<p>{__("`$ls`.`$section`.`$s@key`_group_description")}</p>
{include file="addons/abt__unitheme2/views/abt__ut2/components/types/simple_settings.tpl"
settings=$s.items f_group="`$s@key`."
}
{include file="addons/abt__unitheme2/views/abt__ut2/components/types/device_settings.tpl"
settings=$s.items f_group="`$s@key`." enable_position_fields=$s.enable_position_fields
}
</div>
{/if}
{/foreach}
{/strip}
