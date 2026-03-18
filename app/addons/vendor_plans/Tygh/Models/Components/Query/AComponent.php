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

namespace Tygh\Models\Components\Query;

use Tygh\Models\Components\IModel;

abstract class AComponent
{

    public $result = array();

    protected $model;
    protected $params;
    protected $joins;
    protected $condition;

    public function __construct(IModel $model, Array &$params, $joins = array(), $condition = array())
    {
        $this->model     = $model;
        $this->params    = &$params;
        $this->joins     = $joins;
        $this->condition = $condition;

        $this->prepare();
    }

    /**
     * Preparing result
     */
    abstract public function prepare();

    /**
     * Getting result with convertion to string
     * @return array
     */
    abstract public function get();

}
