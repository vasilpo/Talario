{*
    $allow_update
    $items
    $object_item_id_tag_level
    $no_hide_input
*}

{if $allow_update}
    {script src="js/tygh/backend/video_uploader.js"}
{/if}

{if $items}
    {$new_item_id = $items|array_keys|@max + 1}
{else}
    {$new_item_id = 0}
{/if}

{$hide_meta = false}

{if $no_hide_input}
    {$hide_meta = "cm-no-hide-input"}
{/if}

<div class="table-responsive-wrapper">
    <table class="{if $allow_update}js-draganddrop{/if} table table-middle table-responsive"
        width="100%"
        {if $allow_update}
            data-draganddrop-item=".draganddrop-item"
            data-draganddrop-sortable-field=".draganddrop-sortable-field"
        {/if}
    >
        <thead class="cm-first-sibling">
            <tr>
                <th width="1%"></th>
                <th width="25%">{__("url")}</th>
                <th width="15%">{__("hosting")}</th>
                <th width="25%">{__("preview")}</th>
                <th width="15%">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            {foreach $items as $item_id => $item}
                <tr class="{if $allow_update}draganddrop-item{/if} cm-row-item">
                    <td width="1%">
                        <input class="draganddrop-sortable-field input-micro"
                            type="hidden"
                            value="{$item.position}"
                        />
                        {if $allow_update}
                            <span class="handler"></span>
                        {/if}
                        <input type="hidden" name="{$object_data}[videos][video_links][{$item_id}][video_id]" value="{$item.video_id}"/>
                        <input type="hidden" name="{$object_data}[videos][video_links][{$item_id}][pair_id]" value="{$item.pair_id}"/>
                        <input type="hidden" name="{$object_data}[videos][video_data][{$item_id}][video_id]" value="{$item.video_id}"/>
                    </td>
                    <td width="25%" class="{$hide_meta}">
                        {if $allow_update}
                            <input class="input-large"
                                type="text"
                                name="{$object_data}[videos][video_data][{$item_id}][video_url_id]"
                                value="{$item.video_urls.video_url}"
                                size="30"
                                readonly
                            />
                        {else}
                            <span class="shift-input">{$item.video_urls.video_url}</span>
                        {/if}
                    </td>
                    <td width="15%" class="{$hide_meta}" >
                        {if $allow_update}
                            <select class="span2" name="{$object_data}[videos][video_data][{$item_id}][source]" disabled>
                                {$source = $item.source}
                                <option value="{$item.source}" selected="selected">{$video_sources.$source.source_name}</option>
                            </select>
                            <input type="hidden" name="{$object_data}[videos][video_data][{$item_id}][source]" value="{$item.source}"/>
                        {else}
                            <span class="shift-input">{$item.source}</span>
                        {/if}
                    </td>
                    <td width="25%" class="{$hide_meta}" >
                        {include "common/image.tpl"
                            image              = $item.preview.detailed
                            show_detailed_link = true
                            href               = $item.preview.href.video_url
                            blank              = true
                            image_css_class    = "video-preview"
                        }
                    </td>
                    {if $allow_update}
                        <td width="15%" class="right nowrap {$hide_meta}">
                            {include "buttons/multiple_buttons.tpl"
                                only_delete = "YesNo::YES"|enum
                                but_class   = "js-delete-video"
                            }
                        </td>
                    {/if}
                </tr>
            {/foreach}
            {if $allow_update}
                <tr class="draganddrop-item {$hide_meta}" id="box_add_video_{$object_id}">
                    <td width="1%">
                        <input class="draganddrop-sortable-field input-micro"
                            type="hidden"
                            value=""
                        />
                        <span class="handler"></span>
                        <input type="hidden" name="{$object_data}[videos][video_links][{$new_item_id}][video_id]" value=""/>
                        <input type="hidden" name="{$object_data}[videos][video_links][{$new_item_id}][pair_id]" value=""/>
                        <input type="hidden" name="{$object_data}[videos][video_data][{$new_item_id}][video_id]" value=""/>
                    </td>
                    <td width="25%" class="{$hide_meta}">
                        <input type="text" name="{$object_data}[videos][video_data][{$new_item_id}][video_url_id]" size="20"/>
                        <p class="muted description">{__("insert_video_link_tooltip")}</p>
                    </td>
                    <td width="15%" class="{$hide_meta}" >
                        <select class="span2" name="{$object_data}[videos][video_data][{$new_item_id}][source]">
                            <option value="" disabled selected hidden></option>
                            {foreach $video_sources as $name => $source}
                                <option value="{$name}">{$source.source_name}</option>
                            {/foreach}
                        </select>
                        <p class="{$hide_meta} muted description">{__("choose_video_hosting_tooltip")}</p>
                    </td>

                    <td width="25%" class="{$hide_meta} js-video-preview">
                        <image></image>
                    </td>
                    <td width="15%" class="right nowrap {$hide_meta}">
                        {include "buttons/multiple_buttons.tpl"
                            item_id   = "add_video_{$object_id}"
                            tag_level = $object_item_id_tag_level
                            simple    = true
                        }
                    </td>
                </tr>
            {/if}
            <input type="hidden" name="{$object_data}[videos][deleted_videos]" class="js-deleted-videos" value=""/>

            {if $override}
                <input type="hidden" name="{$object_data}[videos][override]" value="{"YesNo::YES"|enum}"/>
            {/if}
        </tbody>
    </table>
</div>