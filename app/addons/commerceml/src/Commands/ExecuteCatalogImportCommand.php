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
 * Class ExecuteImportCommand
 *
 * @package Tygh\Addons\CommerceML\Commands
 *
 * @see \Tygh\Addons\CommerceML\Commands\ExecuteCatalogImportCommandHandler
 */
class ExecuteCatalogImportCommand extends AImportCommand
{
    /**
     * Creates command instance
     *
     * @param int    $import_id   Import ID
     * @param int    $time_limit  Executing time limit
     * @param string $entity_type Entity type
     *
     * @return \Tygh\Addons\CommerceML\Commands\ExecuteCatalogImportCommand
     */
    public static function create($import_id, $time_limit, $entity_type)
    {
        $self = new self();
        $self->import_id = (int) $import_id;
        $self->time_limit = (int) $time_limit;
        $self->entity_type = $entity_type;

        return $self;
    }
}
