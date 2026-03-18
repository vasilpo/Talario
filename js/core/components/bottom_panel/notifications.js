export const notifications = {
    _position: function () {
        const $notificationContentExtended = $('.cm-notification-content.notification-content-extended');
        if ($notificationContentExtended.length > 0) {
            $notificationContentExtended.each(function (id, elm) {
                $.ceNotification('position', $(elm), false);
            });
        }
    },
};
