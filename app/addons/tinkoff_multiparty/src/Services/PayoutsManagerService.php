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

namespace Tygh\Addons\TinkoffMultiparty\Services;

use Tygh\Addons\TinkoffMultiparty\Payouts\PayoutsManager;
use Tygh\VendorPayouts;

class PayoutsManagerService
{
    /** @var \Tygh\Addons\TinkoffMultiparty\Payouts\PayoutsManager[] $instances */
    protected $instances = [];

    /** @var bool $can_collect_commission */
    protected $can_collect_commission;

    /**
     * PayoutsManagerService constructor.
     *
     * @param bool $can_collect_commission Can collect commission
     */
    public function __construct($can_collect_commission)
    {
        $this->can_collect_commission = $can_collect_commission;
    }

    /**
     * Gets manager
     *
     * @param int  $company_id               Company ID
     * @param bool $is_new_instance_required Is new instance required
     *
     * @return \Tygh\Addons\TinkoffMultiparty\Payouts\PayoutsManager
     */
    public function getManager($company_id, $is_new_instance_required = false)
    {
        if (!isset($this->instances[$company_id]) || $is_new_instance_required) {
            $vendor_payouts_instance = VendorPayouts::instance(['vendor' => $company_id]);
            $this->instances[$company_id] = new PayoutsManager(
                $company_id,
                $this->can_collect_commission,
                $vendor_payouts_instance
            );
        }

        return $this->instances[$company_id];
    }
}
