<?php

namespace Tygh\Addons\TalarioScheduleResources\Repository;

class LocationRepository
{
    public function create(array $data)
    {
        return (int) db_query('INSERT INTO ?:talario_locations ?e', $data);
    }

    public function find($location_id)
    {
        return db_get_row('SELECT * FROM ?:talario_locations WHERE location_id = ?i', $location_id) ?: null;
    }

    public function update($location_id, array $data)
    {
        db_query('UPDATE ?:talario_locations SET ?u WHERE location_id = ?i', $data, $location_id);
    }

    public function findByCompany($company_id)
    {
        return db_get_array('SELECT * FROM ?:talario_locations WHERE company_id = ?i ORDER BY name', $company_id);
    }
}
