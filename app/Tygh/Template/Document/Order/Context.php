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

namespace Tygh\Template\Document\Order;

use Tygh\Template\IContext;

/**
 * The context class for the documents with the `order` type (Invoice, Order summary).
 *
 * @package Tygh\Template\Document\Order
 */
class Context implements IContext
{
    /** @var Order */
    protected $order;

    /** @var string */
    protected $area;

    /**
     * Context constructor.
     *
     * @param Order  $order Instance of order.
     * @param string $area  Area identifier.
     */
    public function __construct(Order $order, $area = AREA)
    {
        /**
         * Allows to change the context of documents of the “order” type.
         *
         * @param self   $this      Instance of order.
         * @param int    $order_id  Order identifier.
         * @param string $lang_code Language code.
         * @param string $area      Area identifier.
         */
        fn_set_hook('template_document_order_context_init', $this, $order, $area);

        $this->order = $order;
        $this->area = $area;
    }

    /**
     * Gets instance of order.
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Gets products
     *
     * @return array
     */
    public function getProducts()
    {
        return $this->getOrder()->getProducts();
    }

    /**
     * @inheritdoc
     */
    public function getLangCode()
    {
        return $this->order->lang_code;
    }

    /**
     * @inheritDoc
     */
    public function getLanguageDirection()
    {
        return fn_is_rtl_language($this->order->lang_code) ? 'rtl' : 'ltr';
    }

    /**
     * Gets order currency.
     *
     * @return string Currency code
     */
    public function getCurrencyCode()
    {
        return $this->getOrder()->getCurrencyCode();
    }

    /**
     * @inheritDoc
     */
    public function getArea()
    {
        return $this->area;
    }
}
