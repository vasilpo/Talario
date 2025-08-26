{strip}
<div style="margin-bottom:10px">
<label for="{$prefix_md5}_include_subcats">{__("similar_subcats")}:</label>
<input type="hidden" name="{$prefix}[include_subcats]" value="{"YesNo::NO"|enum}">
<input id="{$prefix_md5}_include_subcats" title="{__("similar_subcats")}" type="checkbox" name="{$prefix}[include_subcats]" value="{"YesNo::YES"|enum}"{if $condition_data.include_subcats == "YesNo::YES"|enum} checked{/if}>
</div>
{/strip}