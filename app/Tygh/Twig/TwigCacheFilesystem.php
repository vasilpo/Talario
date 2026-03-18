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


namespace Tygh\Twig;

/**
 * The class that extends the standard Twig class for template caching; it solves problems with file permissions.
 *
 * @package Tygh\Twig
 */
class TwigCacheFilesystem extends \Twig\Cache\FilesystemCache
{
    /**
     * @inheritDoc
     */
    public function __construct($directory, $options = 0)
    {
        if (!is_dir($directory)) {
            fn_mkdir($directory);
        }

        parent::__construct($directory, $options);
    }

    /**
     * @inheritDoc
     */
    public function write(string $key, string $content): void
    {
        $file_exists = file_exists($key);

        parent::write($key, $content);

        if (!$file_exists) {
            @chmod($key, DEFAULT_FILE_PERMISSIONS);
            @chmod(dirname($key), DEFAULT_DIR_PERMISSIONS);
        }
    }
}