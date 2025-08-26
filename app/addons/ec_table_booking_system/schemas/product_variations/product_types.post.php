<?php
/**
 * CS-Cart Table Booking System - ec_table_booking_system
 * 
 * PHP version 7.1
 * 
 * @category  Add-on
 * @package   CS_Cart
 * @author    Ecarter Technologies Private Limited <support@ecarter.co>
 * @copyright 2021 Ecarter Technologies Private Limited
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('BOOTSTRAP') or die('Access denied');

/**
 * @var array $schema
 */
if (defined('PRODUCT_TYPE_VENDOR_PRODUCT_OFFER') || defined('PRODUCT_TYPE_PRODUCT_OFFER_VARIATION')) {
    $schema[PRODUCT_TYPE_VENDOR_PRODUCT_OFFER]['tabs'][] = 'ec_table_booking_system';
    $schema[PRODUCT_TYPE_PRODUCT_OFFER_VARIATION]['tabs'][] = 'ec_table_booking_system';
}

if (isset($schema['V'])) {
    $schema['V']['tabs'][] = 'ec_table_booking_system';
}


return $schema;
