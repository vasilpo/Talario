(function (_, $) {
  $(function () {
    var url = fn_url('addons.update.rebuild?addon=vendor_panel_configurator');

    // Changing colors
    $(_.doc).on('change', '.js-vendor-panel-configurator-colors-input', function () {
      var stored_colors = {};
      $('.js-vendor-panel-configurator-colors-input').each(function (index, element) {
        if (!stored_colors[element.dataset.target]) {
          stored_colors[element.dataset.target] = {};
        }
        var elem = $('#' + element.dataset.targetInputName)[0];
        stored_colors[element.dataset.target] = elem.value;
      });
      var req = {
        vendor_panel: {
          ...stored_colors,
          color_schema: 'Custom'
        }
      };
      $.ceAjax('request', url, {
        method: 'get',
        data: {
          vendor_panel: {
            ...stored_colors,
            color_schema: 'Custom'
          }
        },
        result_ids: 'vendor_panel_config'
      });
    });

    // Changing a preset
    $(_.doc).on('change', '.js-vendor-panel-color-schema-input', function () {
      $.ceAjax('request', url, {
        result_ids: 'vendor_panel_config',
        method: 'get',
        data: {
          vendor_panel: {
            color_schema: $(this).val()
          }
        }
      });
    });
  });
})(Tygh, Tygh.$);