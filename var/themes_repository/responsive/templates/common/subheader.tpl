{$subheader_tag = $subheader_tag|default:"h3"}
<{$subheader_tag} class="{$class|default:"ty-subheader"}">
    {$extra nofilter}
    {$title nofilter}

    {if $tooltip|trim}
        {include file="common/tooltip.tpl" tooltip=$tooltip params="ty-subheader__tooltip"}
    {/if}
</{$subheader_tag}>