{script src="js/addons/ec_table_booking_system/date_picker.js"}
{if $runtime.controller == 'products' && $runtime.mode == 'view'}
    <script>
        $(document).ready(function(){
            $('.ui-state-default.ui-state-active').click();
        });
    </script>
{/if}
<script>
    $(document).ready(function(){
        $(document).find('.ec_datepickerd').closest('.ty-product-block').removeClass('sticky_add_to_cart');
    });
</script>
<script>
    (function(_, $) {
         var product_id = '{$smarty.request.product_id}';
        $(document).on('change', ".ec_select_slots_list", function(){
            var selected_time = $(this).val();
            var date = $(this).attr('data-ca-selected-date');
            var product_id = $(this).attr('data-ca-productid');
            $.ceAjax('request', fn_url('products.check_slots&selected_date='+date+'&product_id='+product_id+'&selected_time='+selected_time), {
                result_ids: 'ec_timeslots_list',
                method: 'get',
                full_render: true,
                caching: false,
            });
        });
    }(Tygh, Tygh.$));
</script>