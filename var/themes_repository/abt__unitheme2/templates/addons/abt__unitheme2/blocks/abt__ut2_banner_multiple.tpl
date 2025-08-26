{* block-description:abt__ut2_banner_multiple *}

{strip}
    {if $block.properties.navigation == "L"}
        {$id="simple_products_scroller_{$block.snapping_id}"}
        {$elements_to_scroll = 1}
        {if $items}
            <div id="{$id}" class="banners ut2-scroll-container{if $block.properties.multiple_mode_items > 1} multiple{/if}" style="{if $block.properties.margin|trim}padding: {$block.properties.margin};{/if}{if $block.properties.minimal_width|trim}--ab-banner-min-width: {$block.properties.minimal_width|default: 300px};{/if}">
                <button class="ut2-scroll-left" type="button"><span class="ut2-icon-arrow_back_black"></span></button>
                <div class="ut2-scroll-content">
                    {foreach $items as $b}
                        {$b_iteration = $b@iteration}
                        {hook name="abt__ut2_banner:banners"}
                        {if $b.type == 'abt__ut2'}
                            {include file="addons/abt__unitheme2/blocks/components/abt__ut2_banner.tpl"}
                        {elseif $b.type == "G"}
                            <div class="ut2-banner">
                                {if $b.url}<a href="{$b.url|fn_url}"{if $b.target == "B"} target="_blank"{/if}>{/if}
                                    {include file="common/image.tpl" images=$b.main_pair image_auto_size=true}
                                    {if $b.url}</a>{/if}
                            </div>
                        {else}
                            <div class="ut2-banner ty-wysiwyg-content">
                                {$b.description nofilter}
                            </div>
                        {/if}
                        {/hook}
                    {/foreach}
                </div>
                <button class="ut2-scroll-right" type="button"><span class="ut2-icon-arrow_forward_black"></span></button>
            </div>
        {/if}
        {include file="common/simple_scroller_init.tpl" block_id=$id elements_to_scroll=$elements_to_scroll}
    {else}
    {if $items}
        <div id="banner_slider_{$block.snapping_id}" class="banners owl-carousel {if $block.properties.navigation == "L"}native-scroller{/if}{if $block.properties.navigation == "D"} owl-pagination-true {/if}{if $block.properties.multiple_mode_items > 1} multiple{/if}" style="{if $block.properties.margin|trim}padding: {$block.properties.margin};{/if}{if $block.properties.minimal_width|trim}--ab-banner-min-width: {$block.properties.minimal_width|default: 300px};{/if}">
            {foreach $items as $b}
                {$b_iteration = $b@iteration}
                {hook name="abt__ut2_banner:banners"}
                {if $b.type == 'abt__ut2'}
                    {include file="addons/abt__unitheme2/blocks/components/abt__ut2_banner.tpl"}
                {elseif $b.type == "G"}
                    <div class="ut2-banner">
                        {if $b.url}<a href="{$b.url|fn_url}"{if $b.target == "B"} target="_blank"{/if}>{/if}
                            {include file="common/image.tpl" images=$b.main_pair image_auto_size=true}
                            {if $b.url}</a>{/if}
                    </div>
                {else}
                    <div class="ut2-banner ty-wysiwyg-content">
                        {$b.description nofilter}
                    </div>
                {/if}
                {/hook}
            {/foreach}
        </div>
    {/if}

    <script>
        (function(_, $) {
            $.ceEvent('on', 'ce.commoninit', function(context) {
                var slider = context.find('#banner_slider_{$block.snapping_id}');
                if (slider.length) {

                    slider.owlCarousel({
                        direction: '{$language_direction}',
                        items: {$block.properties.multiple_mode_items},
                        singleItem: {if $block.properties.multiple_mode_items > 1}false{else}true{/if},
                        responsive: {if $block.properties.navigation == "L"}false{else}true{/if},
                        {if $block.properties.multiple_mode_items > 1}responsiveBaseWidth: slider,{/if}
                        slideSpeed: {$block.properties.speed|default:400},
                        autoPlay: {if $block.properties.navigation == "L"}false{else}{($block.properties.delay > 0) ? $block.properties.delay * 1000 : "false"}{/if},
                        stopOnHover: true,
                        beforeInit: function () {
                            $.ceEvent('trigger', 'ce.banner.carousel.beforeInit', [this]);
                        },
                        {if $block.properties.navigation == "N"}
                        pagination: false
                        {/if}
                        {if $block.properties.navigation == "D"}
                        pagination: true
                        {/if}
                        {if $block.properties.navigation == "P"}
                        pagination: true,
                        paginationNumbers: true
                        {/if}
                        {if $block.properties.navigation == "A"}
                        pagination: false,
                        navigation: true,
                        navigationText: ['<i class="ut2-icon-arrow_back_black"></i>', '<i class="ut2-icon-arrow_forward_black"></i>']
                        {/if}
                    });
                }
            });

        }(Tygh, Tygh.$));
    </script>
    {/if}
{/strip}