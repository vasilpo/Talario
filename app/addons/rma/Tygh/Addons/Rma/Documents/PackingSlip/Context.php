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


namespace Tygh\Addons\Rma\Documents\PackingSlip;

use Tygh\Template\Document\Order\Context as OrderContext;
use Tygh\Template\Document\Order\Order;
use Tygh\Enum\Addons\Rma\ReturnOperationStatuses;

/**
 * Class Context
 * @package Tygh\Template\Document\PackingSlip
 */
class Context extends OrderContext
{
    /** @var array */
    protected $return_info = array();

    /** @var array */
    protected $products = array();

    /**
     * Context constructor.
     *
     * @param Order $order          Instance of order.
     * @param array $return_info    Return data.
     */
    public function __construct(Order $order, array $return_info = array())
    {
        $this->return_info = $return_info;
        $this->order = $order;

        if (!empty($return_info['items'][ReturnOperationStatuses::APPROVED])) {
            foreach ($return_info['items'][ReturnOperationStatuses::APPROVED] as $item) {
                if (isset($order->data['products'][$item['item_id']]['product_code'])) {
                    $item['product_code'] = $order->data['products'][$item['item_id']]['product_code'];
                } else {
                    $item['product_code'] = '';
                }

                $this->products[] = $item;
            }
        }
    }

    /**
     * Gets return request data.
     * 
     * @return array
     */
    public function getReturnInfo()
    {
        return $this->return_info;
    }

    /**
     * @inheritDoc
     */
    public function getProducts()
    {
        return $this->products;
    }
}