{style src="addons/lr_design_changes/styles.less"}
{* Match the script gate without depending on a block-mutated runtime controller. *}
{if $config.current_path|default:"" == "/dev_copy"}
{style src="addons/lr_design_changes/ux01_homepage.less"}
{/if}
