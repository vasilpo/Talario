<?php
/***************************************************************************
*                                                                          *
*   © 2012 ООО "Эком Системы"                                              *
*                                                                          *
* Это коммерческое программное обеспечение. Только пользователи, которые   *
* приобрели действующую лицензию и согласились с условиями лицензионного   *
* соглашения, могут устанавливать и использовать эту программу.            *
*                                                                          *
****************************************************************************
* ПОЖАЛУЙСТА, ВНИМАТЕЛЬНО ПРОЧТИТЕ ПОЛНЫЙ ТЕКСТ ЛИЦЕНЗИОННОГО СОГЛАШЕНИЯ   *
* В ФАЙЛЕ "copyright.txt", ПРЕДОСТАВЛЕННОМ ВМЕСТЕ С ЭТИМ ДИСТРИБУТИВОМ.    *
***************************************************************************/

use Tygh\Providers\StorefrontProvider;

defined('BOOTSTRAP') or die('Access denied');

/** @var string $mode */

if ($mode === 'generate') {
    fn_disable_live_editor_mode();

    $schema = fn_get_schema('price_list', 'schema');
    if (empty($_REQUEST['display']) || empty($schema['types'][$_REQUEST['display']])) {
        return [CONTROLLER_STATUS_DENIED];
    }

    $class_name = '\Tygh\PriceList\\' . fn_camelize($_REQUEST['display']);
    if (class_exists($class_name)) {
        $generator = new $class_name();
        if (isset($_REQUEST['storefront_id'])) {
            $generator->setStorefrontId($_REQUEST['storefront_id']);
        } elseif (fn_allowed_for('ULTIMATE')) {
            $company_id = fn_get_runtime_company_id();
            $repository = StorefrontProvider::getRepository();
            /** @var \Tygh\Storefront\Storefront|null $storefront */
            $storefront = $repository->findByCompanyId($company_id, true);
            if ($storefront) {
                $generator->setStoreFrontId($storefront->storefront_id);
            }
        }
        $generator->generate(true);
    }

    return [CONTROLLER_STATUS_NO_CONTENT];
}
