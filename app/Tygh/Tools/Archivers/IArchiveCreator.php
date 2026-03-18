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
 * Interface IArchiveCreator
 * @package Tygh\Tools\Archivers
 */
interface IArchiveCreator
{
    /**
     * ArchiveCreator constructor
     *
     * @param string $file Path to archive file
     */
    public function __construct($file);

    /**
     * Add file to archive
     *
     * @param string $file       Path to file
     * @param string $local_name Local name in archive
     * @return bool
     */
    public function addFile($file, $local_name);

    /**
     * Add directory to archive
     *
     * @param string $dir Path to directory
     * @return bool
     */
    public function addDir($dir);

    /**
     * Finalize and close creating archive
     */
    public function close();
}