{script src="js/lib/owlcarousel/owl.carousel.min.js"}
<script>
    (function(_, $) {
        $.ceEvent('on', 'ce.commoninit', function(context) {
            var elm = context.find('#scroll_list_{$block.block_id}');

            if (elm.length) {
                $('.ty-float-left:contains(.ty-scroller-list),.ty-float-right:contains(.ty-scroller-list)').css('width', '100%');

                {if $block.properties.outside_navigation == "Y"}
                function outsideNav () {
                    if(this.options.items >= this.itemsAmount){
                        $("#owl_outside_nav_{$block.block_id}").hide();
                    } else {
                        $("#owl_outside_nav_{$block.block_id}").show();
                    }
                }
                {/if}

                var items = {$block.properties.item_quantity|default:4};
                var itemsDesktop = 4,
                    itemsDesktopSmall = 3;
                    itemsTablet = 3;
                    itemsTabletSmall = {$itemsTabletSmall|default:2};
                    itemsMobile = {$item_quantity_responsive["mobile"]|default:1};

                if ( items > 4 ) {
                    itemsDesktop =  4;
                    itemsDesktopSmall = 3;
                    itemsTablet = 3;
                    itemsTabletSmall = 2;
                } else if ( items === 1 ) {
                    itemsDesktop = itemsDesktopSmall = itemsTablet = 1;
                }

                var itemsTabletSmall = 2,
                    itemsMobile = 1;

                elm.owlCarousel({
                    direction: '{$language_direction}',
                    items: items,
                    itemsDesktop: [1400, itemsDesktop],
                    itemsDesktopSmall: [1399, itemsDesktopSmall],
                    itemsTablet: [900, itemsTablet],
                    itemsTabletSmall: [700, itemsTabletSmall],
                    itemsMobile: [480, itemsMobile],
                    {if $block.properties.scroll_per_page == "Y"}
                    scrollPerPage: true,
                    {/if}
                    {if $block.properties.not_scroll_automatically == "Y"}
                    autoPlay: false,
                    {else}
                    autoPlay: '{$block.properties.pause_delay * 1000|default:0}',
                    {/if}
                    lazyLoad: true,
                    slideSpeed: {$block.properties.speed|default:400},
                    stopOnHover: true,
                    {if $block.properties.outside_navigation == "N"}
                    navigation: true,
                    navigationText: ['<i class="ty-icon-left-open-thin"></i>', '<i class="ty-icon-right-open-thin"></i>'],
                    {/if}
                    pagination: false
                    {if $block.properties.outside_navigation == "Y"},
                    afterInit: outsideNav,
                    afterUpdate : outsideNav
                });

                $('{$prev_selector}').click(function(){
                    elm.trigger('owl.prev');
                });
                $('{$next_selector}').click(function(){
                    elm.trigger('owl.next');
                });

                {else}
            });
        {/if}

    }
    });
    }(Tygh, Tygh.$));
</script>