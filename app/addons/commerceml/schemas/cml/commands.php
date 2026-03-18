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

use Tygh\Addons\CommerceML\Commands\RemoveImportCommand;
use Tygh\Addons\CommerceML\Commands\CleanUpFilesDirCommand;
use Tygh\Addons\CommerceML\Commands\CreateImportCommand;
use Tygh\Addons\CommerceML\Commands\AuthCommand;
use Tygh\Addons\CommerceML\ServiceProvider;
use Tygh\Addons\CommerceML\Commands\UploadImportFileCommand;
use Tygh\Addons\CommerceML\Commands\UnzipImportFileCommand;
use Tygh\Addons\CommerceML\Commands\ExecuteCatalogImportCommand;
use Tygh\Addons\CommerceML\Commands\ExportOrdersCommand;
use Tygh\Addons\CommerceML\Commands\ExecuteSaleImportCommand;

defined('BOOTSTRAP') or die('Access denied');

/**
 * @var array<string, array{middleware: array<callable>, handler: callable}> $schema Declares command handlers
 */
$schema = [
    CreateImportCommand::class => [
        'middleware' => [],
        'handler'    => static function (CreateImportCommand $command) {
            return ServiceProvider::getCreateImportCommandHandler()->handle($command);
        }
    ],
    AuthCommand::class => [
        'middleware' => [],
        'handler'    => static function (AuthCommand $command) {
            return ServiceProvider::getAuthCommandHandler()->handle($command);
        }
    ],
    UploadImportFileCommand::class => [
        'middleware' => [],
        'handler'    => static function (UploadImportFileCommand $command) {
            return ServiceProvider::getUploadImportFileCommandHandler()->handle($command);
        }
    ],
    UnzipImportFileCommand::class => [
        'middleware' => [],
        'handler'    => static function (UnzipImportFileCommand $command) {
            return ServiceProvider::getUnzipImportFileCommandHandler()->handle($command);
        }
    ],
    ExecuteCatalogImportCommand::class => [
        'middleware' => [],
        'handler'    => static function (ExecuteCatalogImportCommand $command) {
            return ServiceProvider::getExecuteImportCommandHandler()->handle($command);
        }
    ],
    RemoveImportCommand::class => [
        'middleware' => [],
        'handler'    => static function (RemoveImportCommand $command) {
            return ServiceProvider::getRemoveImportCommandHandler()->handle($command);
        }
    ],
    CleanUpFilesDirCommand::class => [
        'middleware' => [],
        'handler'    => static function (CleanUpFilesDirCommand $command) {
            return ServiceProvider::getCleanUpFilesDirCommandHandler()->handle($command);
        }
    ],
    ExportOrdersCommand::class => [
        'middleware' => [],
        'handler'    => static function (ExportOrdersCommand $command) {
            return ServiceProvider::getExportOrderCommandHandler()->handle($command);
        }
    ],
    ExecuteSaleImportCommand::class => [
        'middleware' => [],
        'handler'    => static function (ExecuteSaleImportCommand $command) {
            return ServiceProvider::getExecuteSaleImportCommandHandler()->handle($command);
        }
    ]
];

return $schema;
