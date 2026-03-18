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
 * Class PharArchiveReader
 * @package Tygh\Tools\Archivers
 */
class PharArchiveReader implements IArchiveReader
{
    /** @var string  */
    protected $file;

    /**
     * PharArchiveReader constructor
     *
     * @param string $file path to archive
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * @inheritDoc
     */
    public function extractTo($dir)
    {
        $phar = new \PharData($this->file);
        $result = $phar->extractTo($dir, null, true);
        $phar = null;

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getFiles($only_root = false)
    {
        $files = array();

        $tgz = new \PharData($this->file,
            \FilesystemIterator::SKIP_DOTS |
            \FilesystemIterator::CURRENT_AS_FILEINFO |
            \FilesystemIterator::KEY_AS_PATHNAME
        );

        if ($only_root) {
            $iterator = $tgz;
        } else {
            $iterator = new \RecursiveIteratorIterator($tgz, \RecursiveIteratorIterator::SELF_FIRST);
        }

        foreach ($iterator as $path => $file_info) {
            /** @var \SplFileInfo $file_info */
            $files[] = $file_info->isDir()
                ? rtrim($iterator->getSubPathname(), '\\/') . DIRECTORY_SEPARATOR
                : $iterator->getSubPathname();
        }

        $tgz = null;
        $iterator = null;
        sort($files);

        return $files;
    }
}