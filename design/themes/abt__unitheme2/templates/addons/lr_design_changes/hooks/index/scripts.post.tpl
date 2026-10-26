{script src="js/addons/lr_design_changes/homepage_catalog.js"}
{script src="js/addons/lr_design_changes/homepage_search_filters.js"}
{* Native homepage blocks can change runtime.controller to categories.view. *}
{* Keep assets dev-only; the JS requires the actual homepage catalog before activation. *}
{if $config.current_path|default:"" == "/dev_copy"}
<script>
Tygh.talarioUx01Context = {
    authenticated: {if $auth.user_id|default:0}true{else}false{/if},
    firstName: {$user_info.firstname|default:""|json_encode:15 nofilter}
};
</script>
{script src="js/addons/lr_design_changes/ux01_homepage.js"}
{/if}
