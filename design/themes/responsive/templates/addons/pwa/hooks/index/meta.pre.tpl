{if $pwa_is_valid}{strip}
    {$viewport_fit_cover = "viewport-fit=cover"}
    {$user_scalable_no = "user-scalable=no"}

    {if !$meta_data.name_viewport.content|strstr:$viewport_fit_cover}
        {$viewport_content = ", "|explode:$meta_data.name_viewport.content}
        {$viewport_content[] = $viewport_fit_cover}
        {$meta_data.name_viewport.content = ", "|implode:$viewport_content}
    {/if}

    {if !$meta_data.name_viewport.content|strstr:$user_scalable_no}
        {$viewport_content = ", "|explode:$meta_data.name_viewport.content}
        {$viewport_content[] = $user_scalable_no}
        {$meta_data.name_viewport.content = ", "|implode:$viewport_content}
    {/if}

    {* Export *}
    {$meta_data = $meta_data scope=parent}
{/strip}{/if}