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

namespace Tygh\Addons\CallRequests\SmartyEngine\Extensions;

use Smarty\Extension\Base;
use Smarty\FunctionHandler\FunctionHandlerInterface;
use Tygh\Addons\CallRequests\SmartyEngine\Functions\CallPhone;
use Tygh\Addons\CallRequests\SmartyEngine\Functions\CallRequest;

class CallRequests extends Base
{
    /** @var array<string, ?FunctionHandlerInterface> $function_handlers */
    private $function_handlers = [];

    /**
     * @param string $function_name Func name
     */
    public function getFunctionHandler(string $function_name): ?FunctionHandlerInterface
    {
        if (isset($this->function_handlers[$function_name])) {
            return $this->function_handlers[$function_name];
        }

        //phpcs:disable
        switch ($function_name) {
            case 'call_phone':   $this->function_handlers[$function_name] = new CallPhone(); break;
            case 'call_request': $this->function_handlers[$function_name] = new CallRequest(); break;
        }
        //phpcs:enable

        return $this->function_handlers[$function_name] ?? null;
    }
}
