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

/**
 * Class ReturnsCriteria provides values of return request-specific rating criteria.
 *
 * @package Tygh\Addons\VendorRating\Criteria
 */
class ReturnsCriteria extends AbstractCriteria
{
    /**
     * @var \Tygh\Application
     */
    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function getCount()
    {
        list(, $params) = fn_rma_get_returns(
            [
                'company_id' => $this->company_id,
                'period'     => 'C',
                'time_from'  => $this->getFormatter()->asDatetime($this->start_rating_period),
            ],
            1
        );

        return (int) $params['total_items'];
    }

    /**
     * @return \Tygh\Tools\Formatter
     */
    protected function getFormatter()
    {
        return $this->application['formatter'];
    }
}
