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
 * Class RemoveImportCommand
 *
 * @package Tygh\Addons\CommerceML\Commands
 *
 * @see \Tygh\Addons\CommerceML\Commands\RemoveImportCommandHandler
 */
class RemoveImportCommand
{
    /**
     * @var int
     */
    private $import_id;

    /**
     * Creates command instance
     *
     * @param int $import_id Import ID
     *
     * @return \Tygh\Addons\CommerceML\Commands\RemoveImportCommand
     */
    public static function create($import_id)
    {
        $self = new self();
        $self->import_id = (int) $import_id;

        return $self;
    }

    /**
     * @return int
     */
    public function getImportId()
    {
        return $this->import_id;
    }
}
