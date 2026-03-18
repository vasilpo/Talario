(function (_, $) {
  $.ceEvent('on', 'ce:geomap:location_set_after', function (location, $container, response, auto_detect) {
    const $dependsByLocationBlocks = $(_.body).find('.cm-warehouse-block-depends-by-location');
    if (!response.is_detected || $dependsByLocationBlocks.length === 0) {
      return;
    }
    $.ceAjax('request', _.current_url, {
      result_ids: $dependsByLocationBlocks.map((_, element) => element.id).get().join(),
      full_render: true
    });
  });
})(Tygh, Tygh.$);