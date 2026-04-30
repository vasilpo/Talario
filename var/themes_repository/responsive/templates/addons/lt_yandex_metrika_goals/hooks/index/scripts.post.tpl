{$lt_yandex_metrika_goals_data = [
    "counterId" => $addons.rus_yandex_metrika.counter_number|default:"",
    "goals" => $lt_yandex_metrika_goals|default:[]
]}
<script>
    (function (_) {
        _.ltYandexMetrikaGoals = _.ltYandexMetrikaGoals || {};
        _.ltYandexMetrikaGoals.counterId = {$lt_yandex_metrika_goals_data.counterId|json_encode nofilter};
        _.ltYandexMetrikaGoals.pendingGoals = {$lt_yandex_metrika_goals_data.goals|json_encode nofilter};
    })(Tygh);
</script>
{script src="js/addons/lt_yandex_metrika_goals/goals.js"}
