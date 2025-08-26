import React from 'react';
import ReactDOM from 'react-dom';
import $ from 'jquery';

import { createStore } from "redux";
import { Provider } from 'react-redux';

import { reducer, actions } from "./reducer";
import { dismissNotifications, getNotifications } from "./api";
import { NotificationsCenter, NotificationsCenterCounter } from "./component";

const notificationsCenterData = document.querySelector('[data-ca-notifications-center="main"]').dataset;
var langVars = {
  loading: notificationsCenterData.caNotificationsCenterTextLoading,
  showMore: notificationsCenterData.caNotificationsCenterTextShowMore,
  showLess: notificationsCenterData.caNotificationsCenterTextShowLess,
  noData: notificationsCenterData.caNotificationsCenterTextNoData,
  notifications: notificationsCenterData.caNotificationsCenterTextNotifications,
  markAllAsRead: notificationsCenterData.caNotificationsCenterTextMarkAllAsRead,
};

const NotificationsCenterStore = createStore(reducer);

const initNotificationsCenter = async function() {
  const payload = await getNotifications({
    items_per_page: NotificationsCenterStore.getState().fetchPerPage,
    page: NotificationsCenterStore.getState().fetchPage
  });

  try {
    NotificationsCenterStore.dispatch({ type: actions.START_LOAD });
    NotificationsCenterStore.dispatch({ type: actions.APPLY_DATA, payload });
    NotificationsCenterStore.dispatch({ type: actions.END_LOAD });
    NotificationsCenterStore.dispatch({ type: actions.SELECT_FIRST_SECTION });
  } catch (err) {
    NotificationsCenterStore.dispatch({ type: actions.END_LOAD });
  }

  ReactDOM.render(
    (
      <Provider store={NotificationsCenterStore}>
        <NotificationsCenterCounter />
      </Provider>
    ),
    document.querySelector('[data-ca-notifications-center-counter]')
  );

  Tygh.ceNotificationsCenterInited = true;

  $.ceEvent('trigger', 'ce.notifications_center.is_inited');
}

initNotificationsCenter();

$.ceEvent('on', 'ce.notifications_center.reloaded', function (callback, params) {
    initNotificationsCenter().then(function() {
        if (callback) {
            callback(params);
        }
    });
});

$.ceEvent('on', 'ce.notifications_center.enabled', async function () {
  ReactDOM.render(
    (
      <Provider store={NotificationsCenterStore}>
        <NotificationsCenter langVars={langVars} />
      </Provider>
    ),
    document.querySelector('[data-ca-notifications-center-root]')
  );
});


$.ceEvent('on', 'ce.notifications_center.dismiss', function (notification_id, reload) {
    if (!notification_id) {
        return;
    }

    reload = reload || false;

    dismissNotifications([notification_id]);

    if (reload) {
        $.ceEvent('trigger', 'ce.notifications_center.reloaded');
    }
});
