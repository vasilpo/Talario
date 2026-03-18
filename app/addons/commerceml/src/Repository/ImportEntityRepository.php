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


use Tygh\Addons\CommerceML\Dto\ImportItemDto;
use Tygh\Database\Connection;

/**
 * Class ImportEntityRepository
 *
 * @package Tygh\Addons\CommerceML\Repository
 */
class ImportEntityRepository
{
    const TABLE_NAME = 'commerceml_import_entities';

    /**
     * @var \Tygh\Database\Connection
     */
    private $db;

    /**
     * ImportEntityRepository constructor.
     *
     * @param \Tygh\Database\Connection $db Database connection instance
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Saves import entity data
     *
     * @param \Tygh\Addons\CommerceML\Dto\ImportItemDto $entity Import entity instnace
     */
    public function save(ImportItemDto $entity)
    {
        $this->db->replaceInto(self::TABLE_NAME, $this->getImportItemData($entity));
    }

    /**
     * Saves entites in batch
     *
     * @param array<\Tygh\Addons\CommerceML\Dto\ImportItemDto> $entities Entites
     */
    public function batchSave(array $entities)
    {
        $list = array_map(function (ImportItemDto $entity_dto) {
            return $this->getImportItemData($entity_dto);
        }, $entities);

        $this->db->replaceInto(self::TABLE_NAME, $list, true);
    }

    /**
     * Finds entity data
     *
     * @param int    $import_id   Import ID
     * @param string $entity_type Entity type
     * @param string $entity_id   Entity ID
     *
     * @return \Tygh\Addons\CommerceML\Dto\ImportItemDto|null
     */
    public function findEntityData($import_id, $entity_type, $entity_id)
    {
        $data = $this->db->getRow(
            'SELECT * FROM ?:?p WHERE import_id = ?i AND entity_type = ?s AND entity_id = ?s',
            self::TABLE_NAME,
            $import_id,
            $entity_type,
            $entity_id
        );

        if (!$data) {
            return null;
        }

        return ImportItemDto::fromArray($data);
    }

    /**
     * Removes records by import ID
     *
     * @param int $import_id Import ID
     */
    public function removeByImportId($import_id)
    {
        $this->db->query('DELETE FROM ?:?p WHERE import_id = ?i', self::TABLE_NAME, $import_id);
    }

    /**
     * Removes entity
     *
     * @param int    $import_id   Import ID
     * @param string $entity_type Entity type
     * @param string $entity_id   Entity ID
     */
    public function remove($import_id, $entity_type, $entity_id)
    {
        $this->db->query(
            'DELETE FROM ?:?p WHERE import_id = ?i AND entity_type = ?s AND entity_id = ?s',
            self::TABLE_NAME,
            $import_id,
            $entity_type,
            $entity_id
        );
    }

    /**
     * Finds next record for import
     *
     * @param int    $import_id   Import ID
     * @param string $entity_type Entity type
     * @param string $process_id  Process ID
     *
     * @return \Tygh\Addons\CommerceML\Dto\ImportItemDto|null
     */
    public function findNextRecord($import_id, $entity_type, $process_id)
    {
        $data = $this->db->getRow(
            'SELECT * FROM ?:?p WHERE import_id = ?i AND entity_type = ?s AND process_id IS NULL'
            . ' ORDER BY microtime ASC LIMIT 1'
            . ' FOR UPDATE',
            self::TABLE_NAME,
            $import_id,
            $entity_type,
            $process_id
        );

        if (!$data) {
            return null;
        }

        $this->db->query(
            'UPDATE ?:?p SET process_id = ?s WHERE import_id = ?i AND entity_type = ?s AND entity_id = ?s',
            self::TABLE_NAME,
            $process_id,
            $data['import_id'],
            $data['entity_type'],
            $data['entity_id']
        );

        return ImportItemDto::fromArray($data);
    }

    /**
     * Converts import item instance to array
     *
     * @param \Tygh\Addons\CommerceML\Dto\ImportItemDto $import_item_dto Import item DTO
     *
     * @return array<string, string|int|float>
     */
    private function getImportItemData(ImportItemDto $import_item_dto)
    {
        return array_merge($import_item_dto->toArray(), [
            'microtime' => microtime(true) * 10000
        ]);
    }
}
