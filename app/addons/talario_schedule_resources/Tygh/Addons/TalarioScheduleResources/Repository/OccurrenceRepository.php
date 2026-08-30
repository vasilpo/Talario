<?php

namespace Tygh\Addons\TalarioScheduleResources\Repository;

class OccurrenceRepository
{
    public function create(array $data) { return (int) db_query('INSERT INTO ?:talario_resource_occurrences ?e', $data); }
    public function find($id) { return db_get_row('SELECT * FROM ?:talario_resource_occurrences WHERE occurrence_id = ?i', $id) ?: null; }
    public function findByResourceAndRange($id, $from, $to)
    {
        return db_get_array('SELECT * FROM ?:talario_resource_occurrences WHERE resource_id = ?i AND starts_at >= ?s AND starts_at < ?s ORDER BY starts_at', $id, $from, $to);
    }
    public function upsert(array $data)
    {
        $existing_id = (int) db_get_field(
            'SELECT occurrence_id FROM ?:talario_resource_occurrences WHERE resource_id = ?i AND starts_at = ?s',
            $data['resource_id'],
            $data['starts_at']
        );
        if ($existing_id) {
            unset($data['created_at']);
            db_query('UPDATE ?:talario_resource_occurrences SET ?u WHERE occurrence_id = ?i', $data, $existing_id);
            return $existing_id;
        }
        return $this->create($data);
    }
    public function disableExcept($resource_id, $from, $to, array $starts_at)
    {
        $condition = $starts_at ? db_quote(' AND starts_at NOT IN (?a)', $starts_at) : '';
        db_query(
            'UPDATE ?:talario_resource_occurrences SET status = ?s, updated_at = ?i '
            . 'WHERE resource_id = ?i AND starts_at >= ?s AND starts_at < ?s ?p',
            'D', TIME, $resource_id, $from, $to, $condition
        );
    }
}
