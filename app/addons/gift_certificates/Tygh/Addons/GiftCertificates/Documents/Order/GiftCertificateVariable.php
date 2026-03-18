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


namespace Tygh\Addons\GiftCertificates\Documents\Order;


use Tygh\Template\Document\Order\Context;
use Tygh\Template\IActiveVariable;
use Tygh\Template\IVariable;
use Tygh\Tools\Formatter;

/**
 * Class GiftCertificateVariable
 * @package Tygh\Addons\GiftCertificates\Documents\Order
 */
class GiftCertificateVariable implements IVariable, IActiveVariable
{
    public $items = array();
    public $text = '';

    /**
     * GiftCertificateVariable constructor.
     *
     * @param Context   $context    Instance of order invoice context.
     * @param Formatter $formatter  Instance of formatter.
     */
    public function __construct(Context $context, Formatter $formatter)
    {
        $order = $context->getOrder();
        $codes = array();

        if (!empty($order->data['use_gift_certificates'])) {
            foreach ($order->data['use_gift_certificates'] as $code => $item) {
                $item['raw'] = array();

                $item['raw']['amount'] = $item['amount'];
                $item['raw']['cost'] = $item['cost'];

                $item['amount'] = $formatter->asPrice($item['amount']);
                $item['cost'] = $formatter->asPrice($item['cost']);

                $codes[] = __('gift_certificate', array(), $context->getLangCode()) . ': ' . $code . ' (' . $item['cost'] . ')';

                $this->items[$code] = $item;
            }
        }

        $this->text = implode('<br>', $codes);
    }

    /**
     * @inheritDoc
     */
    public static function attributes()
    {
        return array(
            'text', 'items' => array(
                '[0..N]' => array(
                    'gift_cert_id', 'amount', 'cost',
                    'raw' => array(
                        'amount', 'cost'
                    )
                )
            )
        );
    }
}