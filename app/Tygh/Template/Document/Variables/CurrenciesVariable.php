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

namespace Tygh\Template\Document\Variables;

use Tygh\Registry;
use Tygh\Template\IActiveVariable;
use Tygh\Template\IContext;

/**
 * Represents currencies variable for document editor.
 *
 * @package Tygh\Template\Document\Variables
 */
class CurrenciesVariable extends GenericVariable implements IActiveVariable
{
    /**
     * @inheritDoc
     */
    public function __construct(IContext $context, array $config)
    {
        $config['data'] = Registry::get('currencies');

        parent::__construct($context, $config);
    }

    /**
     * @inheritDoc
     */
    public static function attributes()
    {
        $currencies = Registry::get('currencies');

        $result = array();

        foreach ($currencies as $code => $currency) {
            $result[$code] = array_keys($currency);
        }

        return $result;
    }
}