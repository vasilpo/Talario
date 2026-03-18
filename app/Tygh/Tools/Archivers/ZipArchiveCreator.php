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
 * Class ZipArchiveCreator
 * @package Tygh\Tools\Archivers
 */
class ZipArchiveCreator implements IArchiveCreator
{
    /** @var string  */
    protected $file;

    /** @var \ZipArchive */
    protected $zip;

    /**
     * ZipArchiveCreator constructor
     *
     * @param string $file Path to archive
     * @throws \Exception
     */
    public function __construct($file)
    {
        $this->zip = new \ZipArchive;
        $this->file = $file;

        if ($this->zip->open($this->file, \ZipArchive::CREATE) !== true) {
            throw new \Exception('Unable create archive');
        }
    }

    /**
     * @inheritDoc
     */
    public function addFile($file, $local_name)
    {
        return $this->zip->addFile($file, $local_name);
    }

    /**
     * @inheritDoc
     */
    public function addDir($dir)
    {
        $result = true;

        /**
         * @var \RecursiveDirectoryIterator|\RecursiveIteratorIterator|\SplFileInfo $iterator
         */
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir,
                \FilesystemIterator::SKIP_DOTS |
                \FilesystemIterator::CURRENT_AS_FILEINFO |
                \FilesystemIterator::KEY_AS_PATHNAME
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $key => $item) {
            $path = trim($iterator->getSubPathname(), '\\/');

            /** @var \SplFileInfo $item */
            if ($item->isDir()) {
                $result = $this->zip->addEmptyDir($path);
            } else {
                $result = $this->zip->addFile($item->getPathname(), $path);
            }

            if (!$result) {
                break;
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function close()
    {
        $this->zip->close();
        $this->zip = null;
    }
}