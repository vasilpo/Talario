(function (_, $) {
  $.ceEvent('on', 'ce.commoninit', function ($context) {
    const $containers = $('.js-draganddrop', $context);
    if (!$containers.length) {
      return;
    }
    $containers.each(function () {
      const $container = $(this);
      const sortableItem = $container.data('draganddropItem');
      const sortableField = $container.data('draganddropSortableField');
      const $deletedVideosListField = $container.find('input.js-deleted-videos');
      $container.on('click', '.btn-add, .ty-icon-clon', function () {
        const $items = $(sortableItem, $container);
        $items.each(function (index) {
          const $sortableField = $(sortableField, this);
          if (!$sortableField.val()) {
            $sortableField.val(index);
          }
        });
      });
      $container.on('click', '.js-delete-video', function () {
        const $item = $(this).closest(sortableItem);
        const videoId = $item.find('input[name*="[video_id]"]').val() != undefined ? $item.find('input[name*="[video_id]"]').val() : $item.find('input[data-ca-input-name*="[video_id]"]').val();
        let currentDeletedItems = $deletedVideosListField.val() ? $deletedVideosListField.val().split(',') : [];
        let deleted = $item.hasClass('cm-delete-row');
        if (videoId && !deleted) {
          currentDeletedItems.push(videoId);
        } else if (videoId && deleted) {
          const index = currentDeletedItems.indexOf(videoId);
          currentDeletedItems.splice(index, 1);
        }
        $deletedVideosListField.val(currentDeletedItems.join(','));
      });
      $container.sortable({
        tolerance: 'pointer',
        containment: $container,
        cursor: 'move',
        forceHelperSize: true,
        axis: 'y',
        items: sortableItem,
        update: function (event, uiData) {
          const $items = $(sortableItem, $container);
          $items.each(function (index) {
            const $sortableField = $(sortableField, this);
            $sortableField.val(index);
          });
        }
      });
      $container.on('change', 'select[name*="[source]"], input[name*="[video_url_id]"]', function () {
        const $item = $(this).closest('.draganddrop-item');
        const videoUrlId = $item.find('input[name*="[video_url_id]"]').val();
        const source = $item.find('select[name*="[source]"]').val();
        if (!videoUrlId || !source) {
          return;
        }
        $.ceAjax('request', fn_url('products.get_video_preview'), {
          method: 'get',
          data: {
            video_url_id: videoUrlId,
            source: source
          },
          callback: function (response) {
            if (response.image_url) {
              $item.find('td').eq(3).html("<a href=\"".concat(response.video_url, "\" target=\"_blank\">\n                                    <img src=\"").concat(response.image_url, "\" class=\"video-preview\">\n                                </a>"));
            } else if (response.error) {
              $item.find('input[name*="[video_url_id]"]').val('');
              $item.find('select[name*="[source]"]').val('');
            }
          }
        });
      });
      $container.on('click', '.btn-add', function () {
        const $item = $(this).closest('.draganddrop-item');
        const newItemIdPrefix = $item.attr('id') + '_';
        const $newItem = $("[id^=\"".concat(newItemIdPrefix, "\"]")).first();
        $newItem.find('.js-video-preview').html('<image></image>');
      });
    });
  });
})(Tygh, Tygh.$);