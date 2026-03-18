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


namespace Tygh\Template\Document\Variables;


use Tygh\Registry;
use Tygh\Template\IActiveVariable;

/**
 * The class of the `settings` variable; it allows access to the store’s settings.
 *
 * @package Tygh\Template\Document\Variables
 */
class SettingsVariable implements IActiveVariable, \ArrayAccess
{
    /** @var array|mixed  */
    protected $settings = array();

    /**
     * SettingsVariable constructor.
     */
    public function __construct()
    {
        $this->settings = Registry::get('settings');
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return isset($this->settings[$offset]);
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return isset($this->settings[$offset]) ? $this->settings[$offset] : null;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        $this->settings[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        unset($this->settings[$offset]);
    }

    /**
     * @inheritDoc
     */
    public static function attributes()
    {
        $settings = Registry::get('settings');

        $settings = array_intersect_key($settings, array_flip(array(
            'General', 'Appearance', 'Checkout', 'Thumbnails', 'Sitemap'
        )));

        $get_attributes = function ($var) use (&$get_attributes) {
            $attributes = array();

            foreach ($var as $attr => $val) {
                if (is_array($val) && !empty($val)) {
                    $attributes[$attr] = $get_attributes($val);
                } else {
                    $attributes[] = $attr;
                }
            }

            return $attributes;
        };

        return $get_attributes($settings);
    }
}