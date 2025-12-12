(function (_, $) {
  // Set element info
  let elementInfo = {};
  setElementInfo(_.doc);
  if (!elementInfo.$actionsPanel.length || !elementInfo.$actionsTitle.length || !elementInfo.$contentHeading.length || !elementInfo.$contentTitle.length) {
    return;
  }
  function toggleActionsTitle(elementInfo) {
    elementInfo.$contentTitle.toggleClass('visibility-hidden', !isInViewport(elementInfo));
    elementInfo.$actionsTitle.toggleClass('visibility-hidden', isInViewport(elementInfo));
  }
  function isInViewport(elementInfo) {
    return elementInfo.$contentHeading.outerHeight() + parseInt(elementInfo.$actionsPanel.css('marginTop')) + parseInt(elementInfo.$actionsPanel.css('marginBottom')) - elementInfo.contentTitleBottomIndent > $(window).scrollTop();
  }

  // Constructor function for element info
  function ElementInfo($context) {
    const contentTitleBottomIndentCoefficient = 8;
    const _$actionsPanel = $('#actions_panel', $context);
    const _$contentHeading = $('[data-ca-mainbox="contentHeading"]', $context);
    const _$contentTitle = $('[data-ca-mainbox="contentHeadingTitle"]', _$contentHeading);
    const _contentTitleFontSize = parseInt(_$contentTitle.css('font-size'));
    this.$actionsPanel = _$actionsPanel;
    this.$actionsTitle = $('[data-ca-mainbox="navActionsTitle"]', _$actionsPanel);
    this.$contentHeading = _$contentHeading;
    this.$contentTitle = _$contentTitle;
    this.contentTitleFontSize = _contentTitleFontSize;
    this.contentTitleBottomIndent = _contentTitleFontSize / contentTitleBottomIndentCoefficient;
  }
  function setElementInfo($context) {
    elementInfo = new ElementInfo($context);
  }

  // Returns a function, that, when invoked, will only be triggered at most once
  // during a given window of time. Normally, the throttled function will run
  // as much as it can, without ever going more than once per `wait` duration;
  // but if you'd like to disable the execution on the leading edge, pass
  // `{leading: false}`. To disable execution on the trailing edge, ditto.
  function throttle(func, wait, options) {
    var context, args, result;
    var timeout = null;
    var previous = 0;
    if (!options) options = {};
    var later = function () {
      previous = options.leading === false ? 0 : Date.now();
      timeout = null;
      result = func.apply(context, args);
      if (!timeout) context = args = null;
    };
    return function () {
      var now = Date.now();
      if (!previous && options.leading === false) previous = now;
      var remaining = wait - (now - previous);
      context = this;
      args = arguments;
      if (remaining <= 0 || remaining > wait) {
        if (timeout) {
          clearTimeout(timeout);
          timeout = null;
        }
        previous = now;
        result = func.apply(context, args);
        if (!timeout) context = args = null;
      } else if (!timeout && options.trailing !== false) {
        timeout = setTimeout(later, remaining);
      }
      return result;
    };
  }
  ;

  // Events
  $.ceEvent('on', 'ce.commoninit', throttle(() => {
    setElementInfo(_.doc);
    toggleActionsTitle(elementInfo);
  }, 200));
  $(window).on('scroll', throttle(() => toggleActionsTitle(elementInfo), 200));
})(Tygh, Tygh.$);