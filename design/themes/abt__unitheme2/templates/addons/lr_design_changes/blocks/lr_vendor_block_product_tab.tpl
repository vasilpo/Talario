{if $settings.abt__device === "mobile"}
    <div class="ut2-pb__content">
        <div class="lr-vendor-block-product-tab">
            {include file="addons/sd_design_changes/overrides/addons/abt__unitheme2_mv/hooks/products/ab__vendor_block.pre.tpl"}
        </div>
        <script>
            (function (_, $) {
                $(function () {
                    $('.lr-vendor-block-product-tab').each(function () {
                        $(this).closest('.ty-wysiwyg-content').find('.tab-list-title').first().hide();
                    });
                });
            }(Tygh, Tygh.$));
        </script>
    </div>
{/if}
