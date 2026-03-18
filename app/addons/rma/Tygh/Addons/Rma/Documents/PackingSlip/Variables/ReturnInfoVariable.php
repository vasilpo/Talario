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

namespace Tygh\Addons\Rma\Documents\PackingSlip\Variables;


use Tygh\Addons\Rma\Documents\PackingSlip\Context;
use Tygh\Template\Document\Variables\GenericVariable;
use Tygh\Tools\Formatter;

/**
 * Class ReturnInfoVariable
 * @package Tygh\Addons\Rma\Documents\PackingSlip\Variables
 */
class ReturnInfoVariable extends GenericVariable
{
    /**
     * ReturnInfoVariable constructor.
     * @param Context   $context    Instance of rma return context.
     * @param array     $config     Config of variable.
     * @param Formatter $formatter  Instance of formatter.
     */
    public function __construct(Context $context, array $config, Formatter $formatter)
    {
        $actions = fn_get_rma_properties(RMA_ACTION, $context->getLangCode());
        $statuses = fn_get_simple_statuses(STATUSES_RETURN, false, false, $context->getLangCode());

        $config['data'] = $context->getReturnInfo();

        if ($config['data']) {
            $config['data']['timestamp'] = $formatter->asDatetime($config['data']['timestamp']);
        }

        $config['data']['action_name'] = isset($actions[$config['data']['action']]) ? $actions[$config['data']['action']]['property'] : '';
        $config['data']['status_name'] = isset($statuses[$config['data']['status']]) ? $statuses[$config['data']['status']] : '';

        parent::__construct($context, $config);
    }
}