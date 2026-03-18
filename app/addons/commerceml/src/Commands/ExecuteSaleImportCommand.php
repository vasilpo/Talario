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
 * Class ExecuteSaleImportCommand
 *
 * @package Tygh\Addons\CommerceML\Commands
 */
class ExecuteSaleImportCommand extends AImportCommand
{
    /** @var bool */
    public $is_change_status_allowed;

    /** @var bool */
    public $is_edit_order_allowed;

    /**
     * Creates command instance
     *
     * @param int    $import_id                Import ID
     * @param int    $time_limit               Executing time limit
     * @param string $entity_type              Entity type
     * @param bool   $is_change_status_allowed Flag if change status permission allowed
     * @param bool   $is_edit_order_allowed    Flag if edit order permission allowed
     *
     * @return \Tygh\Addons\CommerceML\Commands\ExecuteSaleImportCommand
     */
    public static function create($import_id, $time_limit, $entity_type, $is_change_status_allowed, $is_edit_order_allowed)
    {
        $self = new self();
        $self->import_id = (int) $import_id;
        $self->time_limit = (int) $time_limit;
        $self->entity_type = $entity_type;
        $self->is_change_status_allowed = $is_change_status_allowed;
        $self->is_edit_order_allowed = $is_edit_order_allowed;

        return $self;
    }
}
