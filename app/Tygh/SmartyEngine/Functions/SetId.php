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
use Tygh\SmartyEngine\Core;

class SetId extends Base
{
    /**
     * Function generate id for template
     * phpcs:ignore
     * @param array    $params   Function args
     * @param Template $template Template
     *
     * @return string
     *
     * @throws \ReflectionException Ref exception.
     */
    //phpcs:ignore
    public function handle($params, Template $template)
    {
        /** @var Core $smarty */
        $smarty = $template->getSmarty();
        $template_tree = $this->templateTree($smarty);

        $tree = [];
        $count = count($template_tree) - 1;
        if ($template_tree[$count]['filename'] !== $params['name']) {
            array_push($tree, $params['name']);
        }
        $depth = $template_tree[$count]['depth'] + 1;
        for ($i = $count; $i >= 0; $i--) {
            if ($template_tree[$i]['depth'] < $depth) {
                $depth = $template_tree[$i]['depth'];
                array_unshift($tree, $template_tree[$i]['filename']);
            }

            if ($depth === 0) {
                break;
            }
        }
        $cur_id = join(',', $tree);

        return '[tpl_id ' . $cur_id . ']';
    }

    /**
     * @param Core $core Core
     *
     * @return array<array{filename: string, depth: int}>
     *
     * @throws \ReflectionException ReflectionException.
     */
    public function templateTree(Core $core)
    {
        $res = [];
        $depth = [];
        $d = 0;

        $ref_object = new \ReflectionObject($core);
        $ref_object = $ref_object->getParentClass();
        $property = $ref_object->getProperty('templates');
        $property->setAccessible(true);
        $templates = $property->getValue($core);

        foreach ($templates as $k => $v) {
            [$tpl, ] = explode('#', $k);

            if (empty($v->parent) || !property_exists($v->parent, 'template_resource')) {
                continue;
            }

            if (empty($depth[$v->parent->template_resource])) {
                $depth[$v->parent->template_resource] = ++$d;
            }

            $res[] = [
                'filename' => $tpl,
                'depth'    => $depth[$v->parent->template_resource]
            ];
        }

        return $res;
    }
}
