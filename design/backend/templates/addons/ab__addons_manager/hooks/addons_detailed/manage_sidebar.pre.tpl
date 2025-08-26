{if preg_match('/^abt?__/', $addon.addon) }
<style>
.control-group.sidebar__stats .text-warning {
display: none;
}
</style>
<script>
(function (_, $) {
$('.control-group.sidebar__stats').each(function () {
if ($(this).find('.text-warning').length){
$(this).addClass('hidden');
}
});
})(Tygh, Tygh.$);
</script>
{/if}
