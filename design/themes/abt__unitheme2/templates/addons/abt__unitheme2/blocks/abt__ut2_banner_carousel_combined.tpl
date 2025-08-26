{* block-description:abt__ut2__banner_carousel_combined *}

{strip}
    {if $items}
        <div id="banner_slider_{$block.snapping_id}" class="banners owl-carousel {if $block.properties.navigation == "D" && $items|count > 1 || $block.properties.navigation == "P" && $items|count > 1}owl-pagination-true {/if}" {if $block.properties.margin|trim}style="--ab-banner-block-margin: {$block.properties.margin}"{/if}>
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
                        items: 1,
                        singleItem: true,
                        responsive: true,
                        slideSpeed: {$block.properties.speed|default:400},
                        autoPlay: {($block.properties.delay > 0) ? $block.properties.delay * 1000 : "false"},
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
{/strip}