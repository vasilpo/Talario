{** block-description:tmpl_abt__ut2__categories_title_block **}

<div class="ut2-extra-block-title seo-adjustments-categories-title-block">
    {hook name="wrapper:categories_title_wrapper"}
    {if $category_data.category}
        <h1 class="ty-mainbox-title">
            {hook name="wrapper:categories_title"}
            {$category_data.category nofilter}
            {/hook}
        </h1>
    {/if}
    {/hook}
</div>
