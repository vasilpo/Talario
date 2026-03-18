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

namespace Tygh\Enum;

/**
 *  UsergroupTypes contains possible values for `usergroups`.`type` DB field.
 *
 * @package Tygh\Enum
 */
class UsergroupTypes
{
    const TYPE_ADMIN = 'A';
    const TYPE_CUSTOMER = 'C';

    /**
     * Gets all available user group types with descriptions
     *
     * @param array $exclude List of type codes of user groups to be excluded
     *
     * @return array
     */
    public static function getList(array $exclude = [])
    {
        $types = [
            self::TYPE_ADMIN    => __('administrator'),
            self::TYPE_CUSTOMER => __('customer'),
        ];

        /**
         * Allows to extend the available user group types
         *
         * @param array $types   User group privileges list
         * @param array $exclude List for user groups to be excluded
         */
        fn_set_hook('usergroup_types_get_list', $types, $exclude);

        return array_filter($types, function ($group_code) use ($exclude) {
            return !in_array($group_code, $exclude);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Gets all available user group types
     *
     * @return array
     */
    public static function getAll()
    {
        return array_keys(self::getList());
    }

    /**
     * Gets the map of user types to user group types
     *
     * @return array
     */
    public static function getMapUserType()
    {
        $map = [
            UserTypes::ADMIN => self::TYPE_ADMIN,
            UserTypes::CUSTOMER => self::TYPE_CUSTOMER,
        ];

        /**
         * Allows to extend the mapping of user types to user group types
         *
         * @param array $map The map of user types to user group types
         */
        fn_set_hook('usergroup_types_get_map_user_type', $map);

        return $map;
    }

    /**
     * Gets the user group by user type
     *
     * @param string|false $user_type
     */
    public static function getUsergroupType($user_type)
    {
        $map = self::getMapUserType();

        return isset($map[$user_type]) ? $map[$user_type] : false;
    }
}
