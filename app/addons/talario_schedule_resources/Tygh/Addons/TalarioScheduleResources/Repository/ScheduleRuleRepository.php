<?php

namespace Tygh\Addons\TalarioScheduleResources\Repository;

class ScheduleRuleRepository
{
    public function create(array $data) { return (int) db_query('INSERT INTO ?:talario_resource_schedule_rules ?e', $data); }
    public function find($id) { return db_get_row('SELECT * FROM ?:talario_resource_schedule_rules WHERE rule_id = ?i', $id) ?: null; }
    public function update($id, array $data) { db_query('UPDATE ?:talario_resource_schedule_rules SET ?u WHERE rule_id = ?i', $data, $id); }
    public function findByResource($id) { return db_get_array('SELECT * FROM ?:talario_resource_schedule_rules WHERE resource_id = ?i ORDER BY weekday, starts_time', $id); }
}
