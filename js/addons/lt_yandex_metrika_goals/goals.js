(function (window, _, $) {
  var storagePrefix = 'lt_yandex_metrika_goals:';
  var processedGoals = {};

  _.ltYandexMetrikaGoals = _.ltYandexMetrikaGoals || {};

  var getCounterId = function () {
    if (_.yandexMetrika && _.yandexMetrika.settings && _.yandexMetrika.settings.id) {
      return _.yandexMetrika.settings.id;
    }

    return _.ltYandexMetrikaGoals.counterId || '';
  };

  var isCounterAvailable = function () {
    return typeof ym !== 'undefined' && !!getCounterId();
  };

  var normalizeGoal = function (goal) {
    if (typeof goal === 'string') {
      return {
        name: goal,
        key: goal
      };
    }

    return {
      name: goal && goal.name ? goal.name : '',
      key: goal && goal.key ? goal.key : ''
    };
  };

  var isGoalStored = function (goalKey) {
    try {
      return window.sessionStorage
        && window.sessionStorage.getItem(storagePrefix + goalKey) === 'Y';
    } catch (err) {
      return false;
    }
  };

  var storeGoal = function (goalKey) {
    try {
      if (window.sessionStorage) {
        window.sessionStorage.setItem(storagePrefix + goalKey, 'Y');
      }
    } catch (err) {}
  };

  var appendGoals = function (goals) {
    _.ltYandexMetrikaGoals.pendingGoals = _.ltYandexMetrikaGoals.pendingGoals || [];

    $.each(goals || [], function (index, goal) {
      var normalizedGoal = normalizeGoal(goal);
      var isQueued = false;

      if (!normalizedGoal.name || !normalizedGoal.key) {
        return;
      }

      $.each(_.ltYandexMetrikaGoals.pendingGoals, function (pendingIndex, pendingGoal) {
        var normalizedPendingGoal = normalizeGoal(pendingGoal);

        if (normalizedPendingGoal.key === normalizedGoal.key) {
          isQueued = true;
          return false;
        }
      });

      if (!isQueued) {
        _.ltYandexMetrikaGoals.pendingGoals.push(normalizedGoal);
      }
    });
  };

  var reachGoal = function (goal) {
    var normalizedGoal = normalizeGoal(goal);

    if (!normalizedGoal.name || !normalizedGoal.key) {
      return true;
    }

    if (processedGoals[normalizedGoal.key] || isGoalStored(normalizedGoal.key)) {
      return true;
    }

    if (!isCounterAvailable()) {
      return false;
    }

    ym(getCounterId(), 'reachGoal', normalizedGoal.name);

    processedGoals[normalizedGoal.key] = true;
    storeGoal(normalizedGoal.key);

    return true;
  };

  var processPendingGoals = function () {
    var pendingGoals = _.ltYandexMetrikaGoals.pendingGoals || [];
    var remainingGoals = [];

    if (!pendingGoals.length) {
      return;
    }

    $.each(pendingGoals, function (index, goal) {
      if (!reachGoal(goal)) {
        remainingGoals.push(goal);
      }
    });

    _.ltYandexMetrikaGoals.pendingGoals = remainingGoals;
  };

  $.ceEvent('on', 'ce:yandexMetrika:dependencyLoaded', function () {
    processPendingGoals();
  });
  $.ceEvent('on', 'ce.commoninit', function () {
    processPendingGoals();
  });
  $.ceEvent('on', 'ce.ajaxdone', function (elms, inlineScripts, params, data) {
    if (!data || !data.lt_yandex_metrika_goals) {
      return;
    }

    appendGoals(data.lt_yandex_metrika_goals);
    processPendingGoals();
  });

  processPendingGoals();
})(window, Tygh, Tygh.$);
