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

namespace Tygh\UpgradeCenter;

class Output
{
    /**
     * Console mode flag
     *
     * @var bool $is_console
     */
    private static $is_console = null;

    /**
     * Count of total steps
     *
     * @var int $steps
     */
    private static $steps = 0;

    /**
     * Current step
     *
     * @var int $current_step
     */
    private static $current_step = 0;

    /**
     * Sets steps count
     *
     * @param integer $steps Step count
     */
    public static function steps($steps)
    {
        if (self::isConsole()) {
            echo 'Total parts ' . $steps . PHP_EOL;
        } else {
            fn_set_progress('step_scale', $steps);
        }

        self::$steps = $steps;
    }

    /**
     * Displays message to appropriate output screen (console/display)
     *
     * @param string $message   Message text
     * @param string $title     Title text
     * @param bool   $next_step Move progress to next step
     */
    public static function display($message, $title = '', $next_step = true)
    {
        if (self::isConsole()) {
            if (!empty($title)) {
                echo $title . PHP_EOL . '================================================' . PHP_EOL;
            }

            echo $message . PHP_EOL;

            if ($next_step) {
                echo 'Step ' . self::$current_step . '/' . self::$steps . ' completed' . PHP_EOL;
            }
        } else {
            if (!empty($title)) {
                fn_set_progress('title', $title);
            }

            fn_set_progress('echo', $message, $next_step);
        }

        if ($next_step) {
            self::$current_step++;
        }
    }

    /**
     * Checks if script run from the console
     *
     * @return bool true if run from console
     */
    private static function isConsole()
    {
        if (is_null(self::$is_console)) {
            if (empty($_SERVER['REQUEST_METHOD'])) {
                self::$is_console = true;
            } else {
                self::$is_console = false;
            }
        }

        return self::$is_console;
    }
}
