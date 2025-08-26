(function (_, $) {
  // Events
  // Open the save search popup by clicking the "Save this search as" button in the saved searches panel
  $(_.doc).on('click', '[data-ca-saved-search-horizontal="searchSave"]', openSearchSavePopup);

  // Scroll saved search into view when the page loads
  $.ceEvent('on', 'ce.commoninit', scrollSavedSearchIntoViewOnMobile);

  // Open search save popup
  function openSearchSavePopup() {
    const $searchSaveButton = $('#content_search_filters [data-ca-search-filters="form"] .cm-search-filters-search-save', $(this).closest('#content_top_navigation'));
    if (!$searchSaveButton.length) {
      return;
    }
    $(document.getElementById($searchSaveButton.data('caTargetId'))).ceDialog('open', {
      width: 'auto',
      height: 'auto',
      dialogClass: 'dialog-auto-sized'
    });
  }

  // Scroll saved search into view
  function scrollSavedSearchIntoViewOnMobile($context) {
    if (!window.matchMedia('(max-width: 767px)').matches) {
      return;
    }
    const $savedSearchViews = $('#saved_search_horizontal_views', $context);
    if (!$savedSearchViews.length) {
      return;
    }
    const $savedSearchNav = $('[data-ca-saved-search-horizontal="nav"]', $savedSearchViews);
    if (!$savedSearchNav.length) {
      return;
    }
    const $activeItem = $('[data-ca-saved-search-horizontal="item"].active', $savedSearchViews);
    if (!$activeItem.length) {
      return;
    }
    const adminContentWrapperWidth = 16;
    const activeItemLeftOnCenter = $(window).width() / 2 - $activeItem.width() / 2;
    const activeItemLeft = $activeItem.position().left;
    if (activeItemLeft <= activeItemLeftOnCenter) {
      return;
    }
    $savedSearchNav[0].scrollLeft = activeItemLeft - activeItemLeftOnCenter + adminContentWrapperWidth;
  }
})(Tygh, Tygh.$);