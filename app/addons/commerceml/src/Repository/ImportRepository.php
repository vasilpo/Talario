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


use Tygh\Addons\CommerceML\Dto\ImportDto;
use Tygh\Database\Connection;

/**
 * Class ImportRepository
 *
 * @package Tygh\Addons\CommerceML\Repository
 */
class ImportRepository
{
    const TABLE_NAME = 'commerceml_imports';

    /**
     * @var \Tygh\Database\Connection
     */
    private $db;

    /**
     * ImportRepository constructor.
     *
     * @param \Tygh\Database\Connection $db Database connection instance
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Saves import data
     *
     * @param \Tygh\Addons\CommerceML\Dto\ImportDto $import Import data
     *
     * @return \Tygh\Addons\CommerceML\Dto\ImportDto
     */
    public function save(ImportDto $import)
    {
        $result = $this->db->replaceInto(self::TABLE_NAME, $import->toArray());

        if (!$import->import_id && $result) {
            $import->import_id = (int) $result;
        }

        return $import;
    }

    /**
     * Finds import data by ID
     *
     * @param int $import_id Import ID
     *
     * @return \Tygh\Addons\CommerceML\Dto\ImportDto|null
     */
    public function findById($import_id)
    {
        $import_id = (int) $import_id;

        if (!$import_id) {
            return null;
        }

        $data = $this->db->getRow('SELECT * FROM ?:?p WHERE import_id = ?i', self::TABLE_NAME, $import_id);

        if (!$data) {
            return null;
        }

        return ImportDto::fromArray($data);
    }

    /**
     * @param string $catalog_id Catalog ID
     *
     * @return \Tygh\Addons\CommerceML\Dto\ImportDto|null
     */
    public function findByCatalogId($catalog_id)
    {
        if (!$catalog_id) {
            return null;
        }

        $data = $this->db->getRow('SELECT * FROM ?:?p WHERE catalog_id = ?s ORDER BY import_id DESC', self::TABLE_NAME, $catalog_id);

        if (!$data) {
            return null;
        }

        return ImportDto::fromArray($data);
    }

    /**
     * Removes import by ID
     *
     * @param int $import_id Import ID
     */
    public function remove($import_id)
    {
        $this->db->query('DELETE FROM ?:?p WHERE import_id = ?i', self::TABLE_NAME, $import_id);
    }
}
