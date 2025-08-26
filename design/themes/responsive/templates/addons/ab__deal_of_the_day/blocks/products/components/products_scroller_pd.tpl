{if $block.properties.enable_quick_view == "Y"}
	{$quick_nav_ids = $items|fn_fields_from_multi_level:"product_id":"product_id"}
{/if}

{if $block.properties.hide_add_to_cart_button == "Y"}
	{assign var="show_add_to_cart" value=false}
{else}
	{assign var="show_add_to_cart" value=true}
{/if}

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

{assign var="show_trunc_name" value=true}
{assign var="show_old_price" value=true}
{assign var="show_price" value=true}
{assign var="show_rating" value=true}
{assign var="show_clean_price" value=true}
{assign var="show_product_amount" value=false}
{assign var="show_list_discount" value=true}
{assign var="show_list_buttons" value=false}
{assign var="but_role" value="action"}
{assign var="show_product_labels" value=true}
{assign var="show_discount_label" value=true}
{assign var="show_shipping_label" value=true}

{* FIXME: Don't move this file *}
{script src="js/tygh/product_image_gallery.js"}
{assign var="obj_prefix" value="`$block.block_id`000"}
{$block.properties.outside_navigation = "N"}

<div id="scroll_list_{$block.block_id}" class="jcarousel-skin owl-carousel ty-scroller ty-scroller-list ab-scroller-pd"
	 data-ca-scroller-item="{$item_quantity}"
	 data-ca-scroller-item-desktop="{$item_quantity_desktop}"
	 data-ca-scroller-item-desktop-small="{$item_quantity_desktop_small}"
	 data-ca-scroller-item-tablet="{$item_quantity_tablet}"
	 data-ca-scroller-item-mobile="{$item_quantity_mobile}"
>
	{foreach from=$items item="product" name="for_products"}
		{hook name="products:product_scroller_pd"}
			<div class="ty-scroller-list__item ty-scroller__item">

				{if $product}
					{assign var="obj_id" value=$product.product_id}
					{assign var="obj_id_prefix" value="`$obj_prefix``$product.product_id`"}
					{include file="common/product_data.tpl" product=$product}

					<div class="ab-pd__item ty-grid-list__item ty-quick-view-button__wrapper">
						{assign var="form_open" value="form_open_`$obj_id`"}
						{$smarty.capture.$form_open nofilter}

						<div class="ab-pd__image" style="height: {$settings.Thumbnails.product_lists_thumbnail_height}px;">
							{include file="views/products/components/product_icon.tpl" product=$product show_gallery=false}
							{assign var="product_labels" value="product_labels_`$obj_prefix``$obj_id`"}
							{$smarty.capture.$product_labels nofilter}
						</div>

						{assign var="rating" value="rating_$obj_id"}
						<div class="ab-pd__rating">
							{if $smarty.capture.$rating|trim|strlen > 40}
								{$smarty.capture.$rating nofilter}
							{else}
								{if $addons.discussion.status == "A"}
									<div class="ty-nowrap ty-stars"><i class="ty-stars__icon ty-icon-star-empty"></i><i class="ty-stars__icon ty-icon-star-empty"></i><i class="ty-stars__icon ty-icon-star-empty"></i><i class="ty-stars__icon ty-icon-star-empty"></i><i class="ty-stars__icon ty-icon-star-empty"></i></div>
								{/if}
							{/if}
						</div>

						<div class="ab-pd__item-name">
							{assign var="name" value="name_$obj_id"}
							{$smarty.capture.$name nofilter}
						</div>

						{if $show_price}
							<div class="ab-pd__price">
								<div class="ab-pd__price-item">
									{assign var="price" value="price_`$obj_id`"}
									{$smarty.capture.$price nofilter}<br>
									{assign var="old_price" value="old_price_`$obj_id`"}
									{if $smarty.capture.$old_price|trim}{$smarty.capture.$old_price nofilter}{/if}
								</div>

								{assign var="clean_price" value="clean_price_`$obj_id`"}
								{$smarty.capture.$clean_price nofilter}
								{capture name="list_discount_`$obj_id`"}
									<span class="cm-reload-{$obj_prefix}{$obj_id}" id="line_discount_update_{$obj_prefix}{$obj_id}">
									<input type="hidden" name="appearance[show_price_values]" value="{$show_price_values}" />
									{if $product.discount}
										<span class="ty-list-price ty-save-price ty-nowrap" id="line_discount_value_{$obj_prefix}{$obj_id}"><span class="y_save">{__("you_save")}:</span> {include file="common/price.tpl" value=$product.discount span_id="discount_value_`$obj_prefix``$obj_id`" class="ty-list-price ty-nowrap"}</span>
									{elseif $product.list_discount}
										<span class="ty-list-price ty-save-price ty-nowrap" id="line_discount_value_{$obj_prefix}{$obj_id}"><span class="y_save">{__("you_save")}:</span> {include file="common/price.tpl" value=$product.list_discount span_id="discount_value_`$obj_prefix``$obj_id`"}</span>
									{/if}
									</span>
								{/capture}

								<div class="ab-pd__price-item">
									{assign var="list_discount" value="list_discount_`$obj_id`"}
									{$smarty.capture.$list_discount nofilter}
								</div>
							</div>
						{/if}
						{strip}
							<div class="ab-pd__control">
								{if $show_add_to_cart}
									<div class="button-container">
										{assign var="add_to_cart" value="add_to_cart_`$obj_id`"}
										{$smarty.capture.$add_to_cart nofilter}
									</div>
								{/if}
								{if $settings.Appearance.enable_quick_view == "YesNo::YES"|enum && $settings.ab__device != "mobile"}
									{include file="views/products/components/quick_view_link.tpl" quick_nav_ids=$quick_nav_ids}
								{/if}
							</div>
						{/strip}
						{assign var="form_close" value="form_close_`$obj_id`"}
						{$smarty.capture.$form_close nofilter}
					</div>
				{/if}
			</div>
		{/hook}
	{/foreach}
</div>

{hook name="ab__dotd:scroller_file"}
	{include file="addons/ab__deal_of_the_day/components/ab__dotd_scroller.tpl"}
{/hook}