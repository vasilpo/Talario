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

namespace Tygh\UpgradeCenter\Console;


use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\ArgvInput as BaseArgvInput;

/**
 * ArgvInput represents an input coming from the CLI arguments.
 *
 * @package Tygh\UpgradeCenter\Console
 */
class ArgvInput extends BaseArgvInput
{
    /**
     * @inheritdoc
     */
    public function __construct(array $argv = null, InputDefinition $definition = null)
    {
        if (null === $argv) {
            $argv = $_SERVER['argv'];
        }

        foreach ($argv as $key => $value) {
            if (strpos($value, '--dispatch') === 0) {
                unset($argv[$key]);
                break;
            }
        }

        parent::__construct($argv, $definition);
    }
}