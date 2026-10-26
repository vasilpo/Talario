{script src="js/addons/lr_design_changes/homepage_catalog.js"}
{script src="js/addons/lr_design_changes/homepage_search_filters.js"}
\n{if $runtime.controller|default:"" == "index" && $runtime.mode|default:"" == "index"}
<span id="talario-ux01-user-context" hidden data-auth="{if $auth.user_id|default:0}1{else}0{/if}" data-first-name="{$user_info.firstname|default:""|escape:"html"}"></span>
{script src="js/addons/lr_design_changes/ux01_homepage.js"}
{/if}
