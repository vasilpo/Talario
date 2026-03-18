{if
    ($product_data.master_product_id && !$product_type->isFieldAvailable("detailed_image"))
    || ($product_data && $runtime.company_id && isset($product_data.company_id) && $product_data.company_id == 0)
}
    <div class="control-group">
        <label class="control-label">{__("videos")}:</label>
        <div class="controls">
            {include "views/videos/picker/items.tpl"
                items                   = $product_data.videos
                object_id               = $product_data.product_id
                object_type             = "product_data"
                no_hide_input           =  $no_hide_input_if_shared_product
                object_item_id_tag_level= $object_item_id_tag_level|default:2
                allow_update            = false
            }
        </div>
    </div>
{/if}
