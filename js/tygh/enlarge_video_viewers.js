(function (_, $) {
  $.ceEvent('on', 'ce.dialogshow', function ($dialogContent) {
    if ($dialogContent.is('#product_quick_view')) {
      return;
    }
    const $videoPreviews = $('.js-preview-for-previewers', $dialogContent);
    if (!$videoPreviews.length) {
      return;
    }
    $videoPreviews.each(function () {
      let $videoPreview = $(this);
      const $videoContainer = $($videoPreview.data('videoContainer'));
      $videoPreview = $videoPreview.replaceWith($videoContainer.html());
    });
    $dialogContent.find('.ty-video--aspect-ratio').removeClass('ty-video--aspect-ratio');
  });
})(Tygh, Tygh.$);