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

namespace Tygh\Template\Internal;


use Tygh\BlockManager\Layout;
use Tygh\Registry;
use Tygh\Template\IContext;
use Tygh\Themes\Styles;

/**
 * The context class for on-site notifications.
 *
 * @package Tygh\Template\Internal
 */
class Context implements IContext
{
    /** @var string */
    protected $lang_code;

    /** @var array */
    public $data;

    /** @var string */
    protected $area;

    /**
     * Context constructor.
     *
     * @param array     $data       Notification message data.
     * @param string    $area       Area code.
     * @param string    $lang_code  Language code.
     */
    public function __construct(array $data, $area, $lang_code)
    {
        $data['settings'] = Registry::get('settings');
        $data['lang_code'] = $lang_code;
        $data['language_direction'] = fn_is_rtl_language($lang_code) ? 'rtl' : 'ltr';

        $this->data = $data;
        $this->lang_code = $lang_code;
        $this->area = $area;
    }

    /**
     * @inheritDoc
     */
    public function getLangCode()
    {
        return $this->lang_code;
    }

    /**
     * @inheritDoc
     */
    public function getLanguageDirection()
    {
        return fn_is_rtl_language($this->lang_code) ? 'rtl' : 'ltr';
    }

    /**
     * @inheritDoc
     */
    public function getArea()
    {
        return $this->area;
    }
}
