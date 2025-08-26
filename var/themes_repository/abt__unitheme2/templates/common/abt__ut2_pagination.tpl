{$p=$search|fn_generate_pagination}
{$id="`$type`_pagination_contents"}

{if $position == 'top'}
    <div id="{$id}">
{elseif $position == 'bottom'}
    <!--{$id}--></div>

    {if $p.next_page > $p.current_page}
        {$load_more_mode = $settings.abt__ut2.load_more.mode[$settings.ab__device]}

        {if "AJAX_REQUEST"|defined && $load_more_mode === 'semi_auto'}
            {$load_more_mode = 'auto'}
        {/if}

        <div class="ut2-load-more-container ut2-load-more-{$load_more_mode}" id="load_more_{$id}">
            {$show_more=$p.items_per_page}
            {$left_products=$p.total_items-($p.items_per_page*$p.current_page)}
            {assign var="c_url" value=$config.current_url|fn_query_remove:'page'}
            {$lang_variable = "abt__ut2.load_more.show_more.clear.`$object`"}

            {if $left_products < $p.items_per_page}
                {$show_more=$left_products}
            {/if}

            {if $settings.abt__ut2.load_more.show_products_num[$settings.ab__device] === 'YesNo::YES'|enum}
                {$lang_variable = "abt__ut2.load_more.show_more.num.`$object`"}
            {/if}

            <span class="ut2-load-more" data-ut2-load-more-url="{"`$c_url`&page=`$p.next_page``$extra_url`"|fn_url}" data-ut2-load-more-result-ids="{"`$type`_pagination_contents"},load_more_{$type}_pagination_contents,ut2_pagination_block,ut2_pagination_block_bottom">
                <span class="loader-wrapper">
                    <i class="loader"></i>
                </span>
                <span class="loader-text">{__($lang_variable, [$show_more])}</span>
            </span>
        <!--load_more_{$id}--></div>
    {/if}
{/if}
