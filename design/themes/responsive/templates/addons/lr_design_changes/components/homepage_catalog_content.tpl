{$products = $catalog_data.products}
{$search = $catalog_data.search}
{$filters = $catalog_data.filters}
{$request = $catalog_data.request}

{$homepage_catalog_categories_block = $block}
{$homepage_catalog_categories_block.type = "categories"}
{$homepage_catalog_mobile_categories_block = $homepage_catalog_categories_block}
{$homepage_catalog_mobile_categories_block.block_id = "`$block.block_id`_categories_mobile"}
{$homepage_catalog_mobile_categories_block.snapping_id = "`$block.block_id`_categories_mobile"}
{$homepage_catalog_mobile_categories_block.user_class = "ut2-categories hidden-tablet hidden-desktop"}
{$homepage_catalog_filters_block = $block}
{$homepage_catalog_filters_block.type = "product_filters"}
{$homepage_catalog_sidebar_filters_block = $homepage_catalog_filters_block}
{$homepage_catalog_sidebar_filters_block.block_id = "`$block.block_id`_sidebar"}
{$homepage_catalog_sidebar_filters_block.snapping_id = "`$block.block_id`_sidebar"}
{$homepage_catalog_selected_filters_block = $homepage_catalog_filters_block}
{$homepage_catalog_selected_filters_block.block_id = "`$block.block_id`_selected"}
{$homepage_catalog_selected_filters_block.snapping_id = "`$block.block_id`_selected"}
{$homepage_catalog_mobile_filters_block = $homepage_catalog_filters_block}
{$homepage_catalog_mobile_filters_block.block_id = "`$block.block_id`_mobile"}
{$homepage_catalog_mobile_filters_block.snapping_id = "`$block.block_id`_mobile"}
{$homepage_catalog_mobile_filters_block.user_class = "ut2-filters hidden-desktop hidden-tablet rt-position"}

<div class="lr-homepage-catalog-layout">
    <div class="lr-homepage-catalog-layout__sidebar side-grid ut2-bottom">
        {if $catalog_data.categories_tree}
            <div class="lr-homepage-catalog-layout__categories ut2-subcategories-block">
                {include file="design/themes/abt__unitheme2/templates/blocks/categories/categories_dropdown_vertical.tpl"
                    block=$homepage_catalog_categories_block
                    items=$catalog_data.categories_tree
                }
            </div>
        {/if}

        <div class="ty-sidebox lr-homepage-catalog-layout__filters ut2-sidebox-important ut2-filters hidden-phone">
            <div class="ut2-sidebox-important__title">
                <span class="ut2-sidebox-important__title-wrapper">{__("lr_design_changes.homepage_catalog_filters_title")}</span>
            </div>


            <div class="ty-sidebox__body">
                {include file="design/themes/abt__unitheme2/templates/blocks/product_filters/original.tpl"
                    block=$homepage_catalog_sidebar_filters_block
                    items=$filters
                    search=$search
                }
            </div>
        </div>
    </div>

    <div class="lr-homepage-catalog-layout__content main-content-grid">
        <div class="lr-homepage-catalog-layout__section-heading hidden-phone">
            <h2 class="lr-homepage-catalog-layout__section-title">Каталог занятий</h2>
            <hr class="lr-homepage-catalog-layout__section-separator">
        </div>

        <div class="lr-homepage-catalog-layout__section-heading hidden-desktop hidden-tablet">
            <h2 class="lr-homepage-catalog-layout__section-title">Каталог занятий</h2>
            <hr class="lr-homepage-catalog-layout__section-separator">
        </div>

        <div class="container-fluid-row container-fluid-row-full-width top-sticky-panel__filters">
            <div class="row-fluid">
                <div class="span16">
                    {capture name="homepage_catalog_mobile_categories"}
                        {include file="design/themes/abt__unitheme2/templates/blocks/categories/categories_dropdown_vertical.tpl"
                            block=$homepage_catalog_mobile_categories_block
                            items=$catalog_data.categories_tree
                        }
                    {/capture}

                    {include file="design/themes/abt__unitheme2/templates/blocks/wrappers/abt__ut2_onclick_dropdown_outside_position.tpl"
                        block=$homepage_catalog_mobile_categories_block
                        content=$smarty.capture.homepage_catalog_mobile_categories
                        title=__("categories")
                    }
                    {capture name="homepage_catalog_mobile_filters"}
                        {include file="blocks/product_filters/for_category/original.tpl"
                            block=$homepage_catalog_mobile_filters_block
                            items=$filters
                            search=$search
                        }
                    {/capture}

                    {include file="design/themes/abt__unitheme2/templates/blocks/wrappers/abt__ut2_onclick_dropdown_outside_position.tpl"
                        block=$homepage_catalog_mobile_filters_block
                        content=$smarty.capture.homepage_catalog_mobile_filters
                        title=__("filters")
                    }
                </div>
            </div>
        </div>

        {include file="design/themes/abt__unitheme2/templates/blocks/product_filters/for_category/abt__ut2_selected_filters.tpl"
            block=$homepage_catalog_selected_filters_block
            items=$filters
        }

        <div class="ut2-cat-container">
            {$homepage_catalog_ajax_wrapper_id = "homepage_catalog_content_`$block.block_id`"}
            <div class="cat-view-grid" id="category_products_{$block.block_id}">
                <div id="{$homepage_catalog_ajax_wrapper_id}">
                {if $products}
                    {assign var="product_columns" value=$settings.Appearance.columns_in_products_list}
                    {$category_data = []}
                    {$ut2_load_more = $settings.abt__ut2.load_more.product_list == "YesNo::YES"|enum}
                    {$selected_layout = $catalog_data.selected_layout}
                    {$layouts = ""|fn_get_products_views:false:0}
                    {$homepage_catalog_current_url = $config.current_url}
                    {$homepage_catalog_full_render = $full_render|default:null}
                    {$homepage_catalog_runtime = $runtime}
                    {$homepage_catalog_extra_url = $extra_url|default:null}

                    {if strpos($config.current_url, "?") === false}
                        {$config.current_url = $config.current_url|fn_link_attach:"dispatch=index.index"}
                    {/if}

                    {$full_render = true}
                    {$extra_url = "&full_render=Y"}

                    {include file="addons/lr_design_changes/components/homepage_catalog_sorting.tpl"
                        category_data=$category_data
                        request=$request
                        search=$search
                        selected_layout=$selected_layout
                        target_id=$homepage_catalog_ajax_wrapper_id
                        base_url=$config.current_url
                    }

                    {if $layouts.$selected_layout.template}
                        {$runtime.controller = "categories"}
                        {$runtime.mode = "view"}
                        {include file="`$layouts.$selected_layout.template`"
                            columns=$product_columns
                            no_pagination=true
                            no_sorting=true
                        }
                        {$runtime = $homepage_catalog_runtime}
                    {/if}

                    {$config.current_url = $homepage_catalog_current_url}
                    {$full_render = $homepage_catalog_full_render}
                    {$extra_url = $homepage_catalog_extra_url}
                {elseif !$show_not_found_notification && $request.features_hash}
                    {include file="common/no_items.tpl"
                        text_no_found=__("text_no_products_found")
                        no_items_extended=true
                        reset_url=$config.current_url|fn_query_remove:"features_hash"
                    }
                {else}
                    {include file="common/no_items.tpl"
                        text_no_found=__("text_no_products")
                    }
                {/if}
                <!--{$homepage_catalog_ajax_wrapper_id}--></div>
            <!--category_products_{$block.block_id}--></div>
        </div>
    </div>
</div>
