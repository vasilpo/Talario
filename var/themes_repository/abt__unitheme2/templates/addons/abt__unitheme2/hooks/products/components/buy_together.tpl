{** block-description:buy_together **}

{assign var="show_scroll" value=$show_scroll|default:true}
{assign var="bt_image_width" value=100}

{script src="js/tygh/exceptions.js"}

{if $chains}
    {strip}
        <div class="ut2__buy-together" id="ut2__buy-together">
            {if !$config.tweaks.disable_dhtml && !$no_ajax}
                {assign var="is_ajax" value=true}
            {/if}

            {foreach from=$chains key="key" item="chain" name="chains"}
                {assign var="obj_prefix" value="bt_`$chain.chain_id`"}
                <form class="abt__ut2_chain_form {if $is_ajax}cm-ajax cm-ajax-full-render{/if}{if !$smarty.foreach.chains.first && $show_scroll} hidden{/if}" action="{""|fn_url}" method="post" name="chain_form_{$chain.chain_id}" enctype="multipart/form-data">
                    <input type="hidden" name="redirect_url" value="{$config.current_url}" />
                    <input type="hidden" name="result_ids" value="cart_status*,wish_list*" />
                    {if !$stay_in_cart || $is_ajax}
                        <input type="hidden" name="redirect_url" value="{$config.current_url}" />
                    {/if}
                    <input type="hidden" name="product_data[{$chain.product_id}_{$chain.chain_id}][chain]" value="{$chain.chain_id}" />
                    <input type="hidden" name="product_data[{$chain.product_id}_{$chain.chain_id}][product_id]" value="{$chain.product_id}" />

                    {assign var="buy_together_options_class" value="cm-reload-{$obj_prefix}{$chain.product_id}_{$chain.chain_id}"}

                    {if $chain.products}
                        {foreach from=$chain.products key="_id" item="_product"}
                            {assign var="buy_together_options_class" value="{$buy_together_options_class} cm-reload-{$obj_prefix}{$_product.product_id}"}
                        {/foreach}
                    {/if}

                    <div class="subheader">{$chain.name}</div>

                    {if $chain.description}
                        <div class="ut2-bt__description">
                            {$chain.description nofilter}
                        </div>
                    {/if}

                    <div class="ut2-bt">
                        <div class="ut2-bt__products ty-scroll-x">

                            {if $chain.products}
                                <div class="ut2-bt__product">
                                    <div class="ut2-bt__product-image cm-reload-{$obj_prefix}{$chain.product_id}_{$chain.chain_id}" id="bt_product_image_{$obj_prefix}{$chain.product_id}_{$chain.chain_id}_main">
                                        <a href="{"products.view?product_id=`$chain.product_id`"|fn_url}">{include file="common/image.tpl" image_width=$bt_image_width image_height=$bt_image_width obj_id="`$chain.chain_id`_`$chain.product_id`" images=$chain.main_pair}</a>
                                        <!--bt_product_image_{$obj_prefix}{$chain.product_id}_{$chain.chain_id}_main--></div>

                                    <div class="ut2-bt__product-name">
                                        <a href="{"products.view?product_id=`$chain.product_id`"|fn_url}" class="cm-tooltip" title="{$chain.product_name}">{$chain.product_name}</a>
                                    </div>

                                    {if $chain.product_options}
                                        {capture name="buy_together_product_options"}
                                            <div id="buy_together_options_{$chain.chain_id}_{$key}_main" class="ty-buy-together-box">
                                                <div class="{$buy_together_options_class}" id="buy_together_options_update_{$chain.chain_id}_{$chain.product_id}_main">
                                                    <input type="hidden" name="appearance[show_product_options]" value="1" />
                                                    <input type="hidden" name="appearance[bt_chain]" value="{$chain.chain_id}" />
                                                    <input type="hidden" name="appearance[bt_id]" value="{$key}" />

                                                    {include file="views/products/components/product_options.tpl" product=$chain id="`$chain.product_id`_`$chain.chain_id`" product_options=$chain.product_options name="product_data" no_script=true extra_id="`$chain.product_id`_`$chain.chain_id`_main"}
                                                    <!--buy_together_options_update_{$chain.chain_id}_{$chain.product_id}_main--></div>
                                                {include file="buttons/button.tpl" but_id="add_item_close" but_name="" but_text=__("save_and_close") but_role="action" but_meta="ty-btn__primary cm-dialog-closer"}
                                            </div>
                                        {/capture}
                                        <div class="ut2-bt__product-options">
                                            {include file="common/popupbox.tpl" id="buy_together_options_`$chain.chain_id`_`$chain.product_id`_main" link_meta="ty-btn ty-btn__tertiary" text=__("specify_options") content=$smarty.capture.buy_together_product_options link_text=__("specify_options") act="general"}
                                        </div>
                                    {/if}

                                    <div class="ut2-bt__product-price cm-reload-{$obj_prefix}{$chain.product_id}_{$chain.chain_id}" id="bt_product_price_{$obj_prefix}{$chain.product_id}_{$chain.chain_id}_main">
                                        {if !(!$auth.user_id && $settings.General.allow_anonymous_shopping == "hide_price_and_add_to_cart")}
                                            <span class="price">{if $chain.min_qty > 1}<span class="count">{$chain.min_qty}x</span>{/if}{include file="common/price.tpl" value=$chain.discounted_price}</span>
                                            {if $chain.price != $chain.discounted_price}
                                                <span class="ty-strike">{include file="common/price.tpl" value=$chain.price}</span>
                                            {/if}
                                        {/if}
                                        <!--bt_product_price_{$obj_prefix}{$chain.product_id}_{$chain.chain_id}_main--></div>
                                </div>
                            {/if}

                            {foreach from=$chain.products key="_id" item="_product"}
                                <div class="ut2-bt__plus chain-plus">+</div>

                                <div class="ut2-bt__product">
                                    <input type="hidden" name="product_data[{$_product.product_id}][product_id]" value="{$_product.product_id}" />

                                    <div class="ut2-bt__product-image cm-reload-{$obj_prefix}{$_product.product_id}" id="bt_product_image_{$chain.chain_id}_{$_product.product_id}">
                                        <a href="{"products.view?product_id=`$_product.product_id`"|fn_url}">{include file="common/image.tpl" image_width=$bt_image_width image_height=$bt_image_width  obj_id="`$chain.chain_id`_`$_product.product_id`" images=$_product.main_pair class="ut2-bt__product-image"}</a>
                                        <!--bt_product_image_{$chain.chain_id}_{$_product.product_id}--></div>

                                    <div class="ut2-bt__product-name">
                                        <a href="{"products.view?product_id=`$_product.product_id`"|fn_url}" class="cm-tooltip" title="{$_product.product_name}">{$_product.product_name}</a>

                                        {if $_product.product_options}
                                            {foreach from=$_product.product_options item="option"}
                                                <div class="ut2-bt-option"><span class="ut2-bt-option__name">{$option.option_name}</span>: {$option.variant_name}</div>
                                            {/foreach}
                                        {elseif $_product.aoc == "YesNo::YES"|enum || $_product.aoc === true}
                                            {capture name="buy_together_product_options"}
                                            <div id="buy_together_options_{$chain.chain_id}_{$_product.product_id}" class="ty-buy-together-box">
                                                <div class="{$buy_together_options_class}" id="buy_together_options_update_{$chain.chain_id}_{$_product.product_id}">
                                                    <input type="hidden" name="appearance[show_product_options]" value="1" />
                                                    <input type="hidden" name="appearance[bt_chain]" value="{$chain.chain_id}" />
                                                    <input type="hidden" name="appearance[bt_id]" value="{$_id}" />
                                                    {include file="views/products/components/product_options.tpl" product=$_product id=$_product.product_id  product_options=$_product.options name="product_data" no_script=true extra_id="`$_product.product_id`_`$chain.chain_id`"}
                                                    <!--buy_together_options_update_{$chain.chain_id}_{$_product.product_id}--></div>
                                                    {include file="buttons/button.tpl" but_id="add_item_close" but_name="" but_text=__("save_and_close") but_role="action" but_meta="ty-btn__secondary cm-dialog-closer"}
                                            </div>
                                            {/capture}
                                            <div class="ut2-bt__product-options">
                                                {include file="common/popupbox.tpl" id="buy_together_options_`$chain.chain_id`_`$_product.product_id`" link_meta="" text=__("specify_options") content=$smarty.capture.buy_together_product_options link_text=__("specify_options") act="general"}
                                            </div>
                                        {/if}
                                    </div>
                                    <div class="ut2-bt__product-price cm-reload-{$obj_prefix}{$_product.product_id}" id="bt_product_price_{$chain.chain_id}_{$_product.product_id}">
                                        {if !(!$auth.user_id && $settings.General.allow_anonymous_shopping == "hide_price_and_add_to_cart")}
                                            <span class="price">{if $_product.amount > 1}<span class="count">{$_product.amount}x</span>{/if}{include file="common/price.tpl" value=$_product.discounted_price}</span>
                                            {if $_product.price != $_product.discounted_price}
                                                <span class="ty-strike">{include file="common/price.tpl" value=$_product.price}</span>
                                            {/if}
                                        {/if}
                                        <!--bt_product_price_{$chain.chain_id}_{$_product.product_id}--></div>
                                </div>

                            {/foreach}
                        </div>
                        <div class="ut2-bt__price-block">
                            {*<div class="ut2-bt__plus chain-equally"><span>=</span></div>*}
                            <div class="ut2-bt__price-wrap">
                                {if !(!$auth.user_id && $settings.General.allow_anonymous_shopping == "hide_price_and_add_to_cart")}
                                    <div class="ut2-bt-price {$buy_together_options_class}" id="bt_total_price_{$obj_prefix}{$chain.product_id}_{$chain.chain_id}">
                                        <div class="ut2-bt-price__old">
                                            <span class="ut2-bt-price__title">{__("total_list_price")}:</span>
                                            <span class="chain-old-line ty-strike">{include file="common/price.tpl" value=$chain.total_price}</span>
                                        </div>
                                        <div class="ut2-bt-price__new">
                                            <span class="ut2-bt-price__title">{__("price_for_all")}:</span>
                                            <span class="price">{include file="common/price.tpl" value=$chain.chain_price}</span>
                                        </div>
                                        <!--bt_total_price_{$obj_prefix}{$chain.product_id}_{$chain.chain_id}--></div>
                                    {if !(!$auth.user_id && $settings.General.allow_anonymous_shopping == "hide_add_to_cart_button")}
                                        <div class="cm-ut2-bt-submit" id="wrap_chain_button_{$chain.chain_id}">
                                            {include file="buttons/button.tpl" but_text=__("add_all_to_cart") but_id="chain_button_`$chain.chain_id`" but_meta="ty-btn ty-btn__outline" but_name="dispatch[checkout.add]" but_role="action" obj_id=$obj_id}
                                        </div>
                                    {/if}
                                {else}
                                    <p>{__("sign_in_to_view_price")}</p>
                                {/if}
                            </div>
                        </div>
                    </div>

                </form>
            {/foreach}
        </div>

        {if $show_scroll}
            {script src="js/lib/owlcarousel/owl.carousel.min.js"}
            <script>
                (function(_, $) {
                    $.ceEvent('on', 'ce.commoninit', function(context) {
                        var elm = context.find('#ut2__buy-together');
                        var desktop = [1230, 1],
                            desktopSmall = [1024, 1],
                            tablet = [768, 1],
                            mobile = [479, 1];

                        if (elm.length) {
                            elm.owlCarousel({
                                direction: '{$language_direction}',
                                items: 1,
                                itemsDesktop: desktop,
                                itemsDesktopSmall: desktopSmall,
                                itemsTablet: tablet,
                                itemsMobile: mobile,
                                scrollPerPage: true,
                                slideSpeed: 1000,
                                autoPlay: 10000,
                                lazyLoad: true,
                                stopOnHover: true,
                                pagination: true,
                                paginationNumbers: true,
                                navigation: true,
                                navigationText: ['<i class="ut2-icon-arrow_back_black"></i>', '<i class="ut2-icon-arrow_forward_black"></i>'],
                                afterInit: function(item) {
                                    $('.abt__ut2_chain_form.hidden').removeClass('hidden');
                                }
                            });
                        }
                    });
                }(Tygh, Tygh.$));
            </script>
        {/if}
    {/strip}
{/if}
