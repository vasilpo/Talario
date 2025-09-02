<?php
/****************************************************************************
 *                                                                          *
 *   © Simtech Development Ltd.                                             *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 ***************************************************************************/

namespace Tygh\Addons\SdQrOrder\Helpers;

use Tygh\Registry;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QRGdImagePNG;
use Tygh\Storage;
use chillerlan\QRCode\Common\EccLevel;
use Tygh\Enum\Addons\SdQrOrder\ImageSettings;
use Tygh\Enum\SiteArea;

class QrHelper
{
    /**
     * Generates a QR code image for the given order ID.
     *
     * @param int $order_id Order ID
     *
     * @return void
     */
    public static function generateOrderQr(int $order_id): void
{
    if (empty($order_id)) {
        return;
    }

    $file_path = Storage::instance('images')
        ->getAbsolutePath(ImageSettings::DIRECTORY . "/{$order_id}/" . ImageSettings::FILE);

    if (file_exists($file_path)) {
        return;
    }

    $dir_path = dirname($file_path);
    if (!is_dir($dir_path)) {
        fn_mkdir($dir_path);
    }

    $options = new QROptions([
        'version'         => ImageSettings::VERSION,
        'eccLevel'        => EccLevel::L,
        'scale'           => ImageSettings::SCALE,
        'outputInterface' => QRGdImagePNG::class,
    ]);

    $url = fn_url("orders.details?order_id={$order_id}", SiteArea::ADMIN_PANEL);
    (new QRCode($options))->render($url, $file_path);
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
        $structure = ImageSettings::DIRECTORY . "/{$order_id}/";
        $file = ImageSettings::FILE;
        $file_path = Storage::instance('images')->getAbsolutePath($structure . $file);

        if (file_exists($file_path)) {
            return Storage::instance('images')->getUrl($structure . $file);
        }

        return null;
    }
}
