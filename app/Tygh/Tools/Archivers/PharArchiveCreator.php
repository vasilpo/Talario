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
 * Class PharArchiveCreator
 * @package Tygh\Tools\Archivers
 */
class PharArchiveCreator implements IArchiveCreator
{
    /** @var string  */
    protected $file;

    /** @var \PharData */
    protected $phar;

    /** @var string */
    protected $extension;

    /**
     * ZipArchiveCreator constructor
     *
     * @param string $file Path to archive
     */
    public function __construct($file)
    {
        $this->file = $file;
        $this->phar = new \PharData($this->file);
        $this->extension = strtolower((string) pathinfo($this->file, PATHINFO_EXTENSION));
    }

    /**
     * @inheritDoc
     */
    public function addFile($file, $local_name)
    {
        try {
            $this->phar->addFile($file, $local_name);
        } catch (\PharException $e) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function addDir($dir)
    {
        try {
            $this->phar->buildFromDirectory($dir);
        } catch (\PharException $e) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function close()
    {
        if ($this->extension === 'zip') {
            $this->phar->compressFiles(\Phar::GZ);
            $this->phar = null;
        } else {
            /** @var \PharData $phar */
            $phar = $this->phar->compress(\Phar::GZ, 'tmp.' . $this->extension);
            $this->phar = null;
            $path = $phar->getPath();

            unset($phar);
            rename($path, $this->file);
        }
    }
}