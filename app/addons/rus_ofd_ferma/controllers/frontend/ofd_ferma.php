<?php

use Tygh\Addons\OfdFerma\OfdFerma;
use Tygh\Settings;


if (!defined('BOOTSTRAP')) { die('Access denied'); }

if($mode === 'cron') {

    $ofdferma = Tygh::$app['addons.rus_ofd_ferma.ofd_ferma'];

    $ofdferma->setDebug(1);
    $ofdferma->UpdateChecksStatus(); 
    $ofdferma->setDebug(0);
    
    exit;
}
