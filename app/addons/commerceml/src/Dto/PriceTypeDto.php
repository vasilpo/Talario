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


namespace Tygh\Addons\CommerceML\Dto;

/**
 * Class PriceTypeDto
 *
 * @package Tygh\Addons\CommerceML\Dto
 */
class PriceTypeDto implements RepresentEntityDto
{
    use RepresentEntitDtoTrait;

    const REPRESENT_ENTITY_TYPE = 'price_type';

    const TYPE_BASE_PRICE = 'base_price';

    const TYPE_LIST_PRICE = 'list_price';

    const TYPE_USERGROUP = 'usergroup';

    /**
     * @var \Tygh\Addons\CommerceML\Dto\IdDto
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var \Tygh\Addons\CommerceML\Dto\PropertyDtoCollection
     */
    public $properties;

    /**
     * PriceTypeDto constructor.
     */
    public function __construct()
    {
        $this->properties = new PropertyDtoCollection();
    }

    /**
     * Creates local id by usergroup ID
     *
     * @param string $usergroup_id Usergroup ID
     *
     * @return string
     */
    public static function createLocalIdByUsergroupId($usergroup_id)
    {
        return sprintf('%s__%s', self::TYPE_USERGROUP, $usergroup_id);
    }

    /**
     * Parses local ID
     *
     * @param string $local_id Local ID
     *
     * @return array<string>
     */
    public static function parseLocalId($local_id)
    {
        return explode('__', $local_id);
    }
}
