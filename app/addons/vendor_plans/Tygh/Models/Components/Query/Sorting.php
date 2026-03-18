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

class Sorting extends AComponent
{

    public $directions = array(
        'asc' => 'asc',
        'desc' => 'desc',
    );

    public function prepare()
    {
        $sort_fields = $this->model->getSortFields();

        if (empty($this->params['sort_by']) || empty($sort_fields[$this->params['sort_by']])) {
            $this->params['sort_by'] = key($sort_fields);
        }

        if (empty($this->params['sort_order']) || empty($this->directions[$this->params['sort_order']])) {
            $default_direction = $this->model->getSortDefaultDirection();
            $this->params['sort_order'] = !empty($default_direction) ? $default_direction : key($this->directions);
        }

        $sorting = $sort_fields[$this->params['sort_by']];
        if (is_array($sorting)) {
            $sorting = implode(' ' . $this->directions[$this->params['sort_order']] . ', ', $sorting);
        }

        if (!empty($sorting)) {
            $sorting .= ' ' . $this->directions[$this->params['sort_order']];
            $this->params['sort_order_rev'] = $this->params['sort_order'] == 'asc' ? 'desc' : 'asc';
        }

        $this->result = $sorting;
    }

    public function get()
    {
        if (!empty($this->result)) {
            return ' ORDER BY ' . $this->result;
        }

        return '';
    }

}
