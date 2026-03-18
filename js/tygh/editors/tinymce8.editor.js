/* editior-description:text_tinymce8 */
(function (_, $) {
  // FIXME: when jQuery UI will be updated from 1.11.1 version, remove the code below.
  $.widget("ui.dialog", $.ui.dialog, {
    /*! jQuery UI - v1.10.2 - 2013-12-12
     *  http://bugs.jqueryui.com/ticket/9087#comment:27 - bugfix
     *  http://bugs.jqueryui.com/ticket/4727#comment:23 - bugfix
     *  allowInteraction fix to accommodate windowed editors
     */
    _allowInteraction: function (event) {
      if (this._super(event)) {
        return true;
      }

      // address interaction issues with general iframes with the dialog
      if (event.target.ownerDocument != this.document[0]) {
        return true;
      }

      // address interaction issues with dialog window
      if ($(event.target).closest(".mce-container").length) {
        return true;
      }

      // address interaction issues with iframe based drop downs in IE
      if ($(event.target).closest(".mce").length) {
        return true;
      }
    },
    /*! jQuery UI - v1.10.2 - 2013-10-28
     *  http://dev.ckeditor.com/ticket/10269 - bugfix
     *  moveToTop fix to accommodate windowed editors
     */
    _moveToTop: function (event, silent) {
      if (!event || !this.options.modal) {
        this._super(event, silent);
      }
    }
  });

  // Prevent jQuery UI dialog from blocking focusin
  $(document).on('focusin', function (e) {
    if ($(e.target).closest(".tox-tinymce, .tox-tinymce-aux, .moxman-window, .tam-assetmanager-root").length) {
      e.stopImmediatePropagation();
    }
  });
  const tyghSettings = $('#tygh_settings').length ? $('#tygh_settings').data() : {};
  var support_langs = ['en', 'ar', 'hy', 'az', 'eu', 'be', 'bs', 'ca', 'hr', 'cs', 'da', 'dv', 'nl', 'et', 'fi', 'gl', 'de', 'el', 'id', 'it', 'ja', 'kk', 'lv', 'lt', 'fa', 'pl', 'ro', 'ru', 'sr', 'sk', 'es', 'tg', 'ta', 'ug', 'uk', 'vi', 'cy', 'tr', 'uz', 'eo', 'ga',, 'kab', 'ku', 'ky', 'ne', 'oc', 'sq'];
  var lang_map = {
    'fr': 'fr_FR',
    'ka': 'ka_GE',
    'he': 'he_IL',
    'hu': 'hu_HU',
    'is': 'is_IS',
    'bg': 'bg_BG',
    'zh': 'zh_CN',
    'ko': 'ko_KR',
    'nb': 'nb_NO',
    'pt': 'pt_PT',
    'sl': 'sl_SI',
    'sv': 'sv_SE',
    'th': 'th_TH'
  };
  var lang = fn_get_listed_lang(support_langs);
  if (lang in lang_map) {
    lang = lang_map[lang];
  }
  const isDarkAdminArea = tyghSettings && tyghSettings.caArea === 'A' && tyghSettings.caIsDarkTheme;
  var editor = {
    editorName: 'tinymce8',
    is_destroying: false,
    params: {
      base_url: "".concat(_.current_location, "/js/lib/tinymce8"),
      suffix: '.min',
      license_key: 'gpl',
      // Not installed: accordion anchor autoresize charmap codesample directionality emoticons help importcss insertdatetime media nonbreaking pagebreak preview quickbars save searchreplace table visualblocks visualchars
      plugins: 'autolink autosave wordcount link image lists advlist code fullscreen',
      menubar: false,
      statusbar: true,
      extended_valid_elements: "i[*],span[*]",
      forced_root_block: 'p',
      media_strict: false,
      toolbar: undefined,
      toolbar_mode: 'wrap',
      resize: true,
      theme: 'silver',
      language: lang,
      strict_loading_mode: true,
      convert_urls: false,
      remove_script_host: false,
      body_class: 'wysiwyg-content',
      content_css: isDarkAdminArea ? 'dark' : 'default',
      skin: isDarkAdminArea ? 'oxide-dark' : 'oxide',
      file_picker_callback: function (callback, value, meta) {
        var options = $.extend(_.fileManagerOptions, {
          url: fn_url('elf_connector.images?security_hash=' + _.security_hash)
        });
        tinyMCE.activeEditor.windowManager.openUrl({
          url: _.current_location + '/js/lib/elfinder/elfinder.tinymce8.html',
          title: _.tr('file_browser'),
          width: 900,
          height: 480,
          onMessage: (api, message) => {
            if (message.mceAction === 'ready') {
              api.sendMessage({
                mceAction: 'init',
                options: options
              });
            } else if (message.mceAction === 'fileSelected' && message.url) {
              var url = message.url + '?' + new Date().getTime();
              callback(url);
              api.close();
            }
          }
        });
      },
      entity_encoding: 'raw'
    },
    run: function ($el, params) {
      params = params || {};

      // Not installed: accordion accordionremove anchor charmap codesample emoticons help lineheight ltr media nonbreaking pagebreak preview print quickimage quicktable rtl save searchreplace strikethrough table tablecellprops tabledelete tabledeletecol tabledeleterow tableinsertcolafter tableinsertcolbefore tableinsertrowafter tableinsertrowbefore tableprops tablerowprops visualblocks visualchars
      params.toolbar = 'undo redo | blocks fontfamily fontsize | bold italic underline forecolor backcolor | link image | numlist bullist indent outdent | align | removeformat | code fullscreen';
      if (_.area === 'C') {
        params.toolbar = params.toolbar.replace('| link image ', '').replace('code ', '');
      }
      params.directionality = _.language_direction;
      if (typeof tinymce === 'undefined') {
        $.ceEditor('state', 'loading');
        return $.getScript('js/lib/tinymce8/tinymce.min.js', function () {
          $.ceEditor('state', 'loaded');
          $el.ceEditor('run', params);
        });
      }
      if (!params.setup) {
        params.setup = function (editor) {
          editor.on('init', function () {
            if ($el.prop('disabled')) {
              $el.ceEditor('disable', true);
            }
            if (editor.id && editor.startContent) {
              $("#".concat(editor.id)).val(editor.startContent);
            }
            $el[0].defaultValue = $el.val();
          });
          editor.on('change', function () {
            tinymce.get(editor.id).save();
            $el.ceEditor('changed', editor.getContent());
          });
          editor.on('input', function () {
            tinymce.get(editor.id).save();
            $el.ceEditor('changed', editor.getContent());
          });
        };
      }
      params.target = $el[0];
      params = $.extend(this.params, params);
      tinymce.init(params);
    },
    destroy: function ($el) {
      var _this = this;
      if (typeof tinymce !== 'undefined' && typeof tinymce.get !== 'undefined') {
        if (editor.initialized) {
          tinymce.remove('#' + $el.prop('id'));
        }
      }
      this.is_destroying = true;
      setTimeout(function () {
        // TinyMCE editor disappears by timeout after destroy, even if editor is recovered
        // add delay to track it
        _this.is_destroying = false;
      }, 1);
    },
    recover: function ($el) {
      if (this.is_destroying) {
        setTimeout(function () {
          $el.ceEditor('run');
        }, 1);
      } else {
        $el.ceEditor('run');
      }
    },
    val: function ($el, value) {
      if (typeof value == 'undefined') {
        return $el.val();
      } else {
        $el.val(value);
      }
      return true;
    },
    insert: function (elm, text) {
      tinymce.editors[0].execCommand('mceInsertContent', false, text);
    },
    updateTextFields: function (elm) {
      return true;
    },
    disable: function ($el, value) {
      var state = value === true ? 'Off' : 'On';
      $('.mce-toolbar-grp').toggle();
      tinyMCE.editors[0].getBody().setAttribute('contenteditable', !value);
      $el.prop('disabled', value);
    }
  };
  $.ceEditor('handlers', editor);
})(Tygh, Tygh.$);