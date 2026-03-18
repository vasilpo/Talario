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
use Tygh\BlockManager\RenderManager;
use Tygh\BlockManager\SchemesManager;
use Tygh\Enum\SiteArea;

class RenderLocation extends Base
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
        if (!empty($params['dispatch'])) {
            $dispatch = (string) $params['dispatch'];
        } elseif ($template->getSmarty()->getTemplateVars('exception_status')) {
            $dispatch = 'no_page';
        } else {
            $dispatch = !empty($_REQUEST['dispatch']) ? (string) $_REQUEST['dispatch'] : 'index.index';
        }

        $smarty = $template->getSmarty();
        $original_smarty_vars = $smarty->getTemplateVars();
        $template_vars = $template->getTemplateVars();

        $smarty->assign($template_vars);

        $location_id = 0;
        if (!empty($params['location_id'])) {
            $location_id = $params['location_id'];
        }

        $area = !empty($params['area']) ?  $params['area'] : AREA;

        if (!empty($params['dynamic_object'])) {
            $dynamic_object = $params['dynamic_object'];
        } elseif (!empty($_REQUEST['dynamic_object']) && $area !== SiteArea::STOREFRONT) {
            $dynamic_object = $_REQUEST['dynamic_object'];
        } else {
            $dynamic_object_scheme = SchemesManager::getDynamicObject($dispatch, $area, $_REQUEST);
            if (!empty($dynamic_object_scheme) && !empty($_REQUEST[$dynamic_object_scheme['key']])) {
                $dynamic_object = [
                    'object_type' => $dynamic_object_scheme['object_type'],
                    'object_id'   => $_REQUEST[$dynamic_object_scheme['key']]
                ];
                $dispatch = $dynamic_object_scheme['customer_dispatch'];
            } else {
                $dynamic_object = [];
            }
        }

        $lang_code = !empty($params['lang_code']) ? $params['lang_code'] : DESCR_SL;

        $br = new RenderManager($dispatch, $area, $dynamic_object, $location_id, $lang_code);

        $html = $br->render();

        $smarty->assign($original_smarty_vars);

        return $html;
    }
}
