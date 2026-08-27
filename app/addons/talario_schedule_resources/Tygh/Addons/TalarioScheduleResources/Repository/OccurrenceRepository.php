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
}
