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

namespace Tygh\Addons\VendorRating\Criteria;

use Tygh\Application;
use Tygh\Enum\ObjectStatuses;

/**
 * Class ProductsCriteria provides values of product-specific rating criteria.
 *
 * @package Tygh\Addons\VendorRating\Criteria
 */
class ProductsCriteria extends AbstractCriteria
{
    /**
     * @var \Tygh\Application
     */
    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @return int
     */
    public function getActiveCount()
    {
        $count = (int) $this->getDb()->getField(
            'SELECT COUNT(*) FROM ?:products WHERE ?w',
            [
                'product_type' => 'P',
                'company_id'   => $this->company_id,
                'status'       => ObjectStatuses::ACTIVE,
                ['timestamp', '>=', $this->start_rating_period],
            ]
        );

        return $count;
    }

    /**
     * @return \Tygh\Database\Connection
     */
    protected function getDb()
    {
        return $this->application['db'];
    }
}
