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

namespace Tygh\Addons\MobileApp;

use Exception;
use Tygh\Storage;

/**
 * Provides methods for handling Firebase config files.
 *
 * @package Tygh\Addons\MobileApp\GoogleServicesConfig
 */
class GoogleServicesConfig
{
    const ANROID_OS = 'android';
    const IOS = 'ios';

    /** @var string */
    protected static $file_path = 'mobile_app';

    /** @var string */
    protected static $android_file_name = 'google-services.json';

    /** @var string */
    protected static $ios_file_name = 'GoogleService-Info.plist';

    /**
     * Uploads data
     *
     * @param array<array<string|int>> $uploaded_data Uploaded data
     * @param int                      $storefront_id Storefront identifier
     *
     * @return bool
     *
     * @throws \Exception Unknown OS type for definition of Firebase config file name.
     */
    public static function upload($uploaded_data, $storefront_id = 0)
    {
        $type_to_setting_name = [
            self::ANROID_OS => 'google_services_config_file_android',
            self::IOS       => 'google_services_config_file_ios'
        ];
        $result_size = 0;

        foreach ($type_to_setting_name as $type => $setting_name) {
            if (empty($uploaded_data[$setting_name]['path'])) {
                continue;
            }

            list($size) = Storage::instance('downloads')->put(self::getFullFilePath($type, $storefront_id), [
                'file'      => $uploaded_data[$setting_name]['path'],
                'overwrite' => true,
            ]);

            $result_size += $size;
        }

        return $result_size > 0;
    }

    /**
     * Checks if a file exists
     *
     * @param string $type          Type of OS
     * @param int    $storefront_id Storefront identifier
     *
     * @return bool
     *
     * @throws \Exception Unknown OS type for definition of Firebase config file name.
     */
    public static function isExist($type, $storefront_id = 0)
    {
        return Storage::instance('downloads')->isExist(self::getFullFilePath($type, $storefront_id));
    }

    /**
     * Gets file path
     *
     * @param string $type          Type of OS
     * @param int    $storefront_id Storefront identifier
     *
     * @return string
     *
     * @throws \Exception Unknown OS type for definition of Firebase config file name.
     */
    public static function getFilePath($type, $storefront_id = 0)
    {
        return Storage::instance('downloads')->getAbsolutePath(self::getFullFilePath($type, $storefront_id));
    }

    /**
     * Gets the file
     *
     * @param string $type          Type of OS
     * @param int    $storefront_id Storefront identifier
     *
     * @return bool
     *
     * @throws \Exception Unknown OS type for definition of Firebase config file name.
     */
    public static function getFile($type, $storefront_id = 0)
    {
        return Storage::instance('downloads')->get(self::getFullFilePath($type, $storefront_id));
    }

    /**
     * Deletes the file
     *
     * @param string $type          Type of OS
     * @param int    $storefront_id Storefront identifier
     *
     * @return bool
     *
     * @throws \Exception Unknown OS type for definition of Firebase config file name.
     */
    public static function deleteFile($type, $storefront_id = 0)
    {
        return Storage::instance('downloads')->delete(self::getFullFilePath($type, $storefront_id));
    }

    /**
     * Gets full file path
     *
     * @param string $type          Type of OS
     * @param int    $storefront_id Storefront identifier
     *
     * @return string
     *
     * @throws \Exception Unknown OS type for definition of Firebase config file name.
     */
    public static function getFullFilePath($type, $storefront_id)
    {
        return implode(DIRECTORY_SEPARATOR, [self::$file_path, $storefront_id, self::getFileNameByType($type)]);
    }

    /**
     * Gets config file name by type.
     *
     * @param string $type Type of OS
     *
     * @return string
     *
     * @throws \Exception Unknown OS type for definition of Firebase config file name.
     */
    public static function getFileNameByType($type)
    {
        if ($type === self::ANROID_OS) {
            $file_name = self::$android_file_name;
        } elseif ($type === self::IOS) {
            $file_name = self::$ios_file_name;
        } else {
            throw new Exception(__('mobile_app.unknown_os_type'));
        }

        return $file_name;
    }
}
