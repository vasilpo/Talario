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

namespace Tygh\Addons\Tinkoff\Enum;

defined('BOOTSTRAP') or die('Access denied');

use Tygh\Enum\AgentTypes;

class TinkoffAgentTypes extends AgentTypes
{
    const BANKING_PAYMENT_AGENT = 'bank_paying_agent';
    const BANKING_PAYMENT_SUBAGENT = 'bank_paying_subagent';
    const PAYMENT_AGENT = 'paying_agent';
    const PAYMENT_SUBAGENT = 'paying_subagent';
    const COMMISSIONER = 'commission_agent';
    const AGENT = 'another';
}
