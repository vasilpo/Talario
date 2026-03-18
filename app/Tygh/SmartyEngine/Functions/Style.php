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

namespace Tygh\SmartyEngine\Functions;

use Smarty\FunctionHandler\Base;
use Smarty\Template;
use Tygh;
use Tygh\Registry;

class Style extends Base
{
    /**
     * phpcs:ignore
     * @param array    $params   Function args
     * @param Template $template Template
     *
     * @return string
     */
    //phpcs:ignore
    public function handle($params, Template $template)
    {
        list($_area) = Tygh::$app['view']->getArea();
        $params['area'] = !empty($params['area']) ? $params['area'] : $_area;
        $params['src'] = !empty($params['src']) ? $params['src'] : '';
        $location = Registry::get('config.current_location') . (strpos($params['src'], '/') === 0
                ? ''
                : ('/' . fn_get_theme_path('[relative]/[theme]', $params['area']) . '/css'));
        $url = $location . '/' . $params['src'];

        if (!empty($params['content'])) {
            return '<style' . (!empty($params['media']) ? (' media="' . $params['media'] . '"') : '') . '>' . $params['content'] . '</style>';
        }

        return '<link type="text/css" rel="stylesheet"' .
            (!empty($params['media']) ? (' media="' . $params['media'] . '"') : '') .
            ($params['area'] !== $_area ? ' data-ca-external="Y"' : '') .
            ' href="' . $url . '" />';
    }
}
