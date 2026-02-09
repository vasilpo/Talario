<?php

Tygh::$app['addons.rus_ofd_ferma.ofd_ferma'] = function () {
    return new Tygh\Addons\OfdFerma\OfdFerma();
};

fn_register_hooks(
    'change_order_status'
);