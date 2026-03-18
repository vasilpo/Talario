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

namespace Tygh\Addons\TinkoffMultiparty\Enum;

defined('BOOTSTRAP') or die('Access denied');

use ReflectionClass;
use Tygh\Enum\AgentTypes;

class TinkoffMultipartyAgentTypes extends AgentTypes
{
    const BANKING_PAYMENT_AGENT = 'bank_paying_agent';
    const BANKING_PAYMENT_SUBAGENT = 'bank_paying_subagent';
    const PAYMENT_AGENT = 'paying_agent';
    const PAYMENT_SUBAGENT = 'paying_subagent';
    const COMMISSIONER = 'commission_agent';
    const AGENT = 'another';

    /**
     * Convert value of agent type to specific version of it for current object.
     *
     * @param string $original_agent_type_value Original value of agent type
     *
     * @return string
     */
    public static function getValue($original_agent_type_value)
    {
        return (string) constant('self::' . strtoupper($original_agent_type_value));
    }

    /**
     * Return all available agent type variants for specific object.
     *
     * @return array<string, string>
     */
    public static function getAllValues()
    {
        $reflect = new ReflectionClass(self::class);
        return $reflect->getConstants();
    }
}
