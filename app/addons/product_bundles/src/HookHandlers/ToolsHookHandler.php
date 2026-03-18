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

namespace Tygh\Addons\ProductBundles\HookHandlers;

use Tygh\Addons\ProductBundles\ServiceProvider;

class ToolsHookHandler
{
    /**
     * The `tools_change_status` hook handler.
     *
     * @param array<string> $params Parameters of request.
     * @param mixed         $result Result of updating status.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
     *
     * @return void
     */
    public function onToolsChangeStatus(array $params, $result)
    {
        if (!$result) {
            return;
        }
        if ($params['table'] !== 'product_bundles') {
            return;
        }
        $bundle_service = ServiceProvider::getService();
        $bundle_service->updateBundleStatus((int) $params['id'], $params['status']);
    }
}
