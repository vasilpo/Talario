(function (_, $) {
    $(document).on('submit', 'form[name="bookings_search_form"]', function () {
        var $query = $(this).find('[name="query"]');

        $query.val($.trim($query.val()));
    });
})(Tygh, Tygh.$);
