(function (_, $) {
    $(document).on('submit', 'form[name="exikane_bookings_search_form"]', function () {
        var $query = $(this).find('[name="query"]');

        $query.val($.trim($query.val()));
    });
})(Tygh, Tygh.$);
