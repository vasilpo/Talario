{if $saved_search && $saved_search.dispatch && $view_type}
    {* 
        Import
        ---
        $saved_search
        $view_type
        $is_compact_view
        $search
        $last_view_current_object_schema

        Local
        ---
        $saved_search_count_threshold_difference
        $saved_search_count_threshold_xl
        $saved_search_count_threshold_xxl
        $saved_search_count_threshold_xxxl
        $views
        $return_current_url
        $redirect_current_url
        $saved_search_count_threshold
        $new_search
        $active_view
        $view

        Global
        ---
        $config
    *}

    {script src="js/tygh/saved_search_horizontal.js"}

    {$new_search = $saved_search.allow_new_search|default:true}
    {$views = $view_type|fn_get_views}
    {$return_current_url = $config.current_url|fn_query_remove:"view_id":"new_view"}
    {$redirect_current_url = $config.current_url|escape:url}

    {* Saved search count thresholds *}
    {$saved_search_count_threshold_difference = $config.saved_search.count_threshold_difference|default:3}
    {$saved_search_count_threshold_xl = $config.saved_search.count_threshold_xl|default:9}
    {$saved_search_count_threshold_xxl = $config.saved_search.count_threshold_xxl|default:11}
    {$saved_search_count_threshold_xxxl = $config.saved_search.count_threshold_xxxl|default:13}
    {if $is_compact_view}
        {$saved_search_count_threshold_xl = $saved_search_count_threshold_xl - $saved_search_count_threshold_difference}
        {$saved_search_count_threshold_xxl = $saved_search_count_threshold_xxl - $saved_search_count_threshold_difference}
        {$saved_search_count_threshold_xxxl = $saved_search_count_threshold_xxxl - $saved_search_count_threshold_difference}
    {/if}
    {* Minus "All" view *}
    {$saved_search_count_threshold_xl = $saved_search_count_threshold_xl - 1}
    {$saved_search_count_threshold_xxl = $saved_search_count_threshold_xxl - 1}
    {$saved_search_count_threshold_xxxl = $saved_search_count_threshold_xxxl - 1}
    
    {$saved_search_btn_group_class = ""}
    {if $views|@count > $saved_search_count_threshold_xxxl}
        {* Skip *}
        {$saved_search_btn_group_class = "saved-search__btn-group--xxxxl"}
    {elseif $views|@count > $saved_search_count_threshold_xxl}
        {$saved_search_btn_group_class = "saved-search__btn-group--xxxl"}
    {elseif $views|@count > $saved_search_count_threshold_xl}
        {$saved_search_btn_group_class = "saved-search__btn-group--xxl"}
    {elseif $views|@count > 0}
        {$saved_search_btn_group_class = "saved-search__btn-group--xl"}
    {/if}

    {* Get iteration for active saved search *}
    {$views_prepare = $views}
    {foreach $views_prepare as $view_prepare_key => $view_prepare}
        {$views_prepare[$view_prepare_key].index = $view_prepare@index}
    {/foreach}
    {$search_iteration = ($views_prepare[$search.view_id].index + 1)}

    <div class="pills">
        <ul class="nav nav-pills saved-search-horizontal" data-ca-saved-search-horizontal="nav">
            <li class="saved-search__item saved-search__item--horizontal
                {if !$search.view_id|intval && !$search.temp_view}active{/if}" data-ca-saved-search-horizontal="item">
                <a href="{"`$saved_search.dispatch`.reset_view?`$view_suffix`"|fn_url}" class="saved-search__item-name saved-search__item-name--horizontal">{__("all")}</a>
            </li>
            {if $views}
                {foreach $views as $view name=views}
                    {$saved_search_item_class = ""}

                    {if $view.view_id|intval !== $search.view_id|intval}
                        {if $view@iteration > (
                                ($search_iteration > $saved_search_count_threshold_xxxl)
                                    ? ($saved_search_count_threshold_xxxl - 1)
                                    : $saved_search_count_threshold_xxxl
                            )
                        }
                            {$saved_search_item_class = "`$saved_search_item_class` saved-search__item--visible-xxxl"}
                        {/if}
                        {if $view@iteration > (
                                ($search_iteration > $saved_search_count_threshold_xxl)
                                    ? ($saved_search_count_threshold_xxl - 1)
                                    : $saved_search_count_threshold_xxl
                            )
                        }
                            {$saved_search_item_class = "`$saved_search_item_class` saved-search__item--visible-xxl"}
                        {/if}
                        {if $view@iteration > (
                                ($search_iteration > $saved_search_count_threshold_xl)
                                    ? ($saved_search_count_threshold_xl - 1)
                                    : $saved_search_count_threshold_xl
                            )
                        }
                            {$saved_search_item_class = "`$saved_search_item_class` saved-search__item--visible-xl"}
                        {/if}
                    {/if}

                    {* Active saved search with allow default view or create new search *}
                    {if $view.view_id|intval === $search.view_id|intval
                        && (
                            $last_view_current_object_schema.allow_default_view
                            || $new_search
                        )
                    }
                    <li class="dropdown active saved-search__item saved-search__item--horizontal {$saved_search_item_class}"
                        data-ca-saved-search-horizontal="item">
                        <a class="cm-view-name dropdown-toggle saved-search__item-name saved-search__item-name--horizontal"
                            href="#"
                            data-toggle="dropdown"
                            data-ca-saved-search-horizontal-view-id="{$view.view_id}"
                            data-ca-saved-search-horizontal-view-name="{$view.name}"
                        >
                            {$view.name} <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            {if $last_view_current_object_schema.allow_default_view}
                                <li>
                                    {if $view.is_default === "YesNo::YES"|enum}
                                        <a href="{"`$saved_search.dispatch`.unset_default_view?view_id=`$view.view_id`&redirect_url=`$redirect_current_url`"|fn_url}"
                                            class="cm-confirm"
                                            {([
                                                "data-ca-confirm-text" => __("saved_search.set_as_non_default_confirm", [
                                                        "[name]" => $view.name
                                                    ])
                                            ])|render_tag_attrs nofilter}
                                        >
                                            <span class="flex-inline top">
                                                {include_ext file="common/icon.tpl" class="icon-pushpin"}
                                            </span>
                                            {__("saved_search.set_as_non_default")}
                                        </a>
                                    {else}
                                        <a href="{"`$saved_search.dispatch`.set_default_view?view_id=`$view.view_id`&redirect_url=`$redirect_current_url`"|fn_url}"
                                            class="cm-confirm"
                                            {([
                                                "data-ca-confirm-text" => __("saved_search.set_as_default_confirm", [
                                                        "[name]" => $view.name
                                                    ])
                                            ])|render_tag_attrs nofilter}
                                        >
                                            <span class="flex-inline top">
                                                {include_ext file="common/icon.tpl" class="icon-pushpin"}
                                            </span>
                                            {__("saved_search.set_as_default")}
                                        </a>
                                    {/if}
                                </li>
                            {/if}
                            {if $new_search}
                                <li>
                                    <a href="{"`$saved_search.dispatch`.delete_view?view_id=`$view.view_id`&redirect_url=`$redirect_current_url`"|fn_url}"
                                        class="cm-confirm text-error"
                                    >
                                        <span class="flex-inline top">
                                            {include_ext file="common/icon.tpl" class="icon-trash"}
                                        </span>
                                        {__("delete")}
                                    </a>
                                </li>
                            {/if}
                        </ul>
                    </li>
                    {* Active saved search without permissions *}
                    {elseif $view.view_id|intval === $search.view_id|intval}
                    <li class="active saved-search__item saved-search__item--horizontal {$saved_search_item_class}"
                        data-ca-saved-search-horizontal="item">
                        <a class="cm-view-name saved-search__item-name"
                            href="#"
                            data-ca-saved-search-horizontal-view-id="{$view.view_id}"
                            data-ca-saved-search-horizontal-view-name="{$view.name}"
                        >
                            {$view.name}
                        </a>
                    </li>
                    {else}
                    {* Saved search item *}
                    <li class="saved-search__item saved-search__item--horizontal {$saved_search_item_class}"
                        data-ca-saved-search-horizontal="item">
                        <a class="cm-view-name saved-search__item-name saved-search__item-name--horizontal"
                            href="{"`$saved_search.dispatch`?view_id=`$view.view_id``$view_additional_parameters`&`$view_suffix`"|fn_url}"
                            data-ca-saved-search-horizontal-view-id="{$view.view_id}"
                            data-ca-saved-search-horizontal-view-name="{$view.name}"
                        >
                            {$view.name}
                        </a>
                    </li>
                    {/if}
                {/foreach}
            {/if}

            {* Custom saved search *}
            {if $search.temp_view}
                <li class="saved-search__item saved-search__item--horizontal active {$saved_search_item_class}"
                    data-ca-saved-search-horizontal="item">
                    <a href="#">{__("custom_search")}</a>
                </li>
            {/if}

            {* More saved search dropdown *}
            {if $views}
                <li class="btn-group saved-search__btn-group {$saved_search_btn_group_class}">
                    <a class="saved-search__item-name--horizontal saved-search__item-name--more dropdown-toggle" data-toggle="dropdown">
                        {__("saved_search.more_short")}
                        <span class="caret"></span>
                    </a>
                    <ul id="tools_list_saved_search_horizontal" class="dropdown-menu cm-smart-position">
                        {foreach $views as $view}
                            {$view_class = ""}
                            {if $view@iteration === $search_iteration}
                                {$view_class = "`$view_class` hidden"}
                            {else}
                                {if $view@iteration <= (
                                        ($search_iteration > $saved_search_count_threshold_xxxl)
                                            ? ($saved_search_count_threshold_xxxl - 1)
                                            : $saved_search_count_threshold_xxxl
                                    )
                                }
                                    {$view_class = "`$view_class` saved-search__dropdown-item--hidden-xxxl"}
                                {/if}
                                {if $view@iteration <= (
                                        ($search_iteration > $saved_search_count_threshold_xxl)
                                            ? ($saved_search_count_threshold_xxl - 1)
                                            : $saved_search_count_threshold_xxl
                                    )
                                }
                                    {$view_class = "`$view_class` saved-search__dropdown-item--hidden-xxl"}
                                {/if}
                                {if $view@iteration <= (
                                        ($search_iteration > $saved_search_count_threshold_xl)
                                            ? ($saved_search_count_threshold_xl - 1)
                                            : $saved_search_count_threshold_xl
                                    )
                                }
                                    {$view_class = "`$view_class` saved-search__dropdown-item--hidden-xl"}
                                {/if}
                            {/if}

                            <li class="saved-search__dropdown-item {$view_class} {if $view.view_id|intval === $search.view_id|intval}active{/if} {if $view.wrapper_class}{$view.wrapper_class}{/if}">
                                {btn type=$view.type|default:"list"
                                    href="`$saved_search.dispatch`?view_id=`$view.view_id``$view_additional_parameters`&`$view_suffix`"
                                    text=$view.name
                                    title=$view.description
                                    id=$view.id
                                    method=$view.method
                                    target=$view.target
                                    process=$view.process
                                    class=($view.meta) ? "saved-search__dropdown-item-name `$view.meta`" : "saved-search__dropdown-item-name"
                                    form=$view.form
                                    dispatch=$view.dispatch
                                    target=$view.target
                                    data=[
                                        "data-ca-saved-search-horizontal-view-id" => $view.view_id,
                                        "data-ca-saved-search-horizontal-view-name" => $view.name
                                    ]
                                    onclick=$view.onclick
                                    raw=$view.raw
                                    icon=$view.icon
                                }
                            </li>
                        {/foreach}
                    </ul>
                </li>
            {/if}

            {* Save this search as button *}
            {if $new_search}
                <li class="saved-search__item saved-search__item--horizontal saved-search__item--new"
                    data-ca-saved-search-horizontal="item">
                    <button type="button"
                        class="saved-search__item-name saved-search__item-name--horizontal saved-search__item-name--new"
                        data-ca-saved-search-horizontal="searchSave"
                    >
                        {include_ext file="common/icon.tpl" class="icon-plus"}
                    </button>
                </li>
            {/if}
        </ul>
    </div>
{/if}
