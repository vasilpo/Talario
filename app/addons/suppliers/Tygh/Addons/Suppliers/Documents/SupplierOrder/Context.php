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

namespace Tygh\Addons\Suppliers\Documents\SupplierOrder;


use Tygh\Template\Document\Order\Context as OrderContext;
use Tygh\Template\Document\Order\Order;

/**
 * Class Context
 * @package Tygh\Addons\Suppliers\Documents\SupplierOrder
 */
class Context extends OrderContext
{
    /** @var array */
    protected $supplier;

    /** @var array */
    protected $products = array();

    /**
     * Context constructor.
     *
     * @param Order $order      Instance of order.
     * @param array $supplier   Supplier data.
     */
    public function __construct(Order $order, array $supplier)
    {
        $this->order = $order;
        $this->supplier = $supplier;

        $products = $order->getProducts();

        foreach ($products as $key => $product) {
            if (
                (!empty($product['extra']['supplier_id']) && $product['extra']['supplier_id'] == $supplier['supplier_id'])
                || (fn_get_product_supplier_id($product['product_id']) == $supplier['supplier_id'])
            ) {
                $this->products[$key] = $product;
            }
        }
    }

    /**
     * Gets products.
     *
     * @return array
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Gets supplier data.
     *
     * @return array
     */
    public function getSupplier()
    {
        return $this->supplier;
    }
}