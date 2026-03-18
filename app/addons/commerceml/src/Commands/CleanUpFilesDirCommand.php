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


namespace Tygh\Addons\CommerceML\Commands;

use Tygh\Registry;

/**
 * Class CleanUpFilesDirCommand
 *
 * @package Tygh\Addons\CommerceML\Commands
 *
 * @see \Tygh\Addons\CommerceML\Commands\CleanUpFilesDirCommandHandler
 */
class CleanUpFilesDirCommand
{
    /**
     * @var string
     */
    public $dir;

    /**
     * @var bool
     */
    public $rotate = true;

    /**
     * @var int
     */
    public $max_dirs_count = 20;

    /**
     * CleanUpFilesDirCommand constructor.
     *
     * @param string $dir            Dir path
     * @param bool   $rotate         Whether to rotate dirs
     * @param int    $max_dirs_count Max dirs count after rotate
     */
    public function __construct($dir, $rotate = true, $max_dirs_count = 20)
    {
        $this->dir = $dir;
        $this->rotate = $rotate;
        $this->max_dirs_count = $max_dirs_count;
    }

    /**
     * ClearFilesDirCommand constructor.
     *
     * @param string $dir    Dir path
     * @param bool   $rotate Whether to rotate dirs
     *
     * @return \Tygh\Addons\CommerceML\Commands\CleanUpFilesDirCommand
     */
    public static function create($dir, $rotate = true)
    {
        $max_dirs_count = (int) Registry::ifGet('config.commerceml.max_dirs_count', 20);

        return new self($dir, $rotate, $max_dirs_count);
    }
}
