(function (_, $) {
  const $elmPwaStatusWrapper = $('[data-ca-pwa-config="elmPwaStatusWrapper"]');
  if ($elmPwaStatusWrapper.length === 0) {
    return;
  }
  $('#elm_pwa_status', $elmPwaStatusWrapper).on('change', function (e) {
    var _$elmPwaStatusWrapper;
    const isChecked = $(this).is(':checked');
    if (!confirm($elmPwaStatusWrapper.data(isChecked ? 'caPwaConfigConfirmOnText' : 'caPwaConfigConfirmOffText'))) {
      e.preventDefault();
      $(this).prop('checked', !isChecked);
      return;
    }
    var storefrontId = (_$elmPwaStatusWrapper = $elmPwaStatusWrapper.data('caStorefrontId')) !== null && _$elmPwaStatusWrapper !== void 0 ? _$elmPwaStatusWrapper : null;
    $.ceAjax('request', fn_url($elmPwaStatusWrapper.data('caPwaConfigSubmitUrl')), {
      method: 'post',
      data: {
        manifest_status: isChecked ? 'Y' : 'N',
        result_ids: $elmPwaStatusWrapper.data('caPwaConfigResultIds'),
        storefront_id: storefrontId
      }
    });
  });
})(Tygh, Tygh.$);