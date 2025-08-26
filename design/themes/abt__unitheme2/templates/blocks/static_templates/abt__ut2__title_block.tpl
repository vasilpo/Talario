{** block-description:tmpl_abt__ut2__title_block **}

{if $title || $smarty.capture.title|trim}
    <div class="ty-mainbox-container clearfix{if isset($hide_wrapper)} cm-hidden-wrapper{/if}{if $hide_wrapper} hidden{/if}{if $details_page} details-page{/if}{if $block.user_class} {$block.user_class}{/if}{if $content_alignment == "RIGHT"} ty-float-right{elseif $content_alignment == "LEFT"} ty-float-left{/if}">
        {hook name="wrapper:mainbox_general_title_wrapper"}
            <div class="ty-mainbox-title{if $block.properties.abt__ut_center_title === "YesNo::YES"|enum} ut2-center-title{/if}{if $block.properties.abt__ut_title_line_decoration === "YesNo::YES"|enum} ut2-title-line-decoration{/if}{if $block.properties.abt__ut_big_size_title === "big"} ut2-big-size-title{/if}{if $block.properties.abt__ut_big_size_title === "biggest"} ut2-biggest-size-title{/if}{if $block.properties.abt__ut_title_opacity === "YesNo::YES"|enum} ut2-title-opacity{/if}">
                {hook name="wrapper:mainbox_general_title"}
                    <span>
                        {if $smarty.capture.title|trim}
                            {$smarty.capture.title nofilter}
                        {else}
                            {$title nofilter}
                        {/if}
                    </span>
                {/hook}
            </div>
        {/hook}
    </div>
{/if}