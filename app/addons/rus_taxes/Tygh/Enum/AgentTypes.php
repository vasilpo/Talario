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

namespace Tygh\Enum;

use ReflectionClass;

defined('BOOTSTRAP') or die('Access denied');

class AgentTypes
{
    const BANKING_PAYMENT_AGENT = 'banking_payment_agent';
    const BANKING_PAYMENT_SUBAGENT = 'banking_payment_subagent';
    const PAYMENT_AGENT = 'payment_agent';
    const PAYMENT_SUBAGENT = 'payment_subagent';
    const ATTORNEY = 'attorney';
    const COMMISSIONER = 'commissioner';
    const AGENT = 'agent';

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
