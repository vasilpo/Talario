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

namespace Tygh\SmartyEngine\Filters\Pre;

use Smarty\Filter\FilterInterface;
use Smarty\Template;
use Tygh;

class TemplateWrapper implements FilterInterface
{
    /**
     * @param string   $code     Code
     * @param Template $template Template
     *
     * phpcs:ignore
     * @return mixed|string
     */
    public function filter($code, Template $template)
    {
        /** @var Tygh\SmartyEngine\Core $smarty */
        $smarty = $template->getSmarty();
        $cur_templ = fn_addon_template_overrides($template->template_resource, $smarty);
        $cur_templ = str_replace(Tygh::$app['view']->default_resource_type . ':', '', $cur_templ);

        $ignored_template = [
            'index.tpl',
            'common/pagination.tpl',
            'views/categories/components/menu_items.tpl',
            'views/block_manager/render/location.tpl',
            'views/block_manager/render/container.tpl',
            'views/block_manager/render/grid.tpl',
            'views/block_manager/render/block.tpl',
            'backend:common/template_editor.tpl',
            'backend:common/theme_editor.tpl',
            'backend:views/debugger/debugger.tpl',
        ];

        if (!in_array($cur_templ, $ignored_template) && fn_get_file_ext($cur_templ) === 'tpl') { // process only "real" templates (not eval'ed, etc.)
            $code =
                '{if $runtime.customization_mode.design == "Y" && $smarty.const.AREA == "C"}' .
                '{capture name="template_content"}' . $code . '{/capture}' .
                '{if $smarty.capture.template_content|trim}' .
                '{if $auth.area == "A"}' .
                '<span class="cm-template-box template-box" data-ca-te-template="' . $cur_templ . '" id="{set_id name="' . $cur_templ . '"}">' .
                '<div class="cm-template-icon icon-edit ty-icon-edit hidden"></div>' .
                '{$smarty.capture.template_content nofilter}<!--[/tpl_id]--></span>' .
                '{else}{$smarty.capture.template_content nofilter}{/if}{/if}' .
                '{else}' . $code . '{/if}';
        }

        return $code;
    }
}
