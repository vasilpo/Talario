(function (_, $) {
  function toggleVideoState($elements, state) {
    if ($elements.length) {
      $elements.each(function () {
        const videoSource = $(this).data('ca-video-source');
        const videoSourceFunction = 'getPlayer' + videoSource + 'Messages';
        const messagingNeeds = autoplayDependenciesRoutine(this, state);
        if (typeof window[videoSourceFunction] === 'function') {
          const messages = window[videoSourceFunction](state, messagingNeeds.get_state_message, messagingNeeds.get_volume_message);
          if (messages !== undefined && messages.volume_message !== undefined) {
            this.contentWindow.postMessage(JSON.stringify(messages.volume_message), '*');
          }
          if (messages !== undefined && messages.state_message !== undefined) {
            this.contentWindow.postMessage(JSON.stringify(messages.state_message), '*');
          }
        }
      });
    }
  }
  function autoplayDependenciesRoutine(video, state) {
    let get_state_message = true;
    let get_volume_message = false;
    const allow = video.getAttribute('allow');
    const videoPlayed = video.getAttribute('data-ca-video-played');
    const videoReady = video.getAttribute('data-ready');
    if (state === 'play') {
      if (allow === undefined || !allow.includes('autoplay')) {
        get_state_message = false;
      } else if (!videoPlayed && videoReady == true) {
        get_volume_message = true;
        video.setAttribute('data-ca-video-played', true);
      } else if (videoPlayed === 'true') {
        get_state_message = false;
      } else if (videoPlayed === 'false') {
        video.setAttribute('data-ca-video-played', true);
      }
    } else if (state === 'pause') {
      if (allow !== undefined && allow.includes('autoplay') && videoPlayed) {
        video.setAttribute('data-ca-video-played', false);
      }
    }
    return {
      get_state_message,
      get_volume_message
    };
  }
  $.ceEvent('on', 'ce.dialogclose', function ($dialogContent) {
    const $videos = $('.js-video', $dialogContent);
    toggleVideoState($videos, 'pause');
  });
  $.ceEvent('on', 'ce.dialogshow', function ($dialogContent) {
    const $galleries = $('.cm-preview-wrapper');
    const $currentVideo = $('.owl-item.active .js-video', $galleries);
    toggleVideoState($currentVideo, 'pause');
  });
  $.ceEvent('on', 'ce.product_image_gallery.image_changed', function () {
    const $galleries = $('.cm-preview-wrapper');
    const $notActiveVideos = $('.owl-item:not(.active) .js-video', $galleries);
    const $currentVideo = $('.owl-item.active .js-video', $galleries);
    toggleVideoState($notActiveVideos, 'pause');
    toggleVideoState($currentVideo, 'play');
  });
  $(_.doc).on('click', '.owl-page, .owl-prev, .owl-next', function () {
    const $galleries = $('.ty-owl-previewer');
    const $notActiveVideos = $('.owl-item .js-video', $galleries);
    toggleVideoState($notActiveVideos, 'pause');
  });
  $(_.doc).on('click', '.ty-swiper-previewer__button-next, .ty-swiper-previewer__button-prev', function () {
    const $galleries = $('.ty-swiper-previewer');
    const $notActiveVideos = $('.swiper-slide .js-video', $galleries);
    toggleVideoState($notActiveVideos, 'pause');
  });
  $(_.doc).on('mouseenter', '.owl-item, .swiper-slide', function () {
    const video = $(this).find('iframe');
    if (video.length) {
      toggleVideoState(video, 'play');
    }
  });
})(Tygh, Tygh.$);