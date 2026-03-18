{if !$product_type->isFieldAvailable("detailed_image")}
    <div class="control-group">
        <label class="control-label">Videos:</label>
        <div class="controls">
            {include "views/videos/picker/items.tpl"
                items                    = $product_data.videos
                object_id                = $product_data.product_id
                object_type              = "product_data"
                no_hide_input            = $no_hide_input_if_shared_product
                object_item_id_tag_level = $object_item_id_tag_level|default:2
                allow_update             = false
            }
        </div>
    </div>
{/if}
