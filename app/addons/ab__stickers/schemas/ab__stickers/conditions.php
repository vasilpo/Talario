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
use Tygh\Enum\ObjectStatuses;
use Tygh\Registry;
defined('BOOTSTRAP') or die('Access denied');
$schema = [
'constant' => [
'conditions' => [
'price' => [
'operators' => ['eq', 'neq', 'lte', 'gte', 'lt', 'gt'],
'type' => 'input',
'format_function' => 'fn_format_price',
'available_placeholders' => ['[price]']
],
'feature' => [
'operators' => ['eq', 'neq', 'lte', 'gte', 'lt', 'gt', 'in', 'nin', 'exist', 'cont', 'ncont'],
'type' => 'chained',
'chained_options' => [
'parent_url' => 'product_features.get_features_list',
],
],
'categories' => [
'operators' => ['in', 'nin'],
'type' => 'picker',
'template' => 'addons/ab__stickers/views/ab__stickers/components/conditions/include_subcats.tpl',
'picker_props' => [
'picker' => 'pickers/categories/picker.tpl',
'params' => [
'multiple' => true,
'use_keys' => 'N',
'view_mode' => 'table',
],
],
],
'amount' => [
'operators' => ['eq', 'neq', 'lte', 'gte', 'lt', 'gt'],
'type' => 'input',
'field' => 'amount',
'additional_classes' => 'cm-value-integer',
'available_placeholders' => ['[amount]']
],
'popularity' => [
'operators' => ['eq', 'neq', 'lte', 'gte', 'lt', 'gt'],
'type' => 'input',
'additional_classes' => 'cm-value-integer',
],
'weight' => [
'operators' => ['eq', 'neq', 'lte', 'gte', 'lt', 'gt'],
'type' => 'input',
'additional_classes' => 'cm-value-decimal',
'decimal_format' => '%.3f',
'available_placeholders' => ['[weight]', '[ceil_integer_weight]']
],
'creating_date' => [
'operators' => ['eq', 'neq', 'lte', 'gte', 'lt', 'gt'],
'type' => 'input',
'additional_classes' => 'cm-value-integer',
'tooltip' => 'ab__stickers.conditions.names.creating_date.tooltip',
],
'free_shipping' => [
'type' => 'statement',
'template' => 'addons/ab__stickers/views/ab__stickers/components/conditions/free_shipping_additional_tmpl.tpl',
],
],
],
'dynamic' => [
'conditions' => [
'price' => [
'operators' => ['eq', 'neq', 'lte', 'gte', 'lt', 'gt'],
'type' => 'input',
'format_function' => 'fn_format_price',
'available_placeholders' => ['[price]'],
'validate_function' => ['fn_ab__stickers_validate_price'],
],
'discount' => [
'operators' => ['eq', 'neq', 'lte', 'gte', 'lt', 'gt'],
'type' => 'input',
'additional_classes' => 'cm-value-integer',
'validate_function' => ['fn_ab__stickers_validate_discount'],
'available_placeholders' => ['[discount]'],
'template' => 'addons/ab__stickers/views/ab__stickers/components/conditions/discount_additional_tmpl.tpl',
],
'discount_sum' => [
'operators' => ['eq', 'neq', 'lte', 'gte', 'lt', 'gt'],
'type' => 'input',
'additional_classes' => 'cm-value-integer',
'validate_function' => ['fn_ab__stickers_validate_discount_sum'],
'available_placeholders' => ['[discount_sum]'],
'template' => 'addons/ab__stickers/views/ab__stickers/components/conditions/discount_additional_tmpl.tpl',
],
'categories' => [
'operators' => ['in', 'nin'],
'type' => 'picker',
'template' => 'addons/ab__stickers/views/ab__stickers/components/conditions/include_subcats.tpl',
'validate_function' => ['fn_ab__stickers_validate_categories'],
'picker_props' => [
'picker' => 'pickers/categories/picker.tpl',
'params' => [
'multiple' => true,
'use_keys' => 'N',
'view_mode' => 'table',
],
],
],
'free_shipping' => [
'type' => 'statement',
'validate_function' => ['fn_ab__stickers_validate_free_shipping'],
'template' => 'addons/ab__stickers/views/ab__stickers/components/conditions/free_shipping_additional_tmpl.tpl',
],
'qty_discounts' => [
'operators' => ['eq', 'neq', 'lte', 'gte', 'lt', 'gt'],
'type' => 'input',
'validate_function' => ['fn_ab__stickers_validate_qty_discount'],
'available_placeholders' => ['[min_qty_discount_count]', '[min_qty_discount_price]', '[min_qty_discount_percentage]']
],
'pre_order' => [
'operators' => ['eq', 'neq', 'lte', 'gte', 'lt', 'gt'],
'type' => 'date',
'validate_function' => ['fn_ab__stickers_validate_pre_order'],
'available_placeholders' => ['[avail_since]'],
],
'promotion' => [
'operators' => ['in', 'nin'],
'type' => 'select',
'variants_function' => ['fn_ab__stickers_get_simple_promotions'],
'validate_function' => ['fn_ab__stickers_validate_promotions'],
],
'amount' => [
'operators' => ['eq', 'neq', 'lte', 'gte', 'lt', 'gt'],
'type' => 'input',
'field' => 'amount',
'additional_classes' => 'cm-value-integer',
'validate_function' => ['fn_ab__stickers_validate_amount'],
'available_placeholders' => ['[amount]'],
],
'weight' => [
'operators' => ['eq', 'neq', 'lte', 'gte', 'lt', 'gt'],
'type' => 'input',
'additional_classes' => 'cm-value-decimal',
'decimal_format' => '%.3f',
'validate_function' => ['fn_ab__stickers_validate_weight'],
'available_placeholders' => ['[weight]', '[ceil_integer_weight]']
],
],
],
];
if (Registry::get('addons.tags.status') === ObjectStatuses::ACTIVE) {
$schema['constant']['conditions']['tags'] = [
'operators' => ['eq', 'neq'],
'type' => 'select',
'variants_function' => ['fn_ab__stickers_get_simple_tags'],
];
}
if (Registry::get('addons.bestsellers.status') === ObjectStatuses::ACTIVE) {
$schema['constant']['conditions']['sales_amount'] = [
'operators' => ['eq', 'neq', 'lte', 'gte', 'lt', 'gt'],
'type' => 'input',
'additional_classes' => 'cm-value-integer',
];
}
if (Registry::get('addons.discussion.status') === ObjectStatuses::ACTIVE
|| Registry::get('addons.product_reviews.status') === ObjectStatuses::ACTIVE) {
$schema['constant']['conditions']['comments'] = [
'operators' => ['eq', 'neq', 'lte', 'gte', 'lt', 'gt'],
'type' => 'input',
'tooltip' => 'ab__stickers.conditions.names.comments.tooltip',
'additional_classes' => 'cm-value-integer',
];
$schema['dynamic']['conditions']['rating'] = [
'operators' => ['eq', 'neq', 'lte', 'gte', 'lt', 'gt'],
'type' => 'input',
'tooltip' => 'ab__stickers.conditions.names.rating.tooltip',
'additional_classes' => 'cm-value-decimal',
'validate_function' => ['fn_ab__stickers_validate_rating'],
'available_placeholders' => ['[rating]'],
];
}
if (Registry::get('addons.age_verification.status') === ObjectStatuses::ACTIVE) {
$schema['constant']['conditions']['age_verification'] = [
'operators' => ['eq', 'neq', 'lte', 'gte', 'lt', 'gt'],
'type' => 'input',
'additional_classes' => 'cm-value-integer',
];
}
if (Registry::get('addons.buy_together.status') === ObjectStatuses::ACTIVE) {
$schema['constant']['conditions']['buy_together'] = [
'type' => 'statement',
'tooltip' => 'ab__stickers.conditions.names.buy_together.tooltip',
];
}
return $schema;
