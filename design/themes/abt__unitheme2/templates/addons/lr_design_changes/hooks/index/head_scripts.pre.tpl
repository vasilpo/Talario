{* This hook runs before render_location can mutate runtime to categories.view. *}
{if $config.current_path|default:"" == "/dev_copy" && $runtime.controller|default:"" == "index" && $runtime.mode|default:"" == "index"}
<script data-no-defer>
{literal}
document.documentElement.classList.add('talario-ux01-boot');
// Independent of the compiled bundle: a failed enhancement must leave a usable store.
window.setTimeout(function () {
    document.documentElement.classList.remove('talario-ux01-boot');
}, 8000);
{/literal}
</script>
{/if}
