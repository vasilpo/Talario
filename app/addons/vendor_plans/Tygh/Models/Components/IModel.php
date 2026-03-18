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

namespace Tygh\Models\Components;

interface IModel
{

    // Main

    public static function model();

    public function findMany($params = array());

    public function findAll($params = array());

    public function find($id, $params = array());

    public function save();

    public function delete();

    public function deleteMany($params);

    public function link($name, IModel $related_model);

    public function isNewRecord();

    // Events

    public function beforeFind(&$params);

    public function afterFind();

    public function beforeSave();

    public function afterSave();

    public function beforeDelete();

    public function afterDelete();

    // Instance

    public function getTableName();

    public function getPrimaryField();

    public function getFields($params);

    public function getSearchFields();

    public function getSortFields();

    public function getSortDefaultDirection();

    public function getExtraCondition($params);

    public function getJoins($params);

    public function getLastViewObjectName();

    public function getDescriptionTableName();

    public function getParams();

    /**
     * Gets attributes
     *
     * @param array<string|int|null|array<string|int>> $attributes Attributes
     *
     * @return array<string|int|null|array<string|int>>
     */
    public function attributes($attributes = []);

    /**
     * Gets current attributes
     *
     * @param array<string|int|null|array<string|int>> $current_attributes Current attributes
     *
     * @return array<string|int|null|array<string|int>>
     */
    public function currentAttributes($current_attributes = []);

}
