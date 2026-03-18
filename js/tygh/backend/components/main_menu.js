(function (_, $) {
  const breakpoints = {
    tablet: 767
  };

  // Hide triangle area on click
  $(_.doc).on('click', '[data-main-menu="subMenu"]', function () {
    $(this).addClass('main-menu--disable-triangle-area');
  });

  // Initialization of horizontal dropdown primary and secondary main menu based on the jQuery UI Menu
  function mainMenuDropdownInit($mainMenuDropdown) {
    $mainMenuDropdown.menu({
      menus: '[data-main-menu-main-type="menu"]',
      items: '[data-main-menu="item"]',
      classes: {
        'ui-menu': 'main-menu--ui-menu',
        'ui-menu-divider': 'main-menu__item--ui-menu-divider',
        'ui-menu-icon': 'main-menu__accordion-heading-icon--ui-menu-icon',
        'ui-menu-item': 'main-menu__item--ui-menu-item',
        'ui-menu-item-wrapper': 'main-menu__link-wrapper--ui-menu-item-wrapper'
      },
      position: {
        using: function (props, feedback) {
          const bottomPanelHeight = parseInt(window.getComputedStyle(document.documentElement).getPropertyValue('--bp-bottom-panel-height'), 10);
          const parentOffsetTop = ($(this).offsetParent().offset() || {
            top: 0,
            left: 0
          }).top;
          const leftOffset = 4;
          $(this).toggleClass('main-menu--dropdown-flipped', feedback.vertical === 'bottom');
          // Fix bugs with bottom panel height and window overflow
          $(this).css({
            top: feedback.element.top + feedback.element.height > $(window).scrollTop() + $(window).height() - bottomPanelHeight ? feedback.target.top - parentOffsetTop + feedback.target.height - feedback.element.height : props.top + parentOffsetTop < $(window).scrollTop() ? $(window).scrollTop() - parentOffsetTop : props.top + 'px',
            left: props.left + (_.language_direction === 'rtl' ? -1 * leftOffset : 1 * leftOffset) + 'px'
          });
        }
      },
      select: function (event, ui) {
        if (Modernizr.touchevents && ui.item.data('mainMenuItemLevel') === 1) {
          event.preventDefault();
          return;
        }
      },
      blur: function (event, ui) {
        if (event.type === 'menublur') {
          const $subMenu = $('[data-main-menu="subMenu"]', ui.item);
          if ($subMenu.length > 0 && $subMenu.attr('aria-expanded') === 'false') {
            $subMenu.removeClass('main-menu--disable-triangle-area');
          }
        }
      }
    }).menu('instance').delay = 15;
  }

  // Collapsed destroy
  function mainMenuCollapsedDestroy($mainMenuCollapsed) {
    if ($mainMenuCollapsed.length === 0) {
      return;
    }
    $mainMenuCollapsed.each(function () {
      // Destroying a Collapse does not remove inline styles
      $('[data-main-menu="linkNestedMenu"]').each(function () {
        $(this).removeClass('collapsed');
      });
      $('[data-main-menu="subMenu"]').each(function () {
        const subMenuElem = $(this)[0];
        ['height'].forEach(styleProp => {
          subMenuElem.style.removeProperty(styleProp);
        });
        if (subMenuElem.style.length === 0) {
          subMenuElem.removeAttribute('style');
        }
      });
    });
  }

  // Dropdowns initilization
  function mainMenuDropdownsInit($mainMenuDropdowns) {
    if ($mainMenuDropdowns.length === 0) {
      return;
    }
    $mainMenuDropdowns.each(function () {
      if (typeof $(this).menu('instance') !== 'undefined') {
        return;
      }
      mainMenuDropdownInit($(this));
    });
  }

  // Dropdowns destroy
  function mainMenuDropdownsDestroy($mainMenuDropdowns) {
    if ($mainMenuDropdowns.length === 0) {
      return;
    }
    $mainMenuDropdowns.each(function () {
      if (typeof $(this).menu('instance') === 'undefined') {
        return;
      }
      $(this).menu('destroy');

      // Destroying a jQuery UI Menu does not remove inline styles
      $('[data-main-menu="subMenu"]').each(function () {
        const subMenuElem = $(this)[0];
        ['top', 'left', 'right', 'bottom'].forEach(styleProp => {
          subMenuElem.style.removeProperty(styleProp);
        });
        if (subMenuElem.style.length === 0) {
          subMenuElem.removeAttribute('style');
        }
      });
    });
  }
  function toggleMainMenu($menuContainer, $mainMenus, isDropdown) {
    if ($mainMenus.length === 0 || $menuContainer.length === 0) {
      return;
    }
    // Init/destroy main menu dropdowns
    if (isDropdown) {
      mainMenuCollapsedDestroy($mainMenus);
      mainMenuDropdownsInit($mainMenus);
    } else {
      mainMenuDropdownsDestroy($mainMenus);
    }

    // Toggle <html> tag class
    $('html').toggleClass('cs-main-menu-collapse', !isDropdown).toggleClass('cs-main-menu-dropdown', isDropdown);

    // Toggle animated class
    $('html').addClass('cs-main-menu-is-animated');
    setTimeout(() => {
      $('html').removeClass('cs-main-menu-is-animated');
    }, parseInt(window.getComputedStyle($('.cs-main-menu')[0]).getPropertyValue('--cs-main-menu-transition-duration'), 10));
    if ($(window).width() > breakpoints.tablet) {
      $menuContainer.attr('data-menu-default-type', isDropdown ? 'dropdown' : 'collapse');

      // Update user menu type on backend
      $.ceAjax('request', fn_url(isDropdown ? $menuContainer.data('menuToggleDropdownHref') : $menuContainer.data('menuToggleCollapseHref')), {
        hidden: true
      });
    }
    $.ceEvent('trigger', 'ce.main_menu.toggle', [$menuContainer, $mainMenus, isDropdown]);
  }
  function removePrerenderStylesAndClasses($menuContainer) {
    $('[data-main-menu="subMenu"]', $menuContainer).each(function () {
      $(this)[0].style.display = '';
    });
    $('[data-main-menu="nestedAccordionHeading"]', $menuContainer).each(function () {
      $(this).removeClass('main-menu__link-wrapper--ui-menu-item-wrapper');
    });
    $('[data-main-menu="linkLinkWrapper"]', $menuContainer).each(function () {
      $(this).removeClass('main-menu__link-wrapper--ui-menu-item-wrapper');
    });
  }
  function initMainMenu($menuContainer, $mainMenus) {
    const isDropdown = $('html').hasClass('cs-main-menu-dropdown');
    const $mainMenuDropdownsDefault = $('[data-main-menu="rootMenu"]');
    const $menuContainerDefault = $('[data-menu="main"]');
    if (typeof $mainMenus === 'undefined' && $mainMenuDropdownsDefault.length === 0) {
      return;
    }
    const $mainMenuDropdowns = typeof $mainMenus === 'undefined' ? $mainMenuDropdownsDefault : $mainMenus;
    const $menuContainerLocal = typeof $menuContainer === 'undefined' ? $menuContainerDefault : $menuContainer;

    // Switch the menu to dropdown on desktop after it switches to collapsible on mobile
    if ($menuContainerLocal.attr('data-menu-default-type') === 'dropdown' && !isDropdown && $(window).width() > breakpoints.tablet) {
      toggleMainMenu($menuContainerLocal, $mainMenuDropdowns, true);
      // Always switch to collapse menu on mobile
    } else if ($(window).width() <= breakpoints.tablet) {
      removePrerenderStylesAndClasses($menuContainerLocal);
      toggleMainMenu($menuContainerLocal, $mainMenuDropdowns, false);
      // Initialization jQuery UI Menu when dropdown menu enabled on desktop
    } else if ($menuContainerLocal.attr('data-menu-default-type') === 'dropdown' || isDropdown) {
      mainMenuDropdownsInit($mainMenuDropdowns);
    }
  }
  const initMainMenuDebounced = $.debounce(initMainMenu);
  $.ceEvent('on', 'ce.commoninit', function (context) {
    const $mainMenuDropdowns = $('html.cs-main-menu-dropdown [data-main-menu="rootMenu"]', context);
    const $menuContainer = $('[data-menu="main"]', context);
    if ($mainMenuDropdowns.length === 0 || $menuContainer.length === 0) {
      return;
    }
    initMainMenu($menuContainer, $mainMenuDropdowns);
  });
  $(window).on('resize', () => {
    initMainMenuDebounced();
  });

  // Event for switch to dropdown menu
  $(document).on('click', 'html.cs-main-menu-collapse [data-main-menu-toggle="btn"]', function () {
    toggleMainMenu($('[data-menu="main"]'), $('html.cs-main-menu-collapse [data-main-menu="rootMenu"]'), true);
  });

  // Event for switch to collapse menu
  $(document).on('click', 'html.cs-main-menu-dropdown [data-main-menu-toggle="btn"]', function () {
    toggleMainMenu($('[data-menu="main"]'), $('html.cs-main-menu-dropdown [data-main-menu="rootMenu"]'), false);
  });

  // Attach a handler to the main menu accordion expand based on the Bootstrap Coollapse
  $(document).on('click.collapse.data-api', 'html.cs-main-menu-collapse [data-main-menu-link-type="accordionToggle"]', function (e) {
    e.preventDefault();
    const $accordionToggle = $(this);
    const target = $accordionToggle.attr('href').replace(/.*(?=#[^\s]+$)/, '');
    if (target === '') {
      return;
    }
    const option = $(target).data('collapse') ? 'toggle' : $accordionToggle.data();
    $accordionToggle[$(target).hasClass('in') ? 'addClass' : 'removeClass']('collapsed');
    $(target).collapse(option);
  });
})(Tygh, Tygh.$);