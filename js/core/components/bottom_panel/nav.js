import { params } from './params';
import { state } from './state';

export const nav = {
    _setActive: function (elem) {
        if (elem) {
            state.navActive = elem.data('bpNavItem');
        }
        $(state.bottomPanel).data('navActive', state.navActive);
        nav._setClass(elem);
    },

    _getNav: function () {
        $(state.bottomPanel).find(params.navItemSelector).each(function () {
            state.nav.push($(this));
        });
    },

    _setClass: function (elem) {

        if (elem) {
            $(state.nav).each(function () {
                $(this).removeClass(params.navItemActiveClass);
            });
            $(elem).addClass(params.navItemActiveClass);

        }
    },

    _addSetActiveListeners: function () {
        $(Tygh.doc).on('click', params.navItemSelector, function (e) {
            return nav._setActive($(this));
        });
    },
};
