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

namespace Tygh\Addons\GraphqlApi;

use Tygh\Application;

// phpcs:disable

class Context
{
    /**
     * @var \Tygh\Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $auth;

    /**
     * @var string
     */
    protected $lang_code;

    /**
     * @var string
     */
    protected $currency;

    public function __construct(Application $app, array $auth, string $lang_code, string $currency)
    {
        $this->app = $app;
        $this->auth = $auth;
        $this->lang_code = $lang_code;
        $this->currency = $currency;
    }

    public function getApp(): Application
    {
        return $this->app;
    }

    public function getLanguageCode(): string
    {
        return $this->lang_code;
    }

    public function getCompanyId(): int
    {
        return (int) $this->auth['company_id'];
    }

    public function getUserId(): int
    {
        return (int) $this->auth['user_id'];
    }

    public function getUserType(): string
    {
        return $this->auth['user_type'];
    }

    public function getCurrencyCode(): string
    {
        return $this->currency;
    }

    public function getAuth(): array
    {
        return $this->auth;
    }
}

