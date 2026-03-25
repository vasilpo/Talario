{if $settings.abt__device === "mobile"}
    <br>
    <div class="ut2-pb__content">
        <div class="lr-motivation-block-product-tab">
            {include file="addons/ab__motivation_block/blocks/ab__motivation_block.tpl"}
        </div>
        <script>
            (function (_, $) {
                $(function () {
                    $('.lr-motivation-block-product-tab').each(function () {
                        $(this).closest('.ty-wysiwyg-content').find('.tab-list-title').first().hide();
                    });
                });
            }(Tygh, Tygh.$));
        </script>
    </div>
{/if}
