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

namespace Tygh\Addons\Suppliers\Notifications\DataProviders;


use Tygh\Enum\ProfileFieldLocations;
use Tygh\Exceptions\DeveloperException;
use Tygh\Notifications\DataProviders\BaseDataProvider;

class SuppliersDataProvider extends BaseDataProvider
{
    protected $order_info = [];

    protected $supplier = [];


    public function __construct(array $data)
    {
        if (empty($data['order_info']) || empty($data['supplier_id']) || empty($data['supplier'])) {
            throw new DeveloperException('The suppliers and order data must be defined.');
        }

        $this->order_info = $data['order_info'];
        $this->supplier = $data['supplier'];

        $data['lang_code'] = fn_get_company_language($this->supplier['company_id']);
        $data['order_status'] = fn_get_status_data($this->order_info['status'], STATUSES_ORDER, $this->order_info['order_id'], $data['lang_code']);
        $data['status_inventory'] = isset($data['order_status']['params']['inventory']) ? $data['order_status']['params']['inventory'] : null;
        $data['profile_fields'] = fn_get_profile_fields(ProfileFieldLocations::EXTRA_FIELDS, [], $data['lang_code']);
        $data['profields'] = $this->getProfields($data['profile_fields']);


        parent::__construct($data);

    }

    protected function getProfields($profile_fields)
    {
        $profields = [];
        foreach ($profile_fields as $section => $fields) {
            $profields[$section] = fn_fields_from_multi_level($fields, 'field_name', 'field_id');
        }

        return $profields;
    }
}