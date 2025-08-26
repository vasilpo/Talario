<?php
/*******************************************************************************************
*   ___  _          ______                     _ _                _                        *
*  / _ \| |         | ___ \                   | (_)              | |              © 2025   *
* / /_\ | | _____  _| |_/ /_ __ __ _ _ __   __| |_ _ __   __ _   | |_ ___  __ _ _ __ ___   *
* |  _  | |/ _ \ \/ / ___ \ '__/ _` | '_ \ / _` | | '_ \ / _` |  | __/ _ \/ _` | '_ ` _ \  *
* | | | | |  __/>  <| |_/ / | | (_| | | | | (_| | | | | | (_| |  | ||  __/ (_| | | | | | | *
* \_| |_/_|\___/_/\_\____/|_|  \__,_|_| |_|\__,_|_|_| |_|\__, |  \___\___|\__,_|_| |_| |_| *
*                                                         __/ |                            *
*                                                        |___/                             *
* ---------------------------------------------------------------------------------------- *
* This is commercial software, only users who have purchased a valid license and accept    *
* to the terms of the License Agreement can install and use this program.                  *
* ---------------------------------------------------------------------------------------- *
* website: https://cs-cart.alexbranding.com                                                *
*   email: info@alexbranding.com                                                           *
*******************************************************************************************/
use Tygh\Enum\Addons\Ab_stickers\StickerTypes;
use Tygh\Enum\YesNo;
use Tygh\Registry;
defined('BOOTSTRAP') or die('Access denied');

function fn_ab__stickers_get_sticker_string_value($string = '', $placeholders = [])
{
if (is_array($placeholders)) {
$placeholders = array_merge($placeholders, [
'[end_line]' => '<br/>',
]);
$string = str_replace(array_keys($placeholders), $placeholders, $string);
}
$string = fn_ab__stickers_parse_math_formulas($string);

fn_set_hook('ab__stickers_get_sticker_string_value_post', $string, $placeholders);
return $string;
}

function fn_ab__stickers_get_placeholders($type = 'all', $params = [])
{
static $placeholders = [];
if (empty($placeholders)) {
$placeholders['all'] = fn_get_schema('ab__stickers', 'placeholders');
foreach ($placeholders['all'] as $placeholder_name => $placeholder) {
if (YesNo::isTrue($placeholder['is_default'] ?? YesNo::NO)) {
$placeholders['default'][$placeholder_name] = $placeholders['common'][$placeholder_name] = $placeholder;
} elseif (isset($placeholder['sticker_type'])) {
$placeholders[$placeholder['sticker_type']][$placeholder_name] = $placeholder;
} else {
$placeholders['common'][$placeholder_name] = $placeholder;
}
}
$sticker_types = fn_ab__stickers_get_conditions_schema();
foreach ($sticker_types as $sticker_type => $conditions) {
foreach ($conditions['conditions'] ?? [] as $condition) {
foreach ($condition['available_placeholders'] ?? [] as $placeholder_name => $placeholder) {
if (isset($placeholders['all'][$placeholder_name])) {
continue;
}
$placeholder_data = [
'name' => $placeholder_name
];
if (isset($condition['validate_function'])) {
$placeholder_data['validate_function'] = $condition['validate_function'];
}
$sticker_type = $sticker_type === 'constant' ? StickerTypes::CONSTANT : StickerTypes::DYNAMIC;
$placeholders[$sticker_type][$placeholder_name] = $placeholders['all'][$placeholder_name] = $placeholder_data;
}
}
}
}
return $placeholders[$type] ?? [];
}

function fn_ab__stickers_check_placeholders($text, $placeholders, $cond = 'or')
{
$result = false;
foreach ($placeholders as $placeholder_name => $placeholder) {
$result = strpos($text, $placeholder_name) !== false;
if (($cond === 'or' && $result) || ($cond === 'and' && !$result)) {
break;
}
}
return $result;
}

function fn_ab__stickers_get_sticker_available_placeholders($sticker_data)
{
$placeholders = fn_ab__stickers_get_placeholders('common');
$placeholders = array_merge($placeholders, fn_ab__stickers_get_placeholders($sticker_data['type']));
$placeholders = array_filter($placeholders, function ($placeholder) use ($sticker_data) {
if (!isset($placeholder['condition'])) {
return true;
}
foreach ($sticker_data['conditions']['conditions'] ?? [] as $condition) {
if ($condition['condition'] === $placeholder['condition']) {
return true;
}
}
return false;
});
return $placeholders;
}

function fn_ab__stickers_parse_placeholders($placeholders, $sticker_data, $product_data)
{
$parsed_placeholders = [];
foreach ($placeholders as $placeholder_name => $placeholder) {
$parse_function = $placeholder['parse_function'] ?? $placeholder['validate_function'] ?? false;
if (!$parse_function) {
continue;
}
$placeholder_result = null;
if (
gettype($parse_function) === 'object' && is_callable($parse_function)
|| (is_array($parse_function) && isset($parse_function[0]) && is_callable($parse_function[0]))
) {
if (isset($sticker_data['conditions']) && !is_array($sticker_data['conditions'])) {
$sticker_data['conditions'] = fn_ab__stickers_decode_data($sticker_data['conditions']);
}
$args = [$sticker_data, $product_data];
if (!isset($placeholder['parse_function'])) {
$args[0] = $sticker_data['conditions'] ?? [];
$args[] = [$product_data['product_id']];
$args[] = Tygh::$app['session']['auth'];
$placeholder_result = call_user_func_array('fn_ab__stickers_validate_dynamic_sticker', $args)['placeholders'] ?? [];
} else {
$args[] = $placeholder_name;
$placeholder_result = call_user_func_array($parse_function[0], $args);
}
if (
is_array($placeholder_result)
&& isset($placeholder_result[$placeholder_name])
) {
$placeholder_result = $placeholder_result[$placeholder_name];
}
} else {
$placeholder_result = $parse_function;
}
if (
in_array(gettype($placeholder_result), ['integer', 'double', 'string'])
&& $placeholder_result !== $placeholder_name
) {
$parsed_placeholders[$placeholder_name] = $placeholder_result;
}
}
return $parsed_placeholders;
}

function fn_ab__stickers_parse_math_formulas($string)
{
$base_pattern = '~\$\{([A-Za-z0-9+\-*/.,_\s()]+)}~ix';
$string = preg_replace_callback($base_pattern, function ($match) {
$formula = 'return (' . $match[1] . ');';
if (!fn_ab__stickers_validate_math_formula($formula)) {
return $match[0];
}
try {
$formula = @eval($formula);
} catch (Error $e) {
$formula = $match[0];
}
return $formula;
}, $string);
return (string) $string;
}
function fn_ab__stickers_validate_math_formula($formula)
{
$formula = '<?php ' . $formula . ' ?>';
$allowed_tokens = [
T_DNUMBER => true,
T_LNUMBER => true,
T_POW => true,
T_OPEN_TAG => true,
T_CLOSE_TAG => true,
T_WHITESPACE => true,
T_RETURN => true,
T_STRING => fn_ab__stickers_get_available_formula_modifiers()
];
$tokens = token_get_all($formula);
foreach ($tokens as $token) {
if (is_array($token)) {
if (!isset($allowed_tokens[$token[0]])) {
return false;
}
if (is_array($allowed_tokens[$token[0]])) {
$allowed_token = $allowed_tokens[$token[0]];
if (!in_array($token[1], $allowed_token)) {
return false;
}
}
}
}
return true;
}

function fn_ab__stickers_get_available_tags()
{
$available_tags = [
'<span>',
'<rt>',
'<ins>',
'<sub>',
'<s>',
'<strike>',
'<sup>',
'<mark>',
'<strong>',
'<i>',
'<em>',
'<big>',
'<small>',
'<b>',
];
fn_set_hook('ab__stickers_get_available_tags', $available_tags);
return $available_tags;
}

function fn_ab__stickers_get_available_formula_modifiers()
{
$available_formula_modifiers = ['abs', 'ceil', 'floor', 'pow', 'round', 'sqrt'];
fn_set_hook('ab__stickers_get_available_formula_modifiers', $available_formula_modifiers);
return $available_formula_modifiers;
}
function fn_ab__stickers_parse_placeholder_discount($sticker, $product, $placeholder = 'all')
{
$discount = empty($product['discount_prc']) ? (empty($product['list_discount_prc']) ? false : $product['list_discount_prc']) : $product['discount_prc'];
$discount_sum = empty($product['discount']) ? (empty($product['list_discount']) ? false : $product['list_discount']) : $product['discount'];
$placeholders = [
'[discount]' => is_numeric($discount) ? (int) $discount : $discount,
'[discount_sum]' => is_numeric($discount_sum) ? (int) $discount_sum : $discount_sum
];
return $placeholder === 'all' ? $placeholders : $placeholders[$placeholder] ?? $placeholder;
}
function fn_ab__stickers_parse_placeholder_qty_discount($sticker, $product, $placeholder = 'all')
{
$product_id = $product['product_id'] ?? 0;
$prices = fn_ab__stickers_get_products_prices($product_id)[$product_id] ?? [];
$min_qty_discount = array_shift($prices) ?? [];
$placeholders = [
'[min_qty_discount_count]' => $min_qty_discount['lower_limit'] ?? false,
'[min_qty_discount_price]' => $min_qty_discount['price'] ?? false,
'[min_qty_discount_percentage]' => $min_qty_discount['percentage_discount'] ?? false,
];
return $placeholder === 'all' ? $placeholders : $placeholders[$placeholder] ?? $placeholder;
}
function fn_ab__stickers_parse_placeholder_rating($sticker, $product, $placeholder = 'all')
{
fn_ab__stickers_get_product_rating($product);
$placeholders = [
'[rating]' => !empty($product['average_rating']) ? sprintf('%.1f', $product['average_rating']) : false,
];
return $placeholder === 'all' ? $placeholders : $placeholders[$placeholder] ?? $placeholder;
}
function fn_ab__stickers_parse_placeholder_avail_since($sticker, $product, $placeholder = 'all')
{
$avail_since = !empty($product['avail_since']) && $product['avail_since'] > TIME ? $product['avail_since'] : false;
if ($avail_since) {
$avail_since = fn_date_format($product['avail_since'], Registry::get('settings.Appearance.date_format'));
}
$placeholders = [
'[avail_since]' => $avail_since,
];
return $placeholder === 'all' ? $placeholders : $placeholders[$placeholder] ?? $placeholder;
}
function fn_ab__stickers_parse_placeholder_price($sticker, $product, $placeholder = 'all')
{
$placeholders = [
'[price]' => !empty($product['price']) ? fn_format_price($product['price']) : false,
'[list_price]' => !empty($product['list_price']) ? fn_format_price($product['list_price']) : false
];
return $placeholder === 'all' ? $placeholders : $placeholders[$placeholder] ?? $placeholder;
}
function fn_ab__stickers_parse_placeholder_amount($sticker, $product, $placeholder = 'all')
{
$placeholders = [
'[amount]' => $product['amount'] ?? false
];
return $placeholder === 'all' ? $placeholders : $placeholders[$placeholder] ?? $placeholder;
}
function fn_ab__stickers_parse_placeholder_weight($sticker, $product, $placeholder = 'all')
{
$placeholders = [
'[weight]' => !empty($product['weight']) ? $product['weight'] : false,
'[ceil_integer_weight]' => !empty($product['weight']) ? ceil($product['weight']) : false
];
return $placeholder === 'all' ? $placeholders : $placeholders[$placeholder] ?? $placeholder;
}
function fn_ab__stickers_parse_placeholder_feature($sticker, $product, $placeholder = 'all')
{
$placeholders = [];
return $placeholder === 'all' ? $placeholders : $placeholders[$placeholder] ?? $placeholder;
}
