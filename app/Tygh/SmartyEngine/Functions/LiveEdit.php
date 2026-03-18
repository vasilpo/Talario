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
use Tygh\Registry;

class LiveEdit extends Base
{
    /**
     * phpcs:ignore
     * @param array{name: string, phrase: string, need_render: bool, input_type: string} $params   Function args
     * @param Template                                                                   $template Template
     *
     * @return string|void
     */
    //phpcs:ignore
    public function handle($params, Template $template)
    {
        if (Registry::get('runtime.customization_mode.live_editor') && !empty($params['name'])) {
            $content = ' data-ca-live-editor-obj="' . $params['name'] . '"';

            if (!empty($params['phrase'])) {
                $phrase = htmlspecialchars($params['phrase']);
                $content .= ' data-ca-live-editor-phrase="' . $phrase . '"';
            }

            if (!empty($params['need_render'])) {
                $content .= ' data-ca-live-editor-need-render="true"';
            }

            if (!empty($params['input_type'])) {
                $content .= ' data-ca-live-editor-input-type="' . $params['input_type'] . '"';
            }

            return $content;
        }
    }
}
