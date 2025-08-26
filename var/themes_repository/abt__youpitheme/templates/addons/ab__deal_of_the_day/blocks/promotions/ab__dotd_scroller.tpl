{** block-description:ab__dotd_scroller **}

{$image_height="200"}
{$image_width="330"}

{if $items}
    {$item_quantity = $block.properties.item_quantity|default:5}
    {$item_quantity_desktop = $item_quantity}
    {$item_quantity_mobile = 1}

    {if $item_quantity > 3}
        {$item_quantity_desktop_small = $item_quantity - 1}
        {$item_quantity_tablet = $item_quantity - 2}
    {elseif $item_quantity === 1}
        {$item_quantity_desktop_small = $item_quantity}
        {$item_quantity_tablet = $item_quantity}
    {else}
        {$item_quantity_desktop_small = $item_quantity - 1}
        {$item_quantity_tablet = $item_quantity - 1}
    {/if}

    {$obj_prefix="`$block.block_id`000"}
    {$block.block_id = "{$block.block_id}_{uniqid()}"}

    {if $block.properties.outside_navigation == "Y"}
        <div class="owl-theme ty-owl-controls">
            <div class="owl-controls clickable owl-controls-outside" id="owl_outside_nav_{$block.block_id}">
                <div class="owl-buttons">
                    <div id="owl_prev_{$obj_prefix}" class="owl-prev"><i class="material-icons">&#xE408;</i></div>
                    <div id="owl_next_{$obj_prefix}" class="owl-next"><i class="material-icons">&#xE409;</i></div>
                </div>
            </div>
        </div>
    {/if}

    <div class="ab__dotd_promotions scroller">
        <div id="scroll_list_{$block.block_id}" class="owl-carousel ty-scroller-list ty-scroller"
             data-ca-scroller-item="{$item_quantity}"
             data-ca-scroller-item-desktop="{$item_quantity_desktop}"
             data-ca-scroller-item-desktop-small="{$item_quantity_desktop_small}"
             data-ca-scroller-item-tablet="{$item_quantity_tablet}"
             data-ca-scroller-item-mobile="{$item_quantity_mobile}"
        >
            {foreach from=$items item="promotion"}
                {if $promotion.status == "A"}
                    <div class="ab__dotd_promotions-item ty-scroller__item">
                        <div class="ab__dotd_promotions-item_image">
                            <a href="{"promotions.view?promotion_id=`$promotion.promotion_id`"|fn_url}" title="">
                                {include file="common/image.tpl" images=$promotion.image image_height=$image_height image_width=$image_width no_ids=true}
                            </a>
                        </div>

                        {if $promotion.ab__dotd_active && $promotion.to_date}
                            {assign var="days_left" value=(($promotion.to_date-$smarty.now)/86400)|ceil}
                            <div class="ab__dotd_promotions-item_days_left{if $days_left <= $addons.ab__deal_of_the_day.highlight_when_left} ab__dotd_highlight{/if}">
                                {if $days_left > 1}
                                    {__('ab__dotd.days_left', [$days_left])}
                                {else}
                                    {__('ab__dotd.today_only')}
                                {/if}
                            </div>
                        {elseif $promotion.ab__dotd_awaited}
                            {assign var="days_left" value=(1-($smarty.now-$promotion.from_date)/86400)|floor}
                            <div class="ab__dotd_promotions-item_days_left">
                                {__('ab__dotd.days_to_start', [$days_left])}
                            </div>
                        {elseif $promotion.ab__dotd_expired}
                            <div class="ab__dotd_promotions-item_days_left">
                                {__('ab__dotd.promotion_expired')}
                            </div>
                        {/if}

                        <div class="ab__dotd_promotions-item_title">
                            <a href="{"promotions.view?promotion_id=`$promotion.promotion_id`"|fn_url}" title="">{$promotion.name nofilter}</a>
                            <div class="ab__dotd_promotions-item_date">
                                {if $promotion.from_date}
                                    {__('ab__dotd.from')} {$promotion.from_date|date_format:"`$settings.Appearance.date_format`"}
                                {/if}
                                {if $promotion.to_date}
                                    {__('ab__dotd.to')} {$promotion.to_date|date_format:"`$settings.Appearance.date_format`"}
                                {/if}
                            </div>
                        </div>
                    </div>
                {/if}
            {/foreach}
        </div>
    </div>

    <div class="all-promotions__button"><a href="{"promotions.list"|fn_url}" class="ty-btn ty-btn__secondary" title="">{__("ab__dotd.all_promotions_list")}</a></div>

    {$prev_selector="#owl_prev_`$obj_prefix`"}
    {$next_selector="#owl_next_`$obj_prefix`"}

    <script>
        (function(_, $) {
            $.ceEvent('on', 'ce.commoninit', function(context) {
                var elm = context.find('#scroll_list_{$block.block_id}');

                $('.ty-float-left:contains(.ty-scroller-list),.ty-float-right:contains(.ty-scroller-list)').css('width', '100%');

                var item = {$block.properties.item_quantity|default:4},
                    itemsDesktop = 4,
                    itemsDesktopSmall = 4;
                itemsTablet = 3;
                itemsTabletSmall = {$itemsTabletSmall|default:2};
                itemsMobile = {$item_quantity_responsive["mobile"]|default:1};

                var desktop = [1400, itemsDesktop],
                    desktopSmall = [1399, itemsDesktopSmall],
                    tablet = [979, itemsTablet],
                    tabletSmall = [768, itemsTabletSmall];
                mobile = [480, itemsMobile];

                {if $block.properties.outside_navigation == "Y"}
                function outsideNav () {
                    if(this.options.items >= this.itemsAmount){
                        $("#owl_outside_nav_{$block.block_id}").hide();
                    } else {
                        $("#owl_outside_nav_{$block.block_id}").show();
                    }
                }
                function afterInit () {
                    outsideNav.apply(this);
                    $.ceEvent('trigger', 'ce.scroller.afterInit', [this]);
                }
                function afterUpdate () {
                    outsideNav.apply(this);
                    $.ceEvent('trigger', 'ce.scroller.afterUpdate', [this]);
                }
                {else}
                function afterInit () {
                    $.ceEvent('trigger', 'ce.scroller.afterInit', [this]);
                }
                function afterUpdate () {
                    $.ceEvent('trigger', 'ce.scroller.afterUpdate', [this]);
                }
                {/if}
                function beforeInit () {
                    $.ceEvent('trigger', 'ce.scroller.beforeInit', [this]);
                }
                function beforeUpdate () {
                    $.ceEvent('trigger', 'ce.scroller.beforeUpdate', [this]);
                }

                if (elm.length) {
                    elm.owlCarousel({
                        direction: '{$language_direction}',
                        items: item,
                        itemsDesktop: desktop,
                        itemsDesktopSmall: desktopSmall,
                        itemsTablet: tablet,
                        itemsTabletSmall: tabletSmall,
                        itemsMobile: mobile,
                        responsiveBaseWidth: elm,
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
                        pagination: false,
                        beforeInit: beforeInit,
                        afterInit: afterInit,
                        beforeUpdate: beforeUpdate,
                        afterUpdate: afterUpdate
                    });
                    {if $block.properties.outside_navigation == "Y"}

                    $('{$prev_selector}').click(function(){
                        elm.trigger('owl.prev');
                    });
                    $('{$next_selector}').click(function(){
                        elm.trigger('owl.next');
                    });
                    {/if}
                }
            });
        }(Tygh, Tygh.$));
    </script>

{/if}