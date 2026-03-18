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

/**
 * Class VendorsCriteria provides values of vendor-specific rating criteria.
 *
 * @package Tygh\Addons\VendorRating\Criteria
 */
class VendorsCriteria extends AbstractCriteria
{
    /**
     * @return int
     */
    public function getManualRating()
    {
        return $this->getVendorService()->getManualRating($this->company_id);
    }

    /**
     * @return \Tygh\Addons\VendorRating\Service\VendorService
     */
    protected function getVendorService()
    {
        return ServiceProvider::getVendorService();
    }
}
