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

namespace Tygh\Addons\GraphqlApi\Validator;

use Tygh\Addons\GraphqlApi\Operation\OperationInterface;
use Tygh\Enum\UserTypes;

class PrivilegeValidator
{
    public function validate(int $user_id, string $user_type, OperationInterface $handler): bool
    {
        if ($user_type === UserTypes::CUSTOMER) {
            $privilege = $handler->getCustomerPrivilege();
        } else {
            $privilege = $handler->getPrivilege();
        }

        if (is_bool($privilege)) {
            return $privilege;
        }

        $has_privilege = fn_check_user_access($user_id, $privilege);

        return $has_privilege;
    }
}
