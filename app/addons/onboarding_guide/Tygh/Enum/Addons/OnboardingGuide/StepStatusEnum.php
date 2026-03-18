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

namespace Tygh\Enum\Addons\OnboardingGuide;

class StepStatusEnum
{
    const ACTIVE = 'A';
    const OPEN = 'O';
    const COMPLETED = 'C';
    const CLOSED = 'X';

    /**
     * @return string[]
     */
    public static function getValues(): array
    {
        return [self::ACTIVE, self::OPEN, self::COMPLETED, self::CLOSED];
    }

    /**
     * Determines that status is valid
     */
    public static function hasStatus(string $status): bool
    {
        return in_array($status, self::getValues());
    }

    /**
     * Determines that status is completed
     */
    public static function isCompleted(string $status): bool
    {
        return in_array($status, [self::COMPLETED, self::CLOSED]);
    }
}
