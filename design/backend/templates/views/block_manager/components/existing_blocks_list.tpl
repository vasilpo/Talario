<div id="block_selection_themes_{$grid_id}{$extra_id}">
    {$tabs_count=count($theme_layout_block_map)}
    {if $tabs_count > 1}
        <div class="tabs cm-j-tabs tabs--enable-fill tabs--count-{$tabs_count}">
            <ul class="nav nav-tabs hidden">
                {foreach $theme_layout_block_map as $key => $data}
                    <li id="theme_layout_blocks_{$key}{$extra_id}" class="cm-js"></li>
                {/foreach}
            </ul>
            <select onchange="Tygh.$('#' + this.value).click();">
                {foreach $theme_layout_block_map as $key => $data}
                    <option value="theme_layout_blocks_{$key}{$extra_id}">{$data.name}</option>
                {/foreach}
            </select>
        </div>
    {/if}

    {foreach $theme_layout_block_map as $key => $data}
        {$blocks=$data.blocks}
        <div id="content_theme_layout_blocks_{$key}{$extra_id}">
            {foreach $blocks as $block}
                {if $block_types[$block.type]}
                    {include file="views/block_manager/components/existing_block.tpl"}
                {/if}
            {/foreach}
        <!--content_theme_layout_blocks_{$key}{$extra_id}--></div>
    {/foreach}
</div>
