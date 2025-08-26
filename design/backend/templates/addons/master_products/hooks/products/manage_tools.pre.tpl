{if "MULTIVENDOR"|fn_allowed_for && $auth.user_type === "UserTypes::VENDOR"|enum}
    {include file="common/tools.tpl"
        tool_href="products.master_products"
        prefix="top"
        title=__("master_products.add_product_from_catalog")
        link_text=__("master_products.add_product_from_catalog")
        hide_tools=true
        icon="icon-plus"
        tool_override_meta="btn btn-primary"
    }

    {* Export *}
    {$allow_create_product = false scope=parent}
{/if}