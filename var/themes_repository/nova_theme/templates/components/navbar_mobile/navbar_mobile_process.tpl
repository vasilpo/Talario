{strip}

{$navbar_mobile_items_sorted = []}

{* Add items without insert_before_id and insert_after_id *}
{foreach $navbar_mobile_items as $key => $item}
    {if isset($item.insert_before_id) || isset($item.insert_after_id)}
        {continue}
    {/if}
    {$navbar_mobile_items_sorted[$key] = $item}
{/foreach}

{* Insert before ID *}
{foreach $navbar_mobile_items as $key => $item}
    {if isset($item.insert_before_id)}
        {$beforeId = $item.insert_before_id}
        {$inserted = false}

        {foreach $navbar_mobile_items_sorted as $sortedKey => $sortedItem}
            {if $sortedKey === $beforeId}
                {$navbar_mobile_items_sorted = array_merge(array_slice($navbar_mobile_items_sorted, 0, array_search($sortedKey, array_keys($navbar_mobile_items_sorted)), true),
                                                    [$key => $item],
                                                    array_slice($navbar_mobile_items_sorted, array_search($sortedKey, array_keys($navbar_mobile_items_sorted)), null, true))}
                {$inserted = true}
                {break}
            {/if}
        {/foreach}

        {if !$inserted}
            {$navbar_mobile_items_sorted[$key] = $item}
        {/if}
    {/if}
{/foreach}

{* Insert after ID *}
{foreach $navbar_mobile_items as $key => $item}
    {if isset($item.insert_after_id)}
        {$afterId = $item.insert_after_id}
        {$inserted = false}

        {foreach $navbar_mobile_items_sorted as $sortedKey => $sortedItem}
            {if $sortedKey === $afterId}
                {$index = array_search($sortedKey, array_keys($navbar_mobile_items_sorted)) + 1}
                {$navbar_mobile_items_sorted = array_merge(array_slice($navbar_mobile_items_sorted, 0, $index, true),
                                                [$key => $item],
                                                array_slice($navbar_mobile_items_sorted, $index, null, true))}
                {$inserted = true}
                {break}
            {/if}
        {/foreach}

        {if !$inserted}
            {$navbar_mobile_items_sorted[$key] = $item}
        {/if}
    {/if}
{/foreach}


{* Set navbar mobile items not controllers for homepage *}
{$navbar_mobile_items_controllers = []}
{foreach $navbar_mobile_items_sorted as $key => $item}
    {if $key !== 'homepage' && isset($item.controllers)}
        {$navbar_mobile_items_controllers = array_merge($navbar_mobile_items_controllers, $item.controllers)}
    {/if}
{/foreach}
{$navbar_mobile_items_controllers = array_unique($navbar_mobile_items_controllers)}
{$navbar_mobile_items_sorted.homepage.not_controllers = $navbar_mobile_items_controllers}

{* Export *}
{$navbar_mobile_items = $navbar_mobile_items_sorted scope=parent}
{/strip}