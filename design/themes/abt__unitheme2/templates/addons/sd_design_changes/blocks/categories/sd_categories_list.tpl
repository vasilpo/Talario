{if $items}
    <div class="sd-categories-block sd-categories-block--small-size">
        {foreach $items as $category}
            {if $category && (!$check_level|default:true || ($category.level|default:0) === 0)}
                {$href=$category|fn_form_dropdown_object_link:$block.type}
                {strip}
                {$background_url = $category.main_pair.detailed.image_path}
                {if $category.main_pair.detailed.is_high_res}
                    {$original_image_w = $category.main_pair.detailed.icon.image_x}
                    {$original_image_h = $category.main_pair.detailed.icon.image_y }
                    {$cropped = fn_image_to_display($category.main_pair, $original_image_w, $original_image_h)}

                    {$url_1 = "url({$cropped.image_path}) 1x"}
                    {$url_2 = "url({$category.main_pair.detailed.image_path}) 2x"}

                    {$background_url="{$cropped.image_path}'); background-image: image-set($url_1, $url_2);" scope="parent"}
                {/if}

                <a class="sd-categories-block__item"
                    href="
                        {if $href}
                            {$href}
                        {else}
                            {"categories.view?category_id={$category.category_id}"|fn_url}
                        {/if}
                    "
                >
                    <div class="sd-categories-block__item-image-wrapper"{if $background_url} style="background-image: url('{$background_url}');"{/if}>
                        <div class="sd-categories-block__item-name{if !$background_url} sd-categories-block__item-name--full-width{/if}">
                            {$category.category}
                        </div>
                    </div>
                </a>
                {/strip}
            {/if}
        {/foreach}
    </div>
{/if}