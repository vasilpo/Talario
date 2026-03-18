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

class Limit extends AComponent
{

    public function prepare()
    {
        $this->result = '';

        $table_name = $this->model->getTableName();
        $field = $this->model->getPrimaryField();

        if (!empty($this->params['items_per_page']) || !empty($this->params['get_count'])) {

            if (empty($this->params['page'])) {
                $this->params['page'] = 1;
            }

            if (empty($this->params['total_items'])) {
                $this->params['total_items'] = db_get_field(
                    "SELECT COUNT(DISTINCT($table_name.$field))"
                    . " FROM $table_name"
                    . $this->joins->get()
                    . $this->condition->get()
                );
            }

            if (!empty($this->params['items_per_page'])) {
                $this->result = db_paginate($this->params['page'], $this->params['items_per_page']);
            }
        }

        if (!empty($this->params['limit'])) {
            $this->result = db_quote(' LIMIT 0, ?i', $this->params['limit']);
        }
    }

    public function get()
    {
        return $this->result;
    }

}
