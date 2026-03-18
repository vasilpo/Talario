(() => {
  const t = {
      bottomPanelSelector: "#bp_bottom_panel",
      offBottomPanelSelector: "#bp_off_bottom_panel",
      bottomButtonsContainerSelector: "#bp_bottom_buttons",
      bottomButtonsSelector: "[data-bp-bottom-buttons]",
      bottomButtonsActiveClass: "bp-bottom-buttons--active",
      bottomButtonDisabledClass: "bp-bottom-button--disabled",
      onBottomPanelSelector: "#bp_on_bottom_panel",
      navItemSpecificSelector: '[data-bp-nav-item="{placeholder}"]',
      navItemSelector: "[data-bp-nav-item]",
      navItemActiveClass: "bp-nav__item--active",
      modesItemSpecificSelector: '[data-bp-modes-item="{placeholder}"]',
      modesItemSelector: "[data-bp-modes-item]",
      modesItemNotDisabledSelector: "[data-bp-modes-item]:not(.bp-modes__item--disabled)",
      modesItemActiveClass: "bp-modes__item--active",
      dropdownSelector: '[data-bp-toggle="dropdown"]',
      dropdownMenuClass: "bp-dropdown-menu",
      dropdownMenuOpenClass: "bp-dropdown-menu--open",
      dropdownMenuItemClass: "bp-dropdown-menu__item",
      htmlSelector: "html",
      htmlActiveClass: "bp-panel-active"
    },
    o = {
      html: {},
      isAdminPanel: !0,
      bottomPanel: {},
      bottomButtonsContainer: {},
      mode: "default",
      isBottomPanelOpen: !0,
      navActive: "customer",
      modesActive: "preview",
      bottomButtons: [],
      dropdowns: [],
      nav: [],
      modes: []
    },
    e = function () {
      o.html.addClass(t.htmlActiveClass);
    },
    n = function () {
      o.html.removeClass(t.htmlActiveClass);
    },
    s = function () {
      $(o.bottomButtonsContainer).addClass(t.bottomButtonsActiveClass), $(o.bottomButtons).each(function () {
        $(this).removeClass(t.bottomButtonDisabledClass + " " + t.bottomButtonDisabledClass + "-" + $(this).data("bpBottomButtons"));
      });
    },
    a = function () {
      $(o.bottomButtonsContainer).removeClass(t.bottomButtonsActiveClass), $(o.bottomButtons).each(function () {
        $(this).addClass(t.bottomButtonDisabledClass + " " + t.bottomButtonDisabledClass + "-" + $(this).data("bpBottomButtons"));
      });
    },
    i = function () {
      const t = $(".cm-notification-content.notification-content-extended");
      t.length > 0 && t.each(function (t, o) {
        $.ceNotification("position", $(o), !1);
      });
    },
    c = {
      _activate: function () {
        o.isBottomPanelOpen = !0, e(), a(), i(), c._setOpenCookie(!0);
      },
      _deactivate: function () {
        o.isBottomPanelOpen = !1, n(), s(), i(), c._setOpenCookie(!1);
      },
      _setOpenCookie: function (t) {
        $.cookie.set("pb_is_bottom_panel_open", +t);
      },
      _getCookie: function () {
        var t = $.cookie.get("pb_is_bottom_panel_open");
        o.isBottomPanelOpen = t || !0;
      },
      _addActivateListeners: function () {
        $(Tygh.doc).on("click", t.onBottomPanelSelector, function () {
          return c._activate();
        });
      },
      _addDeactivateListeners: function () {
        $(Tygh.doc).on("click", t.offBottomPanelSelector, function () {
          return c._deactivate();
        });
      }
    },
    d = {
      _setActive: function (t) {
        t && (o.navActive = t.data("bpNavItem")), $(o.bottomPanel).data("navActive", o.navActive), d._setClass(t);
      },
      _getNav: function () {
        $(o.bottomPanel).find(t.navItemSelector).each(function () {
          o.nav.push($(this));
        });
      },
      _setClass: function (e) {
        e && ($(o.nav).each(function () {
          $(this).removeClass(t.navItemActiveClass);
        }), $(e).addClass(t.navItemActiveClass));
      },
      _addSetActiveListeners: function () {
        $(Tygh.doc).on("click", t.navItemSelector, function (t) {
          return d._setActive($(this));
        });
      }
    },
    l = {
      _setActive: function (t) {
        t && (o.modesActive = t.data("bpModesItem")), $(o.bottomPanel).data("modesActive", o.modesActive), l._setClass(t);
      },
      _getButtons: function () {
        $(o.bottomPanel).find(t.modesItemSelector).each(function () {
          o.modes.push($(this));
        });
      },
      _setClass: function (e) {
        e && ($(o.modes).each(function () {
          $(this).removeClass(t.modesItemActiveClass);
        }), $(e).addClass(t.modesItemActiveClass));
      },
      _addSetActiveListeners: function () {
        $(Tygh.doc).on("click", t.modesItemNotDisabledSelector, function (t) {
          return l._setActive($(this));
        });
      }
    },
    m = function () {
      $(o.bottomPanel).find(t.dropdownSelector).each(function () {
        o.dropdowns.push($(this).parent()), $(this).on("click", function () {
          var e = $(this);
          $(o.dropdowns).each(function () {
            $(this)[0] !== e.parent()[0] && $(this).children("div").removeClass(t.dropdownMenuOpenClass);
          }), $(this).parent().children("div").toggleClass(t.dropdownMenuOpenClass);
        }), $(this).on("focusout", function (e) {
          $(e.relatedTarget).length && $(e.relatedTarget).hasClass(t.dropdownMenuItemClass) || $(o.dropdowns).each(function () {
            $(this).children("div").removeClass(t.dropdownMenuOpenClass);
          });
        }), $(Tygh.doc).on("click", "." + t.dropdownMenuItemClass, function () {
          $(o.dropdowns).each(function () {
            $(this).children("." + t.dropdownMenuClass).removeClass(t.dropdownMenuOpenClass);
          });
        });
      });
    };
  let r;
  const u = {
    init: {
      init: function () {
        r || (o.html = $(t.htmlSelector), o.bottomPanel = $(t.bottomPanelSelector), o.bottomButtonsContainer = $(t.bottomButtonsContainerSelector), o.mode = o.bottomPanel.data("bpMode"), o.isBottomPanelOpen = o.bottomPanel.data("bpIsBottomPanelOpen"), o.navActive = o.bottomPanel.data("bpNavActive"), o.modesActive = o.bottomPanel.data("bpModesActive"), o.bottomButtons = o.bottomButtonsContainer.find(t.bottomButtonsSelector), o.dropdowns = [], o.modes = [], c._getCookie(), c._addActivateListeners(), c._addDeactivateListeners(), d._getNav(), d._setActive(), d._addSetActiveListeners(), m(), $(o.bottomPanel).find(t.modesItemSelector).length && (l._getButtons(), l._setActive(), l._addSetActiveListeners()), r = !0);
      }
    }.init,
    defaults: t
  };
  $.fn.ceBottomPanel = function (t) {
    return u[t] ? u[t].apply(this, Array.prototype.slice.call(arguments, 1)) : "object" != typeof t && t ? void $.error("ty.bottom_panel: method " + t + " does not exist") : u.init.apply(this, arguments);
  }, $.ceEvent("one", "ce.commoninit", function (t) {
    t = $(t || _.doc);
    var o = $("[data-ca-bottom-pannel]", t);
    o.length && o.ceBottomPanel();
  });
})();