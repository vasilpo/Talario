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

namespace Tygh\Gdpr\DataUpdater;

use Tygh\Gdpr\SchemaManager;

/**
 * Updates user data according to schema.
 *
 * @package Tygh\Gdpr\DataModifier
 */
class UserPersonalDataUpdater implements IDataUpdater
{
    protected $schema_manager;

    public function __construct(SchemaManager $schema_manager)
    {
        $this->schema_manager = $schema_manager;
    }

    /**
     * @inheritdoc
     */
    public function update(array $user_data)
    {
        $user_data_schema = $this->schema_manager->getSchema('user_data');

        foreach ($user_data_schema as $data_item_name => $data_descriptor) {
            if (isset($data_descriptor['update_data_callback']) && isset($user_data[$data_item_name])) {
                call_user_func_array($data_descriptor['update_data_callback'], array($user_data[$data_item_name]));
            }
        }

        return true;
    }
}
