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

namespace Tygh\Addons\Recaptcha\RequestMethod;


use ReCaptcha\RequestMethod\Post as BasePost;
use ReCaptcha\RequestMethod\CurlPost;
use ReCaptcha\RequestParameters;

/**
 * Sends POST requests to the reCAPTCHA service
 */
class Post extends BasePost
{
    /**
     * @inheritdoc
     */
    public function submit(RequestParameters $params)
    {
        $result = parent::submit($params);

        if ($result === false && function_exists('curl_init')) {
            $curl_post = new CurlPost();
            $result = $curl_post->submit($params);
        }

        return $result;
    }
}