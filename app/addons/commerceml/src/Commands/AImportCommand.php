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
 * Class AImportCommand
 *
 * @package Tygh\Addons\CommerceML\Commands
 */
abstract class AImportCommand
{
    /**
     * @var int
     */
    protected $import_id;

    /**
     * @var int
     */
    protected $time_limit = 0;

    /**
     * @var string
     */
    protected $entity_type;

    /**
     * @return int
     */
    public function getImportId()
    {
        return $this->import_id;
    }

    /**
     * @return bool
     */
    public function hasTimeLimit()
    {
        return $this->time_limit > 0;
    }

    /**
     * @return int
     */
    public function getTimeLimit()
    {
        return $this->time_limit;
    }

    /**
     * @return string
     */
    public function getEntityType()
    {
        return $this->entity_type;
    }
}
