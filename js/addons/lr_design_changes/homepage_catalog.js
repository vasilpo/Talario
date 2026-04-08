(function (_, $) {
    var MOBILE_MAX_WIDTH = 768;

    function disableHomepageHorizontalFiltersScrollClose(context) {
        if (window.innerWidth > MOBILE_MAX_WIDTH) {
            return;
        }

        var $context = context ? $(context) : $(_.doc);

        $context
            .find('.lr-homepage-catalog-layout .ut2-hz-filters .ut2-scroll-content')
            .off('scroll');
    }

    $.ceEvent('on', 'ce.commoninit', function (context) {
        disableHomepageHorizontalFiltersScrollClose(context);
    });

    $(function () {
        disableHomepageHorizontalFiltersScrollClose(_.doc);
    });
}(Tygh, Tygh.$));
