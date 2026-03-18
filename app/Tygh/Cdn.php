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

namespace Tygh;

use Tygh\Exceptions\DeveloperException;

class Cdn
{
    private static $_instance = null;

    /**
     * Gets CDN object instance
     *
     * @return \Tygh\Backend\Cdn\ABackend CDN object instance
     * @throws \Tygh\Exceptions\DeveloperException
     */
    public static function instance()
    {
        if (empty(self::$_instance)) {
            $backend = Registry::get('config.cdn_backend');

            if (empty($backend)) {
                throw new DeveloperException('CDN: undefined CDN backend');
            }

            $options = Registry::getOrSetCache('init_cdn_settings', ['settings_objects'], 'static', static function () {
                $options = Settings::instance()->getValue('cdn', '');
                $options = !empty($options) ? unserialize($options) : [];

                return serialize($options);
            });

            $class = '\\Tygh\\Backend\\Cdn\\' . ucfirst($backend);
            self::$_instance = new $class(unserialize($options));
        }

        return self::$_instance;
    }
}
