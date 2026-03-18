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

namespace Tygh\Addons\CallRequests\SmartyEngine\Functions;

use Smarty\FunctionHandler\Base;
use Smarty\Template;

class CallRequest extends Base
{
    /**
     * phpcs:ignore
     * @param array    $params   Modifier args
     * @param Template $template Template
     *
     * @return string
     *
     * @throws \Smarty\Exception Smarty exception.
     */
    //phpcs:ignore
    public function handle($params, Template $template)
    {
        $params = array_merge([
            'link_text' => __('call_requests.request_call'),
            'product'  => false,
        ], $params);

        $smarty = $template->getSmarty();

        $new_template = $smarty->createTemplate('addons/call_requests/views/call_requests/components/popup.tpl', null, null, $template);

        foreach ($params as $key => &$value) {
            $new_template->assign($key, $value);
        }

        return $new_template->fetch();
    }
}
