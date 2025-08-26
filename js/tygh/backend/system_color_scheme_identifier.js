(function (_, $) {
  $(document).ready(function () {
    function handleMediaQueryChange(event) {
      addThemeAttrAndClasses(event.matches);
    }
    function addThemeAttrAndClasses(isDarkMode) {
      if (isDarkMode) {
        $('html').removeClass('cs-light-theme');
        $('html').addClass('cs-dark-theme');
        $('#tygh_settings').attr('data-ca-is-dark-theme', 1);
      } else {
        $('html').removeClass('cs-dark-theme');
        $('html').addClass('cs-light-theme');
        $('#tygh_settings').attr('data-ca-is-dark-theme', '');
      }
    }
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    mediaQuery.addEventListener('change', handleMediaQueryChange);
    addThemeAttrAndClasses(mediaQuery.matches);
  });
})(Tygh, Tygh.$);