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


namespace Tygh\Addons\CommerceML\Repository;


use Tygh\Database\Connection;

/**
 * Class ImportRemovedEntityRepository
 *
 * @package Tygh\Addons\CommerceML\Repository
 */
class ImportRemovedEntityRepository
{
    const TABLE_NAME = 'commerceml_import_removed_entities';

    /**
     * @var \Tygh\Database\Connection
     */
    private $db;

    /**
     * ImportRemovedEntityRepository constructor.
     *
     * @param \Tygh\Database\Connection $db Database connection instance
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Adds record
     *
     * @param int    $company_id  Company ID
     * @param string $entity_type Entity type
     * @param string $entity_id   Entity id
     */
    public function add($company_id, $entity_type, $entity_id)
    {
        $this->db->replaceInto(self::TABLE_NAME, [
            'company_id'  => (int) $company_id,
            'entity_type' => (string) $entity_type,
            'entity_id'   => (string) $entity_id
        ]);
    }

    /**
     * Removes record
     *
     * @param int    $company_id  Company ID
     * @param string $entity_type Entity type
     * @param string $entity_id   Entity id
     */
    public function remove($company_id, $entity_type, $entity_id)
    {
        $this->db->query(
            'DELETE FROM ?:?p WHERE ?w',
            self::TABLE_NAME,
            [
                'company_id'  => (int) $company_id,
                'entity_type' => (string) $entity_type,
                'entity_id'   => (string) $entity_id
            ]
        );
    }

    /**
     * Checks if record exists
     *
     * @param int    $company_id  Company ID
     * @param string $entity_type Entity type
     * @param string $entity_id   Entity id
     *
     * @return bool
     */
    public function exists($company_id, $entity_type, $entity_id)
    {
        return (bool) $this->db->getField(
            'SELECT 1 FROM ?:?p WHERE ?w',
            self::TABLE_NAME,
            [
                'company_id'  => (int) $company_id,
                'entity_type' => (string) $entity_type,
                'entity_id'   => (string) $entity_id
            ]
        );
    }
}
