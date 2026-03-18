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

namespace Tygh\Addons\Gdpr\DataExtractor;

use Tygh\Gdpr\SchemaManager;

/**
 * Extracts extra user data specified in schema from collection.
 *
 * @package Tygh\Addons\Gdpr\DataExtractor
 */
class UserExtraPersonalDataCollectionExtractor extends UserPersonalDataCollectionExtractor implements IDataExtractor
{
    public function __construct(SchemaManager $schema_manager)
    {
        parent::__construct($schema_manager);
    }

    /**
     * @inheritdoc
     */
    public function extract(array $user_data)
    {
        return parent::extract($user_data);
    }

    /**
     * Extracts extra data
     *
     * @param array $data   Raw data
     * @param array $params Params
     *
     * @return array
     */
    protected function extractData(array $data, array $params)
    {
        $result = parent::extractData($data, $params);

        if (!empty($data['force_display'])) {
            $result = array_merge($result, $data['force_display']);
        }

        return $result;
    }
}