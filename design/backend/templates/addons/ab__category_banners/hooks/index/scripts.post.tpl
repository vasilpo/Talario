<script>
(function (_, $) {
_.tr({
ab__cb_wrong_time_format: '{__("ab__cb.validator.wrong_time_format")|escape:"javascript"}'
});
$(document).ready(function () {
{literal}
$.ceFormValidator('registerValidator', {
class_name: 'cm-ab-cb-time',
message: _.tr('ab__cb_wrong_time_format'),
func: function (elm_id, elm) {
re = /^(\d{1,2}):(\d{2})?$/;
let elmVal = elm.val();
if (elmVal === '') {
return true;
}
if (regs = elmVal.match(re)) {
return regs[1] <= 23 && regs[2] <= 59;
}
return false;
}
});
});
{/literal}
}(Tygh, Tygh.$));
</script>