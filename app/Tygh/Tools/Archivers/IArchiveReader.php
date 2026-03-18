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

namespace Tygh\Tools\Archivers;

/**
 * Interface IArchiveReader
 * @package Tygh\Tools\Archivers
 */
interface IArchiveReader
{
    /**
     * ArchiveReader constructor
     *
     * @param string $file Path to archive file
     */
    public function __construct($file);

    /**
     * Extract archive
     *
     * @param string $dir Path to directory
     * @return bool
     */
    public function extractTo($dir);

    /**
     * Get files contained in archive
     *
     * @param bool $only_root Only root directory files
     * @return bool
     */
    public function getFiles($only_root = false);
}