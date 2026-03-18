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
use Tygh\BlockManager\Block;
use Tygh\BlockManager\RenderManager;
use Tygh\BlockManager\SchemesManager;
use Tygh\Enum\SiteArea;

class RenderBlock extends Base
{
    /**
     * Function generate id for template
     * phpcs:ignore
     * @param array    $params   Function args
     * @param Template $template Template
     *
     * @return string|void
     */
    //phpcs:ignore
    public function handle($params, Template $template)
    {
        if (empty($params['block_id'])) {
            return;
        }

        $block_id =  $params['block_id'];
        $snapping_id = !empty($params['snapping_id']) ? $params['snapping_id'] : 0;

        if (!empty($params['dispatch'])) {
            $dispatch = (string) $params['dispatch'];
        } else {
            $dispatch = !empty($_REQUEST['dispatch']) ? (string) $_REQUEST['dispatch'] : 'index.index';
        }

        $area = !empty($params['area']) ?  $params['area'] : AREA;

        if (!empty($params['dynamic_object'])) {
            $dynamic_object = $params['dynamic_object'];
        } elseif (!empty($_REQUEST['dynamic_object']) && $area !== SiteArea::STOREFRONT) {
            $dynamic_object = $_REQUEST['dynamic_object'];
        } else {
            $dynamic_object_scheme = SchemesManager::getDynamicObject($dispatch, $area);
            if (!empty($dynamic_object_scheme) && !empty($_REQUEST[$dynamic_object_scheme['key']])) {
                $dynamic_object = [
                    'object_type' => $dynamic_object_scheme['object_type'],
                    'object_id'   => $_REQUEST[$dynamic_object_scheme['key']],
                ];
            } else {
                $dynamic_object = [];
            }
        }

        $block = Block::instance()->getById($block_id, $snapping_id, $dynamic_object, DESCR_SL);

        $render_params = [
            'use_cache' => isset($params['use_cache']) ? (bool) $params['use_cache'] : true,
            'parse_js'  => isset($params['parse_js']) ? (bool) $params['parse_js'] : true,
        ];

        return RenderManager::renderBlock($block, [], 'C', $render_params);
    }
}
