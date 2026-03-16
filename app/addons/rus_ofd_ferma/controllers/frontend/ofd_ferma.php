<?php

use Tygh\Addons\OfdFerma\OfdFerma;
use Tygh\Settings;
use Tygh\Tygh;

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

/** @var string $mode */
if ($mode === 'cron') {
    $ofdferma = Tygh::$app['addons.rus_ofd_ferma.ofd_ferma'];

    $ofdferma->setDebug(1);
    $ofdferma->updateChecksStatus();
    $ofdferma->setDebug(0);

    exit;
}
