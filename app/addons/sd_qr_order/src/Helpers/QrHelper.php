<?php
/****************************************************************************
 *                                                                          *
 *   © Simtech Development Ltd.                                             *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 ***************************************************************************/

namespace Tygh\Addons\QrOrder\Helpers;

use Tygh\Registry;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QRGdImagePNG;
use Tygh\Storage;
use chillerlan\QRCode\Common\EccLevel;
use Tygh\Enum\Addons\QrOrder\ImageSettings;

class QrHelper
{
    /**
     * Generates a QR code image for the given order ID.
     *
     * @param int $order_id Order ID
     *
     * @return void
     */
    public static function generateOrderQr(int $order_id)
    {
        $url = fn_url("orders.details?order_id={$order_id}", 'A');

        $structure = "qr_code_orders/{$order_id}/";
        $file = "order.png";

        $dir_path = Storage::instance('images')->getAbsolutePath($structure);
        if (!file_exists($dir_path)) {
            fn_mkdir($dir_path);
        }
        $file_path = $dir_path . $file;

        if (!file_exists($file_path)) {
            $options = new QROptions([
                'version'         => ImageSettings::VERSION,
                'eccLevel'        => EccLevel::L,
                'scale'           => ImageSettings::SCALE,
                'outputInterface' => QRGdImagePNG::class,
            ]);

            (new QRCode($options))->render($url, $file_path);
        }
    }

    /**
     * Get Qr-code for order.
     *
     * @param int $order_id Order ID
     *
     * @return string|null
     */
    public static function getOrderQr(int $order_id)
    {
        $structure = "qr_code_orders/{$order_id}/";
        $file = "order.png";
        $file_path = Storage::instance('images')->getAbsolutePath($structure . $file);

        if (file_exists($file_path)) {
            return Storage::instance('images')->getUrl($structure . $file);
        }

        return null;
    }
}
