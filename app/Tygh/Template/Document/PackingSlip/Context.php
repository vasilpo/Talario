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


namespace Tygh\Template\Document\PackingSlip;

use Tygh\Template\Document\Order\Context as OrderContext;
use Tygh\Template\Document\Order\Order;

/**
 * The context class for the documents of the `packing_slip` type.
 *
 * @package Tygh\Template\Document\PackingSlip
 */
class Context extends OrderContext
{
    /** @var array */
    protected $shipment = array();

    /** @var array */
    protected $products = array();

    /**
     * Context constructor.
     *
     * @param Order $order      Instance of order.
     * @param array $shipment   Shipment data.
     */
    public function __construct(Order $order, array $shipment = array())
    {
        $this->shipment = $shipment;
        $this->order = $order;

        $products = $order->getProducts();

        if ($shipment) {
            foreach ($products as $key => $product) {
                if (isset($shipment['products'][$key])) {
                    $product['amount'] = $shipment['products'][$key];
                    $this->products[$key] = $product;
                }
            }
        } else {
            $this->products = $products;
        }
    }

    /**
     * Gets shipment data.
     * 
     * @return array
     */
    public function getShipment()
    {
        return $this->shipment;
    }

    /**
     * @inheritDoc
     */
    public function getProducts()
    {
        return $this->products;
    }
}