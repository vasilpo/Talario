import { Tygh } from "../..";
import $ from "jquery";

const _ = Tygh;

var container;
var timers = {};
var delay = 0;

let icon_live_edit = '<span class="ty-icon cm-icon-live-edit icon-live-edit ty-icon-live-edit"></span>';

function _duplicateNotification(key) {
    var dups = $('div[data-ca-notification-key=' + key + ']');
    if (dups.length) {

        if (!_addToDialog(dups)) {
            dups.fadeTo('fast', 0.5).fadeTo('fast', 1).fadeTo('fast', 0.5).fadeTo('fast', 1);
        }

        // Restart autoclose timer
        if (timers[key]) {
            clearTimeout(timers[key]);
            methods.close(dups, true);
        }

        return true;
    }

    return false;
}

function _closeNotification(notification) {
    if (notification.find('.cm-notification-close-ajax').length) {
        $.ceAjax('request', fn_url('notifications.close?notification_id=' + notification.data('caNotificationKey')), {
            hidden: true
        });
    }

    notification.fadeOut('fast', function () {
        notification.remove();
    });

    if (notification.hasClass('cm-notification-content-extended')) {
        var overlay = $('.ui-widget-overlay[data-ca-notification-key=' + notification.data('caNotificationKey') + ']');
        if (overlay.length) {
            overlay.fadeOut('fast', function () {
                overlay.remove();
            });
        }
    }
}

function _processTranslation(text) {
    if (_.live_editor_mode && text.indexOf('[lang') != -1) {
        text = '<var class="live-edit-wrap">' + icon_live_edit + '<var class="cm-live-edit live-edit-item" data-ca-live-edit="langvar::' + text.substring(text.indexOf('=') + 1, text.indexOf(']')) + '">' + text.substring(text.indexOf(']') + 1, text.lastIndexOf('[')) + '</var></var>';
    }

    return text;
}

function _pickFromDialog(event) {
    var nt = $('.cm-notification-content', $(event.target));
    if (nt.length) {
        if (!_addToDialog(nt)) {
            container.append(nt);
        }
    }
    return true;
}

function _addToDialog(notification) {
    var dlg = $.ceDialog('get_last');
    if (dlg.length) {
        $('.cm-notification-container-dialog', dlg).prepend(notification);
        dlg.off('dialogclose', _pickFromDialog);
        dlg.on('dialogclose', _pickFromDialog);
        return true;
    }
    return false;
}

export const methods = {
    show: function (data, key) {
        if (!key) {
            key = $.crc32(data.message);
        }

        if (typeof (data.message) == 'undefined') {
            return false;
        }

        if (_duplicateNotification(key)) {
            return true;
        }

        data.message = _processTranslation(data.message);
        data.title = _processTranslation(data.title);

        // Popup message in the screen center - should be only one at time
        if (data.type == 'I') {
            let zIndexPopup = 1010;
            if (_.area === 'A') {
                zIndexPopup = 1100; // dialog_opener/index.js: _init: zindex = 1099 + 1
            }

            $('.cm-notification-content.cm-notification-content-extended').each(function () {
                methods.close($(this), false);
            });

            $(_.body).append(
                '<div class="ui-widget-overlay" style="z-index:' + zIndexPopup + '" data-ca-notification-key="' + key + '"></div>'
            );

            var $notification = $('<div class="cm-notification-content cm-notification-content-extended notification-content-extended ' + (data.message_state == "I" ? ' cm-auto-hide' : '') + '" data-ca-notification-key="' + key + '">' +
                '<h1 class="cm-notification-content-extended-title">' + data.title + '<span class="cm-notification-close close"></span></h1>' +
                '<div class="notification-body-extended">' +
                data.message +
                '</div>' +
                '</div>');

            methods.position($notification);

        } else {
            var n_class = 'alert';
            var b_class = '';

            if (data.type == 'N') {
                n_class += ' alert-success';
            } else if (data.type == 'W') {
                n_class += ' alert-warning';
            } else if (data.type == 'S') {
                n_class += ' alert-info';
            } else {
                n_class += ' alert-error';
            }

            if (data.message_state == 'I') {
                n_class += ' cm-auto-hide';
            } else if (data.message_state == 'S') {
                b_class += ' cm-notification-close-ajax';
            }

            var $notification = $('<div class="cm-notification-content notification-content ' + n_class + '" data-ca-notification-key="' + key + '">' +
                '<button type="button" class="close cm-notification-close ' + b_class + '" data-dismiss="alert">&times;</button>' +
                '<strong>' + data.title + '</strong>' + data.message +
                '</div>');

            if (!_addToDialog($notification)) {
                container.append($notification);
            }
        }

        $.ceEvent('trigger', 'ce.notificationshow', [$notification]);

        if (data.message_state == 'I') {
            methods.close($notification, true);
        }
    },

    showMany: function (data) {
        for (var key in data) {
            methods.show(data[key], key);
        }
    },

    closeAll: function () {
        var notifications = container.find('.cm-notification-content');
        var dlg = $.ceDialog('get_last');
        if (dlg.length) {
            notifications = notifications.add(dlg.find('.cm-notification-content'));
        }

        notifications.each(function () {
            var self = $(this);
            if (!self.hasClass('cm-notification-close-ajax')) {
                methods.close(self, false);
            }
        })
    },

    close: function (notification, delayed) {
        if (delayed == true) {
            if (delay === 0) { // do not auto-close
                return true;
            }

            timers[notification.data('caNotificationKey')] = setTimeout(function () {
                methods.close(notification, false);
            }, delay);

            return true;
        }

        _closeNotification(notification);
    },

    init: function () {
        delay = _.notice_displaying_time * 1000;
        container = $('.cm-notification-container');

        $(_.doc).on('click', '.cm-notification-close', function () {
            methods.close($(this).parents('.cm-notification-content:first'), false);
        })

        container.find('.cm-auto-hide').each(function () {
            methods.close($(this), true);
        });


        $('.cm-notification-content.notification-content-extended').each((id, elm) => {
            let $notification = $(elm);
            methods.position($notification)
        });
    },

    position: ($notification, is_append) => {
        is_append = (is_append === false) ? false : true;
        const view_height = $.getWindowSizes().view_height;
        const window_height = $(window).height();
        const is_notification_mobile_bottom = $(window).width() > 767 ? false : $('html').hasClass('dialog-mobile-bottom');
        const notification_offset = is_notification_mobile_bottom ? 20 : 300;
        const notification_without_body_height = $.contains(document, $notification[0])
            ? Math.ceil(
                ($notification.outerHeight() - $notification.innerHeight())
                + ($('.cm-notification-content-extended-title', $notification).outerHeight())
                + ($('.cm-product-notification-buttons', $notification).outerHeight())
                + ($('.cm-product-notification-body', $notification).innerHeight()
                    - $('.cm-product-notification-body', $notification).height()
                )
            ) : 143;
        const bottomPanelHeight = (parseInt($('html').css('--bp-bottom-panel-height'), 10) || 0);
        const maxHeight = view_height
                - notification_offset
                - (is_notification_mobile_bottom ? notification_without_body_height : 0)
                - bottomPanelHeight;

        // .ty-product-notification__body: min-height: 72px;
        $('.cm-notification-max-height', $notification).css({
            'max-height': maxHeight < 72 ? 72 : maxHeight,
        });

        if (is_append) {
            $(_.body).append($notification);
        }

        const notification_with_margin_height = Math.floor($notification.outerHeight(true))
            + bottomPanelHeight;

        $notification.css('top', is_notification_mobile_bottom
            ? (notification_with_margin_height < window_height
                ? window_height - notification_with_margin_height
                : notification_offset)
            : (view_height - bottomPanelHeight) / 2 - ($notification.height() / 2));
    }
};

/**
 * Notifications
 * @param {JQueryStatic} $
 */
export const ceNotificationInit = function ($) {
    $.ceNotification = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else {
            $.error('ty.notification: method ' + method + ' does not exist');
        }
    };
}
