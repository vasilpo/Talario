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


namespace Tygh\Template\Document\Order\Variables;


use Tygh\Template\Document\Order\Context;

/**
 * Class CompanyVariable
 * @package Tygh\Template\Document\Order\Variables
 */
class CompanyVariable extends \Tygh\Template\Document\Variables\CompanyVariable
{
    public function __construct(Context $context, array $config = array())
    {
        $order = $context->getOrder();
        parent::__construct($config, $order->getCompanyId(), $context->getLangCode(), $context);
    }
}