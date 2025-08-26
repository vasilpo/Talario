(function (_, $) {
  $.ceEvent('on', 'ce.block_manager.change', function (action, data, controllerData, response) {
    if (!response || !('root_hidden' in response) || !('block_id' in response) || action !== 'switch' && action !== 'delete') {
      return;
    }
    $("[data-ca-menu=\"mainMenuLink\"][data-ca-menu-id-path=\"".concat(response.block_id, "\"]")).toggleClass('main-menu-1__link--root-hidden', response.root_hidden);
  });
})(Tygh, Tygh.$);