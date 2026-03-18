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

namespace Tygh\Core;

/**
 * Interface BootstrapInterface should be implemented when you need to execute any code at application initialisation
 * phase. Remember that the bootstrapping is performed for each request or application runtime, meaning no heavy or
 * slow code should be placed to application bootstrapper. Usually you'll only want to register service providers here.
 *
 * @package Tygh\Core
 */
interface BootstrapInterface
{
    /**
     * @param \Tygh\Core\ApplicationInterface $app Application
     *
     * @return void
     */
    public function boot(ApplicationInterface $app);
}
