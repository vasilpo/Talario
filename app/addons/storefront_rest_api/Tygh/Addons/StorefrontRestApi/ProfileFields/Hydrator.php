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

namespace Tygh\Addons\StorefrontRestApi\ProfileFields;

use Tygh\Enum\ProfileFieldTypes;

/**
 * Class Hydrator populates profile fields schema with data.
 *
 * @package Tygh\Addons\StorefrontRestApi\ProfileFields
 */
class Hydrator
{
    /**
     * Hydrates schema with values.
     *
     * @param array $schema Profile fields schema
     * @param array $data   Data to hydrate from
     *
     * @return array Hydrated schema
     */
    public function hydrate(array $schema, array $data)
    {
        if (!isset($data['fields'])) {
            $data['fields'] = [];
        }

        foreach ($schema as $section_id => &$section) {
            foreach ($section['fields'] as &$field) {
                $field_id = $field['field_id'];

                if ($field['is_default']) {
                    $value_container = $data;
                } else {
                    $value_container = $data['fields'];
                }

                if ($field['field_type'] === ProfileFieldTypes::CHECKBOX && isset($value_container[$field_id])) {
                    if ($value_container[$field_id] === 'Y') {
                        $value_container[$field_id] = true;
                    } else {
                        $value_container[$field_id] = false;
                    }
                }

                $field['value'] = isset($value_container[$field_id])
                    ? $value_container[$field_id]
                    : null;
            }
        }

        return $schema;
    }
}
