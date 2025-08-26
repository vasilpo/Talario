{* block-description:abt__ut2_advanced_subcategories_menu *}

{if $abt__ut2_subcategories.category_tree}
    {$parent = true}

    <div class="ut2-subcategories">
        {include file="addons/abt__unitheme2/blocks/components/abt__ut2_advanced_subcategories_menu_level.tpl"
        categories = $abt__ut2_subcategories.category_tree
        level = $abt__ut2_subcategories.first_level+1
        parent = $parent
        }
    </div>
{/if}

<script>
    (function(_, $) {
        $(document).ready(function () {
            $(".ut2-more-btn").on("click", function () {
                const $this = $(this);
                const action = $this.data("action");
                const $hiddenItems = $this.siblings(".hidden-item");

                if (action === "show") {
                    $hiddenItems.removeClass("hidden");
                    $this.hide();
                    $this.siblings('[data-action="hide"]').show();
                } else if (action === "hide") {
                    $hiddenItems.addClass("hidden");
                    $this.hide();
                    $this.siblings('[data-action="show"]').show();
                }
            });
        });
    }(Tygh, Tygh.$));
</script>

