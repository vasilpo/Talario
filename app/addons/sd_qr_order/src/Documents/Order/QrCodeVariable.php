<?php
/****************************************************************************
 *                                                                          *
 *   © Simtech Development Ltd.                                             *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 ***************************************************************************/

namespace Tygh\Addons\QrOrder\Documents\Order;

use Tygh\Enum\Addons\QrOrder\ImageSettings;
use Tygh\Template\Document\Order\Context;
use Tygh\Template\IVariable;
use Tygh\Storage;

class QrCodeVariable implements IVariable
{
    public $image;

    /**
     * QrCodeVariable constructor.
     *
     * @param Context $context Instance of order invoice context.
     */
    public function __construct(Context $context)
    {
        $order = $context->getOrder();
        $this->image = $this->getQrImage($order->getId());
    }

    /**
    * @param int $order_id
    * @return string URL Qr-code
    */
    private function getQrImage(int $order_id)
    {
        $structure = "qr_code_orders/{$order_id}/";
        $file = "order.png";

        $file_path = Storage::instance('images')->getAbsolutePath($structure . $file);
        $size = ImageSettings::SIZE;
        if (file_exists($file_path)) {
            $url = fn_url(Storage::instance('images')->getUrl($structure . $file), 'C');
            return "<img src=\"{$url}\" alt=\"Qr-code\" width=\"{$size}\" height=\"{$size}\">";
        }

        return '';
    }
}
