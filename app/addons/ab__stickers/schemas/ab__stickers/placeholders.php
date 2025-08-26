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
use Tygh\Enum\ObjectStatuses;
use Tygh\Registry;
defined('BOOTSTRAP') or die('Access denied');
$schema = [
'[end_line]' => [
'name' => '[end_line]',
'title' => 'ab__stickers.placeholders.end_line',
'default_value' => '<br />',
'parse_function' => '<br />',
'is_default' => true,
],
'[price]' => [
'name' => '[price]',
'title' => 'ab__stickers.placeholders.price',
'default_value' => random_int(10, 900),
'parse_function' => ['fn_ab__stickers_parse_placeholder_price'],
],
'[list_price]' => [
'name' => '[list_price]',
'title' => 'ab__stickers.placeholders.list_price',
'default_value' => random_int(10, 900),
'parse_function' => ['fn_ab__stickers_parse_placeholder_price'],
],
'[amount]' => [
'name' => '[amount]',
'title' => 'ab__stickers.placeholders.amount',
'default_value' => random_int(5, 15),
'parse_function' => ['fn_ab__stickers_parse_placeholder_amount'],
],
'[weight]' => [
'name' => '[weight]',
'title' => 'ab__stickers.placeholders.weight',
'default_value' => random_int(5, 15),
'parse_function' => ['fn_ab__stickers_parse_placeholder_weight'],
],
'[ceil_integer_weight]' => [
'name' => '[ceil_integer_weight]',
'title' => 'ab__stickers.placeholders.ceil_integer_weight',
'default_value' => random_int(5, 15),
'parse_function' => ['fn_ab__stickers_parse_placeholder_weight'],
],
'[discount]' => [
'name' => '[discount]',
'title' => 'ab__stickers.placeholders.discount',
'default_value' => random_int(10, 90),
'parse_function' => ['fn_ab__stickers_parse_placeholder_discount'],
'sticker_type' => StickerTypes::DYNAMIC,
'condition' => 'discount',
],
'[discount_sum]' => [
'name' => '[discount_sum]',
'title' => 'ab__stickers.placeholders.discount_sum',
'default_value' => random_int(100, 900),
'parse_function' => ['fn_ab__stickers_parse_placeholder_discount'],
'sticker_type' => StickerTypes::DYNAMIC,
'condition' => 'discount_sum',
],
'[min_qty_discount_count]' => [
'name' => '[min_qty_discount_count]',
'title' => 'ab__stickers.placeholders.min_qty_discount_count',
'default_value' => random_int(1, 10),
'parse_function' => ['fn_ab__stickers_parse_placeholder_qty_discount'],
'sticker_type' => StickerTypes::DYNAMIC,
'condition' => 'qty_discounts',
],
'[min_qty_discount_price]' => [
'name' => '[min_qty_discount_price]',
'title' => 'ab__stickers.placeholders.min_qty_discount_price',
'default_value' => random_int(10, 900),
'parse_function' => ['fn_ab__stickers_parse_placeholder_qty_discount'],
'sticker_type' => StickerTypes::DYNAMIC,
'condition' => 'qty_discounts',
],
'[min_qty_discount_percentage]' => [
'name' => '[min_qty_discount_percentage]',
'title' => 'ab__stickers.placeholders.min_qty_discount_percentage',
'default_value' => random_int(10, 90),
'parse_function' => ['fn_ab__stickers_parse_placeholder_qty_discount'],
'sticker_type' => StickerTypes::DYNAMIC,
'condition' => 'qty_discounts'
],
'[avail_since]' => [
'name' => '[avail_since]',
'title' => 'ab__stickers.placeholders.avail_since',
'parse_function' => ['fn_ab__stickers_parse_placeholder_avail_since'],
'sticker_type' => StickerTypes::DYNAMIC,
'condition' => 'pre_order'
]
];
if (Registry::get('addons.discussion.status') === ObjectStatuses::ACTIVE || Registry::get('addons.product_reviews.status') === ObjectStatuses::ACTIVE) {
$schema['[rating]'] = [
'name' => '[rating]',
'title' => 'ab__stickers.placeholders.rating',
'default_value' => random_int(1, 5),
'parse_function' => ['fn_ab__stickers_parse_placeholder_rating'],
'sticker_type' => StickerTypes::DYNAMIC,
'condition' => 'rating'
];
}
return $schema;
