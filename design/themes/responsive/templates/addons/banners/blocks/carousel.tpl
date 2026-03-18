{** block-description:carousel **}
{$obj_prefix = "`$block.block_id`000"}
{$block_speed = $block.properties.speed|default:400}
{$block_delay = ($block.properties.delay > 0) ? $block.properties.delay * 1000 : "false"}

{if $block_delay && $block_speed > ($block_delay / 2.5)}
    {$block_speed = ($block_delay / 2.5)}
{/if}

{$carousel_outside_navigation = $carousel_outside_navigation|default:false}
{$show_carousel_wrapper = $show_carousel_wrapper|default:false}
{$navigationTextPrev = $navigationTextPrev|default:__("prev_page")}
{$navigationTextNext = $navigationTextNext|default:__("next")}

{if $items}
    {if $show_carousel_wrapper}
        <div class="ty-owl-container-wrapper {if $block.properties.outside_navigation === "YesNo::YES"|enum}ty-owl-container-wrapper--outside-navigation{/if}">
    {/if}

    {if $carousel_outside_navigation}
        <div class="owl-theme ty-owl-controls">
            <div class="owl-controls clickable owl-controls-outside" id="owl_outside_nav_{$block.block_id}">
                <div class="owl-buttons">
                    <div id="owl_prev_{$obj_prefix}" class="owl-prev">{include_ext file="common/icon.tpl" class="ty-icon-left-open-thin"}</div>
                    <div id="owl_next_{$obj_prefix}" class="owl-next">{include_ext file="common/icon.tpl" class="ty-icon-right-open-thin"}</div>
                </div>
            </div>
        </div>
    {/if}

    <div id="banner_slider_{$block.snapping_id}" class="banners ty-owl-container owl-carousel ty-scroller"
        data-ca-scroller-item="1"
        data-ca-scroller-item-desktop="1"
        data-ca-scroller-item-desktop-small="1"
        data-ca-scroller-item-tablet="1"
        data-ca-scroller-item-mobile="1"
    >
        {foreach from=$items item="banner" key="key"}
            <div class="ty-banner__image-item ty-scroller__item">
                {if $banner.type == "G" && $banner.main_pair.image_id}
                    {if $banner.url != ""}<a class="banner__link" href="{$banner.url|fn_url}" {if $banner.target == "B"}target="_blank"{/if}>{/if}
                        {include 
                            file="common/image.tpl" 
                            images=$banner.main_pair 
                            class="ty-banner__image"
                            image_width=$block.content.width
                            image_height=$block.content.height
                        }
                    {if $banner.url != ""}</a>{/if}
                {else}
                    <div class="ty-wysiwyg-content">
                        {$banner.description nofilter}
                    </div>
                {/if}
            </div>
        {/foreach}
    </div>

    {if $show_carousel_wrapper}
        </div>
    {/if}
{/if}

<script>
(function(_, $) {
    $.ceEvent('on', 'ce.commoninit', function(context) {
        var slider = context.find('#banner_slider_{$block.snapping_id}');
        if (slider.length) {
            slider.owlCarousel({
                direction: '{$language_direction}',
                items: 1,
                singleItem : true,
                slideSpeed: {$block_speed},
                paginationSpeed: {$block_speed * 2},
                rewindSpeed: {$block_speed * 2.5},
                autoPlay: {$block_delay},
                stopOnHover: true,
                beforeInit: function () {
                    $.ceEvent('trigger', 'ce.banner.carousel.beforeInit', [this]);
                },
                {if $block.properties.navigation === "N"}
                    pagination: false
                {/if}
                {if $block.properties.navigation === "D"}
                    pagination: true
                {/if}
                {if $block.properties.navigation === "P"}
                    pagination: true,
                    paginationNumbers: true
                {/if}
                {if $block.properties.navigation === "A" && $carousel_outside_navigation}
                    pagination: false,
                    navigation: false,
                {elseif $block.properties.navigation === "A"}
                    pagination: false,
                    navigation: true,
                    navigationText: ['{$navigationTextPrev}', '{$navigationTextNext}']
                {/if}
            });

            {if $carousel_outside_navigation}
                $('#owl_prev_{$obj_prefix}').click(function(){
                    slider.trigger('owl.prev');
                });
                $('#owl_next_{$obj_prefix}').click(function(){
                    slider.trigger('owl.next');
                });
            {/if}
        }
    });
}(Tygh, Tygh.$));
</script>
