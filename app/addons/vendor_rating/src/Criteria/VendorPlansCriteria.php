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

use Tygh\Addons\VendorRating\ServiceProvider;
use Tygh\Application;

/**
 * Class VendorPlansCriteria provides values of vendor plan-specific rating criteria.
 *
 * @package Tygh\Addons\VendorRating\Criteria
 */
class VendorPlansCriteria extends AbstractCriteria
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
    public function getManualRating()
    {
        return $this->getPlanService()->getManualRating($this->getVendorPlanId());
    }

    /**
     * @return \Tygh\Addons\VendorRating\Service\VendorPlanService
     */
    protected function getPlanService()
    {
        return ServiceProvider::getVendorPlanService();
    }

    protected function getVendorPlanId()
    {
        $id = (int) $this->getDb()->getField(
            'SELECT plan_id FROM ?:companies WHERE company_id = ?i',
            $this->company_id
        );

        return $id;
    }

    /**
     * @return \Tygh\Database\Connection
     */
    protected function getDb()
    {
        return $this->application['db'];
    }
}
