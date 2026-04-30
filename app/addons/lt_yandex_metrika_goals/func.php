<?php

// phpcs:disable PSR1.Files.SideEffects

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

use Tygh\Enum\SiteArea;
use Tygh\Enum\YesNo;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access' . ' denied');

const LT_YANDEX_METRIKA_GOALS_REGISTRATION_SUCCESS = 'registration_success';
const LT_YANDEX_METRIKA_GOALS_PARTNER_REGISTRATION_SUCCESS = 'partner_registration_success';
const LT_YANDEX_METRIKA_GOALS_SITE_SEARCH = 'site_search';
const LT_YANDEX_METRIKA_GOALS_PROFILE_ADD_REQUEST = 'lt_yandex_metrika_goals_profile_add_request';
const LT_YANDEX_METRIKA_GOALS_PARTNER_APPLICATION_REQUEST = 'lt_yandex_metrika_goals_partner_application_request';

/**
 * Hook handler for `update_profile`.
 *
 * Queues the user registration goal after a storefront profile has been created.
 *
 * @param string $action            Profile action
 * @param array  $user_data         Saved user data
 * @param array  $current_user_data Previous user data
 *
 * @return void
 *
 * @see fn_update_user()
 */
function fn_lt_yandex_metrika_goals_update_profile($action, array $user_data, array $current_user_data): void
{
    if ($action !== 'add' || !SiteArea::isStorefront(AREA)) {
        return;
    }

    $user_id = !empty($user_data['user_id'])
        ? (int) $user_data['user_id']
        : (!empty($current_user_data['user_id']) ? (int) $current_user_data['user_id'] : 0);
    if ($user_id <= 0) {
        return;
    }

    fn_lt_yandex_metrika_goals_queue_user_registration_goal($user_id, false);
}

/**
 * Hook handler for `get_products_post`.
 *
 * Queues the site search goal for successful storefront product search requests.
 *
 * @param array       $products  Found products
 * @param array       $params    Product search params
 * @param string|null $lang_code Language code
 *
 * @return void
 *
 * @see fn_get_products()
 */
function fn_lt_yandex_metrika_goals_get_products_post(array &$products, array &$params, &$lang_code): void
{
    if (!fn_lt_yandex_metrika_goals_is_trackable_search($params)) {
        return;
    }

    fn_lt_yandex_metrika_goals_queue_goal(
        LT_YANDEX_METRIKA_GOALS_SITE_SEARCH,
        fn_lt_yandex_metrika_goals_get_search_goal_key($params),
        true
    );
}

/**
 * Hook handler for `send_form`.
 *
 * Marks a successfully processed partner application form for the next rendered page.
 *
 * @param array  $page_data   Form page data
 * @param array  $form_values Submitted form values
 * @param bool   $result      Form processing result
 * @param string $from        Sender setting
 * @param string $sender      Reply-to email
 * @param array  $attachments Uploaded files
 * @param bool   $is_html     Whether e-mail is HTML
 * @param string $subject     E-mail subject
 *
 * @return void
 *
 * @see fn_send_form()
 */
function fn_lt_yandex_metrika_goals_send_form(
    array &$page_data,
    array &$form_values,
    &$result,
    &$from,
    &$sender,
    array &$attachments,
    &$is_html,
    &$subject
): void {
    $page_id = !empty($page_data['page_id']) ? (int) $page_data['page_id'] : 0;
    $is_partner_application_page = fn_lt_yandex_metrika_goals_is_partner_application_page($page_id);

    if (!SiteArea::isStorefront(AREA) || $result !== true || !$is_partner_application_page) {
        return;
    }

    if (fn_lt_yandex_metrika_goals_has_invalid_required_form_fields($page_data, $form_values)) {
        unset(Tygh::$app['session'][LT_YANDEX_METRIKA_GOALS_PARTNER_APPLICATION_REQUEST]);

        return;
    }

    $goal_key = LT_YANDEX_METRIKA_GOALS_PARTNER_REGISTRATION_SUCCESS
        . ':form:' . $page_id . ':'
        . md5(uniqid('', true));

    Tygh::$app['session'][LT_YANDEX_METRIKA_GOALS_PARTNER_APPLICATION_REQUEST] = [
        'page_id' => $page_id,
        'time'    => TIME,
        'key'     => $goal_key,
    ];
}

/**
 * Checks whether the current product loading context is a storefront search.
 *
 * @param array $params Product search params
 *
 * @return bool
 */
function fn_lt_yandex_metrika_goals_is_trackable_search(array $params): bool
{
    if (($params['area'] ?? AREA) !== SiteArea::STOREFRONT) {
        return false;
    }

    $dispatch = (string) ($params['dispatch'] ?? '');
    if ($dispatch === '') {
        $controller = (string) Registry::get('runtime.controller');
        $mode = (string) Registry::get('runtime.mode');
        $dispatch = ($controller !== '' && $mode !== '') ? $controller . '.' . $mode : '';
    }

    if ($dispatch !== 'products.search') {
        return false;
    }

    $has_search_performed = !empty($params['search_performed']) && $params['search_performed'] !== 'N';
    $has_features_hash = !empty($params['features_hash']);

    if (!$has_search_performed && !$has_features_hash) {
        return false;
    }

    return true;
}

/**
 * Marks a storefront profile POST as a new customer registration attempt.
 *
 * @param array $auth Current auth data
 *
 * @return void
 */
function fn_lt_yandex_metrika_goals_mark_profile_add_request(array $auth): void
{
    if (!SiteArea::isStorefront(AREA)) {
        return;
    }

    if (!empty($auth['user_id'])) {
        unset(Tygh::$app['session'][LT_YANDEX_METRIKA_GOALS_PROFILE_ADD_REQUEST]);

        return;
    }

    if (empty($_REQUEST['user_data']) || !is_array($_REQUEST['user_data'])) {
        return;
    }

    Tygh::$app['session'][LT_YANDEX_METRIKA_GOALS_PROFILE_ADD_REQUEST] = YesNo::YES;
}

/**
 * Queues the registration goal from the profile controller after a successful POST.
 *
 * @param array $auth Current auth data
 *
 * @return void
 */
function fn_lt_yandex_metrika_goals_queue_profile_registration_from_controller(array $auth): void
{
    if (!SiteArea::isStorefront(AREA)) {
        return;
    }

    if (empty(Tygh::$app['session'][LT_YANDEX_METRIKA_GOALS_PROFILE_ADD_REQUEST])) {
        return;
    }

    unset(Tygh::$app['session'][LT_YANDEX_METRIKA_GOALS_PROFILE_ADD_REQUEST]);

    $profile_updated = Registry::ifGet('runtime.profile_updated', YesNo::NO) === YesNo::YES;
    $user_id = !empty($auth['user_id']) ? (int) $auth['user_id'] : 0;

    if (!$profile_updated || $user_id <= 0) {
        return;
    }

    fn_lt_yandex_metrika_goals_queue_user_registration_goal($user_id, false);
}

/**
 * Queues the registration goal on the first rendered page after profile registration.
 *
 * @param array  $auth Current auth data
 * @param string $mode Current profiles controller mode
 *
 * @return void
 */
function fn_lt_yandex_metrika_goals_queue_recent_profile_registration_from_page(array $auth, string $mode): void
{
    if (!SiteArea::isStorefront(AREA)) {
        return;
    }

    $user_id = !empty($auth['user_id']) ? (int) $auth['user_id'] : 0;
    if ($user_id <= 0) {
        return;
    }

    $referer = isset($_SERVER['HTTP_REFERER']) ? (string) $_SERVER['HTTP_REFERER'] : '';
    $is_registration_landing = $mode === 'success_add' || strpos($referer, 'dispatch=profiles.add') !== false;
    if (!$is_registration_landing) {
        return;
    }

    $created_at = (int) db_get_field('select timestamp from ?:users where user_id = ?i', $user_id);
    $is_recent_user = $created_at > 0 && (TIME - $created_at) <= 300;

    if (!$is_recent_user) {
        return;
    }

    fn_lt_yandex_metrika_goals_queue_user_registration_goal($user_id, true);
}

/**
 * Queues the partner application goal on the successful form result page.
 *
 * @return void
 */
function fn_lt_yandex_metrika_goals_queue_partner_application_from_page(): void
{
    $page_id = !empty($_REQUEST['page_id']) ? (int) $_REQUEST['page_id'] : 0;
    if (!SiteArea::isStorefront(AREA) || !fn_lt_yandex_metrika_goals_is_partner_application_page($page_id)) {
        return;
    }

    $sent = isset($_REQUEST['sent']) ? (string) $_REQUEST['sent'] : '';
    if ($sent !== YesNo::YES) {
        unset(Tygh::$app['session'][LT_YANDEX_METRIKA_GOALS_PARTNER_APPLICATION_REQUEST]);

        return;
    }

    if (
        empty(Tygh::$app['session'][LT_YANDEX_METRIKA_GOALS_PARTNER_APPLICATION_REQUEST])
        || !is_array(Tygh::$app['session'][LT_YANDEX_METRIKA_GOALS_PARTNER_APPLICATION_REQUEST])
    ) {
        return;
    }

    $request_data = Tygh::$app['session'][LT_YANDEX_METRIKA_GOALS_PARTNER_APPLICATION_REQUEST];
    unset(Tygh::$app['session'][LT_YANDEX_METRIKA_GOALS_PARTNER_APPLICATION_REQUEST]);

    $request_page_id = !empty($request_data['page_id']) ? (int) $request_data['page_id'] : 0;
    $request_time = !empty($request_data['time']) ? (int) $request_data['time'] : 0;
    $is_recent_request = $request_page_id === $page_id && $request_time > 0 && (TIME - $request_time) <= 300;

    if (!$is_recent_request || empty($request_data['key'])) {
        return;
    }

    fn_lt_yandex_metrika_goals_queue_goal(
        LT_YANDEX_METRIKA_GOALS_PARTNER_REGISTRATION_SUCCESS,
        (string) $request_data['key'],
        true
    );
}

/**
 * Gets the configured partner application form page.
 *
 * @return int
 */
function fn_lt_yandex_metrika_goals_get_partner_application_page_id(): int
{
    return (int) Registry::get('addons.sd_design_changes.apply_vendor_page_id');
}

/**
 * Checks whether a page is the custom partner application form.
 *
 * @param int $page_id Page identifier
 *
 * @return bool
 */
function fn_lt_yandex_metrika_goals_is_partner_application_page(int $page_id): bool
{
    $target_page_id = fn_lt_yandex_metrika_goals_get_partner_application_page_id();

    return $target_page_id > 0 && $page_id === $target_page_id;
}

/**
 * Checks whether the submitted form has empty or invalid required fields.
 *
 * @param array $page_data   Form page data
 * @param array $form_values Submitted form values
 *
 * @return bool
 */
function fn_lt_yandex_metrika_goals_has_invalid_required_form_fields(array $page_data, array $form_values): bool
{
    $elements = !empty($page_data['form']['elements']) && is_array($page_data['form']['elements'])
        ? $page_data['form']['elements']
        : [];

    foreach ($elements as $element_id => $element) {
        if (($element['required'] ?? YesNo::NO) !== YesNo::YES || ($element['status'] ?? 'A') !== 'A') {
            continue;
        }

        if (
            array_key_exists($element_id, $form_values)
            && fn_lt_yandex_metrika_goals_is_form_field_valid($element, $form_values[$element_id])
        ) {
            continue;
        }

        return true;
    }

    return false;
}

/**
 * Checks whether a submitted required form field is filled and valid.
 *
 * @param array $element Form element
 * @param mixed $value   Submitted value
 *
 * @return bool
 */
function fn_lt_yandex_metrika_goals_is_form_field_valid(array $element, $value): bool
{
    if (!fn_lt_yandex_metrika_goals_is_form_value_filled($value)) {
        return false;
    }

    if (($element['element_type'] ?? '') === FORM_EMAIL && !fn_validate_email((string) $value)) {
        return false;
    }

    return true;
}

/**
 * Checks whether a submitted form value is filled.
 *
 * @param mixed $value Submitted value
 *
 * @return bool
 */
function fn_lt_yandex_metrika_goals_is_form_value_filled($value): bool
{
    if (is_array($value)) {
        foreach ($value as $item) {
            if (fn_lt_yandex_metrika_goals_is_form_value_filled($item)) {
                return true;
            }
        }

        return false;
    }

    if ($value === null || is_object($value) || is_resource($value)) {
        return false;
    }

    return trim((string) $value) !== '';
}

/**
 * Queues the customer registration goal.
 *
 * @param int  $user_id          Goal user identifier
 * @param bool $current_response Whether the current response can render the goal
 *
 * @return void
 */
function fn_lt_yandex_metrika_goals_queue_user_registration_goal(int $user_id, bool $current_response): void
{
    fn_lt_yandex_metrika_goals_queue_goal(
        LT_YANDEX_METRIKA_GOALS_REGISTRATION_SUCCESS,
        LT_YANDEX_METRIKA_GOALS_REGISTRATION_SUCCESS . ':user:' . $user_id,
        $current_response
    );
}

/**
 * Builds a one-time goal key from storefront search parameters.
 *
 * @param array $params Product search params
 *
 * @return string
 */
function fn_lt_yandex_metrika_goals_get_search_goal_key(array $params): string
{
    $search_params = !empty($_REQUEST) && is_array($_REQUEST) ? $_REQUEST : $params;
    $search_params = fn_lt_yandex_metrika_goals_prepare_search_params($search_params);

    return LT_YANDEX_METRIKA_GOALS_SITE_SEARCH . ':' . md5((string) json_encode($search_params));
}

/**
 * Removes volatile request parameters and sorts search params for stable reload deduplication.
 *
 * @param array $params Search params
 *
 * @return array<string, mixed>
 */
function fn_lt_yandex_metrika_goals_prepare_search_params(array $params): array
{
    $ignored_params = [
        '_'              => true,
        'ajax_custom'    => true,
        'callback'       => true,
        'full_render'    => true,
        'is_ajax'        => true,
        'items_per_page' => true,
        'page'           => true,
        'redirect_url'   => true,
        'result_ids'     => true,
        'return_url'     => true,
        'security_hash'  => true,
        'sl'             => true,
        'sort_by'        => true,
        'sort_order'     => true,
    ];
    $prepared_params = [];

    foreach ($params as $param_name => $param_value) {
        if (isset($ignored_params[$param_name]) || is_object($param_value) || is_resource($param_value)) {
            continue;
        }

        if (is_array($param_value)) {
            $param_value = fn_lt_yandex_metrika_goals_prepare_search_params($param_value);
        } elseif (is_scalar($param_value) || $param_value === null) {
            $param_value = trim((string) $param_value);
        } else {
            continue;
        }

        $prepared_params[(string) $param_name] = $param_value;
    }

    ksort($prepared_params);

    return $prepared_params;
}

/**
 * Queues a goal for the current or next frontend response.
 *
 * @param string $goal_name        Yandex Metrika goal name
 * @param string $goal_key         Unique one-time key
 * @param bool   $current_response Whether the current response can render the goal
 *
 * @return void
 */
function fn_lt_yandex_metrika_goals_queue_goal(string $goal_name, string $goal_key, bool $current_response): void
{
    if (!fn_lt_yandex_metrika_goals_has_counter()) {
        return;
    }

    $goal_data = fn_lt_yandex_metrika_goals_build_goal_data($goal_name, $goal_key);

    if ($current_response || defined('AJAX_REQUEST')) {
        fn_lt_yandex_metrika_goals_queue_goal_for_current_response($goal_data);

        return;
    }

    Tygh::$app['session']['lt_yandex_metrika_goals'][$goal_data['key']] = $goal_data;
}

/**
 * Queues a goal for the current page or AJAX response.
 *
 * @param array $goal_data Goal payload
 *
 * @return void
 */
function fn_lt_yandex_metrika_goals_queue_goal_for_current_response(array $goal_data): void
{
    static $request_goals = [];

    $request_goals = fn_lt_yandex_metrika_goals_append_goal($request_goals, $goal_data);

    if (defined('AJAX_REQUEST')) {
        Tygh::$app['ajax']->assign('lt_yandex_metrika_goals', $request_goals);

        return;
    }

    Tygh::$app['view']->assign('lt_yandex_metrika_goals', $request_goals);
}

/**
 * Returns and clears session goals queued during redirecting POST actions.
 *
 * @return array<int, array<string, string>>
 */
function fn_lt_yandex_metrika_goals_pop_session_goals(): array
{
    if (
        empty(Tygh::$app['session']['lt_yandex_metrika_goals'])
        || !is_array(Tygh::$app['session']['lt_yandex_metrika_goals'])
    ) {
        return [];
    }

    $goals = array_values(Tygh::$app['session']['lt_yandex_metrika_goals']);

    unset(Tygh::$app['session']['lt_yandex_metrika_goals']);

    return $goals;
}

/**
 * Checks that the dependency add-on has a counter configured.
 *
 * @return bool
 */
function fn_lt_yandex_metrika_goals_has_counter(): bool
{
    return fn_string_not_empty((string) Registry::get('addons.rus_yandex_metrika.counter_number'));
}

/**
 * Builds a normalized goal payload for frontend JS.
 *
 * @param string $goal_name Yandex Metrika goal name
 * @param string $goal_key  Unique one-time key
 *
 * @return array<string, string>
 */
function fn_lt_yandex_metrika_goals_build_goal_data(string $goal_name, string $goal_key): array
{
    return [
        'name' => $goal_name,
        'key'  => $goal_key,
    ];
}

/**
 * Appends a goal to a list without duplicating the same key.
 *
 * @param array<int, array<string, string>> $goal_list Goal list
 * @param array<string, string>             $goal_data Goal payload
 *
 * @return array<int, array<string, string>>
 */
function fn_lt_yandex_metrika_goals_append_goal(array $goal_list, array $goal_data): array
{
    foreach ($goal_list as $queued_goal) {
        if (!empty($queued_goal['key']) && $queued_goal['key'] === $goal_data['key']) {
            return $goal_list;
        }
    }

    $goal_list[] = $goal_data;

    return $goal_list;
}
