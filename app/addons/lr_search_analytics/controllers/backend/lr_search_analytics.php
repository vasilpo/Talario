<?php
/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

defined('BOOTSTRAP') or die('Access denied');

if ($mode === 'export') {
    fn_lr_search_analytics_export_report();
}
