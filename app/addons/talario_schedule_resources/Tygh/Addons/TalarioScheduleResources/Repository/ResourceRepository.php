<?php

namespace Tygh\Addons\TalarioScheduleResources\Repository;

class ResourceRepository
{
    public function create(array $data) { return (int) db_query('INSERT INTO ?:talario_resources ?e', $data); }
    public function find($id) { return db_get_row('SELECT * FROM ?:talario_resources WHERE resource_id = ?i', $id) ?: null; }
    public function update($id, array $data) { db_query('UPDATE ?:talario_resources SET ?u WHERE resource_id = ?i', $data, $id); }
    public function findByCompany($company_id) { return db_get_array('SELECT * FROM ?:talario_resources WHERE company_id = ?i ORDER BY name', $company_id); }
}
