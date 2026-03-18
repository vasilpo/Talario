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

namespace Tygh\Notifications\DataProviders;


use Tygh\Enum\UserTypes;
use Tygh\Exceptions\DeveloperException;

class ProfileDataProvider extends BaseDataProvider
{
    protected $user_data = [];

    public function __construct(array $data)
    {
        if (empty($data['user_data'])) {
            throw new DeveloperException('The user_data must be defined.');
        }

        $this->user_data = $data['user_data'];

        $data['login_url'] = $this->getLoginUrl();
        $data['forgot_pass_url'] = $this->getForgotPassUrl();

        parent::__construct($data);

    }

    protected function getUrlSuffix()
    {
        $url_suffix = '';
        if ($this->user_data['user_type'] === UserTypes::CUSTOMER && !empty($user_data['company_id'])) {
            $url_suffix = '?company_id=' . $user_data['company_id'];
        }

        return $url_suffix;
    }

    protected function getLoginUrl()
    {
        $url_suffix = $this->getUrlSuffix();

        return fn_url('' . $url_suffix, $this->user_data['user_type']);
    }

    protected function getForgotPassUrl()
    {
        $url_suffix = $this->getUrlSuffix();

        return fn_url('auth.recover_password' . $url_suffix, $this->user_data['user_type']);
    }
}