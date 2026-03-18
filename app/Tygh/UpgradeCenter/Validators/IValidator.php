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

namespace Tygh\UpgradeCenter\Validators;

/**
 * Upgrade validators interface
 */
interface IValidator
{
    /**
     * Validates specified data by schema
     *
     * @param  array $schema  Incoming validator schema
     * @param  array $request Request data
     * @return array Validation result and Data to be displayed
     */
    public function check($schema, $request);

    /**
     * Gets validator name (ID)
     *
     * @return string Name
     */
    public function getName();
}
