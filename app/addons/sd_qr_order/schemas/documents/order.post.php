<?php
/****************************************************************************
 *                                                                          *
 *   © Simtech Development Ltd.                                             *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 ***************************************************************************/

 use Tygh\Addons\QrOrder\Documents\Order\QrCodeVariable;

 defined('BOOTSTRAP') or die('Access denied');
 
 /** @var array $schema */
 
 $schema['qr_code'] = [
     'class' => QrCodeVariable::class,
 ];
 
 return $schema;
