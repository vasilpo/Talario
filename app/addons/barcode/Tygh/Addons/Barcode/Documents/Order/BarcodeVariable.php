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


namespace Tygh\Addons\Barcode\Documents\Order;


use Tygh\Registry;
use Tygh\Template\Document\Order\Context;
use Tygh\Template\IVariable;

/**
 * Class BarcodeVariable
 * @package Tygh\Addons\Barcode\Documents\Order
 */
class BarcodeVariable implements IVariable
{
    public $image;

    /**
     * BarcodeVariable constructor.
     *
     * @param Context $context Instance of order invoice context.
     */
    public function __construct(Context $context)
    {
        $order = $context->getOrder();

        $width = Registry::get('addons.barcode.width');
        $height = Registry::get('addons.barcode.height');
        $url = fn_url(sprintf('image.barcode?id=%s&type=%s&width=%s&height=%s&xres=%s&font=%s&no_session=Y',
            $order->getId(),
            Registry::get('addons.barcode.type'),
            $width,
            $height,
            Registry::get('addons.barcode.resolution'),
            Registry::get('addons.barcode.text_font')
        ));

        $this->image = "<img src=\"{$url}\" alt=\"BarCode\" width=\"{$width}\" height=\"{$height}\">";
    }
}