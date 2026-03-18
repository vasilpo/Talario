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


use Tygh\Common\OperationResult;

/**
 * Class CleanUpFilesDirCommandHandler
 *
 * @package Tygh\Addons\CommerceML\Commands
 */
class CleanUpFilesDirCommandHandler
{
    /**
     * Executes clean up directory
     *
     * @param \Tygh\Addons\CommerceML\Commands\CleanUpFilesDirCommand $command Clean Up command
     *
     * @return \Tygh\Common\OperationResult
     */
    public function handle(CleanUpFilesDirCommand $command)
    {
        $result = new OperationResult(true);

        if (!$command->rotate) {
            fn_rm($command->dir, false);
            return $result;
        }

        if (!is_dir($command->dir) || $this->isDirEmpty($command->dir)) {
            return $result;
        }

        for ($i = $command->max_dirs_count; $i >= 0; --$i) {
            $dir_path = rtrim($command->dir, '/') . ($i === 0 ? '' : '.' . $i);

            if (!is_dir($dir_path)) {
                continue;
            }

            if ($i === $command->max_dirs_count) {
                fn_rm($dir_path);
            } else {
                fn_rename($dir_path, rtrim($command->dir, '/') . '.' . ($i + 1));
            }
        }

        fn_mkdir($command->dir);

        return $result;
    }

    /**
     * Checks if dir is empty
     *
     * @param string $dir Dir path
     *
     * @return bool
     */
    private function isDirEmpty($dir)
    {
        $dh = opendir($dir);

        if ($dh === false) {
            return false;
        }

        while (($item = readdir($dh)) !== false) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            closedir($dh);
            return false;
        }

        closedir($dh);
        return true;
    }
}
