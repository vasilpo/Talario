(function (_, $) {
  $(function () {
    // Init
    function initHelpCenterBackground($context) {
      const $helpCenter = typeof $context === 'undefined' ? $('[data-ca-help-center="main"]') : $('[data-ca-help-center="main"]', $context);
      if (!$helpCenter.length || $helpCenter.data('caHelpCenterBackgroundIsInited')) {
        return;
      }
      loadHelpDataBackground($helpCenter);
    }
    ;

    // API
    function loadHelpDataBackground($helpCenter) {
      if (!$helpCenter.length) {
        return;
      }
      const helpCenterId = $helpCenter.attr('id');
      $.ceAjax('request', $helpCenter.data('caHelpCenterServerUrl') + '?' + new URLSearchParams($helpCenter.data('caHelpCenterRequest')).toString(), {
        caching: false,
        hidden: true,
        callback: renderHelpCenterBackground,
        data: {
          helpCenterId
        }
      });
    }
    ;

    // Main render help center
    function renderHelpCenterBackground(data, params) {
      if (!data.chapters || !data.chapters.length) {
        return;
      }
      const $helpCenter = $('#' + params.data.helpCenterId);
      const elems = {
        $helpCenter: $helpCenter,
        $sectionsInjection: $('[data-ca-help-center="sectionsInjection"]', $helpCenter)
      };
      data = getAdditionalDataBackground(data, elems.$helpCenter);
      data = injectSectionsBackground(data, elems.$sectionsInjection);
      if (data.new_blocks_count > 0) {
        $('.help-center-popup__icon').attr('data-ca-help-center-counter', data.new_blocks_count);
      }
      elems.$helpCenter.data('caHelpCenterBackgroundIsInited', 1);
    }

    // Sections injection
    function injectSectionsBackground(data, $sectionsInjection) {
      if (!data.chapters || !data.chapters.length) {
        return;
      }
      const sectionsInjection = $.parseJSON($sectionsInjection.html());
      data.new_blocks_count = 0;
      data.chapters.map((chaptersItem, chaptersIndex) => {
        chaptersItem.sections.map((sectionsItem, sectionsIndex) => {
          sectionsInjection.forEach(function (injectionSection) {
            if (sectionsItem.id !== injectionSection.id) {
              return;
            }
            data.chapters[chaptersIndex].sections[sectionsIndex] = injectionSection;
            if (injectionSection.blocks.length && data.customer_last_update > data.timestamp_last_view) {
              data.new_blocks_count++;
            }
          });
          if (!sectionsItem.blocks || !sectionsItem.blocks.length) {
            return;
          }
          sectionsItem.blocks.map(block => {
            if (block.date_added && block.date_added > data.timestamp_last_view) {
              data.new_blocks_count++;
            }
          });
        });
      });
      return data;
    }

    // Get additional info from data attributes
    function getAdditionalDataBackground(data, $helpCenter) {
      data.timestamp_last_view = $helpCenter.data('caHelpCenterTimestampLastView') || 0;
      data.customer_last_update = $helpCenter.data('caHelpCenterCustomerLastUpdate') || 0;
      return data;
    }

    // Init
    initHelpCenterBackground();
    $.ceEvent('on', 'ce.commoninit', function ($context) {
      initHelpCenterBackground($context);
    });

    // Load help center when popup opens
    $.ceEvent('on', 'ce.dialogshow', function ($context) {
      if ($context.data('caHelpCenter') !== 'popupContent') {
        return;
      }
      const $helpCenter = $('[data-ca-help-center="main"]', $context);
      if (!$helpCenter.length) {
        return;
      }
      if ($helpCenter.data('caHelpCenterIsInited')) {
        $.ceHelpCenter('init');
      } else {
        // Load help center script
        $.getScript("js/addons/help_center/help_center.js?ver=".concat(encodeURIComponent(_.product_version)), function () {
          $.ceHelpCenter('init');
        });
      }
    });
  });
})(Tygh, Tygh.$);