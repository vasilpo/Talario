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

/**
 * Class AbstractCriteria is a base class that can be used for rating criteria provider.
 *
 * @package Tygh\Addons\VendorRating\Criteria
 */
abstract class AbstractCriteria implements CriteriaInterface
{
    /**
     * @var int
     */
    protected $company_id;

    /**
     * @var int
     */
    protected $start_rating_period;

    /** @inheritDoc */
    public function init($company_id, $start_rating_period)
    {
        $this->company_id = (int) $company_id;
        $this->start_rating_period = (int) $start_rating_period;
    }
}
