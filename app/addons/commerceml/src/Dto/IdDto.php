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
 * Class IdDto
 *
 * @package Tygh\Addons\CommerceML\Dto
 */
class IdDto
{
    /**
     * @var string|int Local object ID
     */
    public $local_id;

    /**
     * @var string External object ID
     */
    public $external_id;

    /**
     * @var string ID which should be used as entity ID at storage
     */
    public $id;

    /**
     * UuidDto constructor.
     *
     * @param string $external_id External object ID
     * @param string $local_id    Local object ID
     */
    public function __construct($external_id = '', $local_id = '')
    {
        $this->external_id = (string) $external_id;
        $this->local_id = (string) $local_id;

        if ($this->external_id) {
            $this->id = $this->external_id;
        } else {
            $this->id = $this->local_id;
        }
    }

    /**
     * Gets external id if it exists otherwise local id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Checks if local ID specified
     *
     * @return bool
     */
    public function hasLocalId()
    {
        return !empty($this->local_id);
    }

    /**
     * Removes local ID
     */
    public function removeLocalId()
    {
        $this->local_id = '';
    }

    /**
     * @param string|null $external_id External object ID
     *
     * @return \Tygh\Addons\CommerceML\Dto\IdDto
     */
    public static function createByExternalId($external_id)
    {
        return new self((string) $external_id);
    }

    /**
     * @param string|null $local_id Local object ID
     *
     * @return \Tygh\Addons\CommerceML\Dto\IdDto
     */
    public static function createByLocalId($local_id)
    {
        return new self('', (string) $local_id);
    }
}
