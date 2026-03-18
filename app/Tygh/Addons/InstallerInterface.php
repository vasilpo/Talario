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

namespace Tygh\Addons;

use Tygh\Core\ApplicationInterface;

/**
 * Interface InstallerInterface should be implemented by classes specified at add-on XML scheme v4 as custom installers.
 *
 * @package Tygh\Addons
 */
interface InstallerInterface
{
    /**
     * @return InstallerInterface
     */
    public static function factory(ApplicationInterface $app);

    /**
     * @return void
     */
    public function onBeforeInstall();

    /**
     * @return void
     */
    public function onInstall();

    /**
     * @return void
     */
    public function onUninstall();
}