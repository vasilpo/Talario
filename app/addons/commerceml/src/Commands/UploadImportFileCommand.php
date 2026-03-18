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


/**
 * Class UploadImportFileCommand
 *
 * @package Tygh\Addons\CommerceML\Commands
 *
 * @see \Tygh\Addons\CommerceML\Commands\UploadImportFileCommandHandler
 */
class UploadImportFileCommand
{
    /**
     * @var string
     */
    public $file_name;

    /**
     * @var string
     */
    public $dir_path;

    /**
     * Returns file content from the POST data
     *
     * @return string
     */
    public function getFileContent()
    {
        return file_get_contents('php://input');
    }

    /**
     * Creates upload command
     *
     * @param string $filename File name
     * @param string $dir_path Upload dir
     *
     * @return \Tygh\Addons\CommerceML\Commands\UploadImportFileCommand
     */
    public static function create($filename, $dir_path = null)
    {
        if ($dir_path === null) {
            $dir_path = sprintf('%s/exim/1C_%s/', rtrim(fn_get_files_dir_path(), '/'), date('dmY'));
        }

        $self = new self();

        $self->file_name = (string) $filename;
        $self->dir_path = (string) $dir_path;

        return $self;
    }
}
