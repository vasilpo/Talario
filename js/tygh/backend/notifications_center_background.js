(function (_, $) {
  $(function () {
    // Background init: check notifications and attach events
    function initNotificationsCenterBackground($main, $context) {
      checkNotifications($main, setCounter);
      attachEvents($main, $context);
      $main.data('caNotificationsCenterBackgroundIsInited', 1);
    }

    // Init: load notifications center script, initialization, and open
    function initNotificationsCenterMain(triggerType, $main, callback) {
      const trigger = triggerType === 'reloaded' ? 'ce.notifications_center.reloaded' : 'ce.notifications_center.enabled';
      if (_.ceNotificationsCenterInited) {
        $.ceEvent('trigger', trigger, [callback, $main]);
        setCounter($main, {
          notifications_center: {
            unread_notifications_count: ''
          }
        });
      } else {
        $.getScript("js/tygh/notifications_center.js?ver=".concat(encodeURIComponent(_.product_version)), function () {
          $.ceEvent('one', 'ce.notifications_center.is_inited', function () {
            $.ceEvent('trigger', trigger, [callback, $main]);
            setCounter($main, {
              notifications_center: {
                unread_notifications_count: ''
              }
            });
          });
        });
      }
    }

    // Open notifications center dropdown
    function openNotificationsCenter($main) {
      if (!$main.length || $main.hasClass('open')) {
        return;
      }
      $('.notifications-center__opener-btn', $main).trigger('click');
    }

    // Set notifications center counter
    function setCounter($main, data) {
      if (data.notifications_center.unread_notifications_count === '') {} else if (!data || !data.notifications_center || !data.notifications_center.unread_notifications_count || data.notifications_center.unread_notifications_count === 0) {
        return;
      }
      $('[data-ca-notifications-center-counter]', $main).attr('data-ca-notifications-center-count', data.notifications_center.unread_notifications_count);
    }

    // Send ajax-request for check notifications
    function checkNotifications($main, callback) {
      $.ceAjax('request', fn_url('notifications_center.manage'), {
        hidden: true,
        data: {
          items_per_page: 10,
          page: 1
        },
        callback: function (response) {
          if (!callback) {
            return;
          }
          callback($main, response);
        }
      });
    }

    // Attach events
    function attachEvents($main, $context) {
      const $openerBtn = $('.notifications-center__opener-btn', $main);
      if ($openerBtn.length) {
        $openerBtn.on('click', initNotificationsCenterMain);
      }
      const $reloadedForm = $('[data-ca-notifications-center-reloaded="form"]', $context);
      if ($reloadedForm.length) {
        $.ceEvent('on', "ce.formajaxpost_".concat($reloadedForm.prop('name')), function () {
          initNotificationsCenterMain('reloaded', $main, openNotificationsCenter);
        });
      }
    }

    // Attach global events
    $.ceEvent('on', 'ce.commoninit', function ($context) {
      const $main = $('[data-ca-notifications-center="main"]', $context);
      if (!$main.length || _.area !== 'A' || $main.data('caNotificationsCenterBackgroundIsInited')) {
        return;
      }
      initNotificationsCenterBackground($main, $context);
    });
  });
})(Tygh, Tygh.$);