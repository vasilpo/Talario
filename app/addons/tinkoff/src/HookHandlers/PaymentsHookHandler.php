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

namespace Tygh\Addons\Tinkoff\HookHandlers;

class PaymentsHookHandler
{
    /**
     * The `get_payment_processors_post` hook handler.
     *
     * Actions performed:
     *     - Adds specific attributes to some payment processor for categorization.
     *
     * @param string                            $lang_code  Language code.
     * @param array<array<string, string|bool>> $processors Payment processors.
     *
     * @see \fn_get_payment_processors()
     *
     * @return void
     */
    public function onGetPaymentProcessorsPost($lang_code, array &$processors)
    {
        foreach ($processors as &$processor) {
            if ($processor['addon'] !== 'tinkoff') {
                continue;
            }
            $processor['russian'] = true;
        }
        unset($processor);
    }
}
