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

namespace Tygh\Addons\Recaptcha\GoogleRecaptchaV3;

use ReCaptcha\ReCaptcha;
use ReCaptcha\RequestMethod;
use ReCaptcha\RequestParameters;

class GoogleRecaptchaV3 extends ReCaptcha
{
    /**
     * Shared secret for the site.
     * @var type string
     */
    private $secret;

    /**
     * Method used to communicate  with service. Defaults to POST request.
     * @var RequestMethod
     */
    private $requestMethod;

    /**
     * Creates a configured instance to use the reCAPTCHA service.
     *
     * @param string        $secret        Shared secret between site and reCAPTCHA server.
     * @param RequestMethod $requestMethod Method used to send the request. Defaults to POST.
     *
     * @throws \RuntimeException If recaptcha secret is invalid.
     */
    public function __construct($secret, RequestMethod $requestMethod = null)
    {
        parent::__construct($secret, $requestMethod);
        $this->secret = $secret;
        $this->requestMethod = $requestMethod;
    }

    /**
     * Calls the reCAPTCHA siteverify API to verify whether the user passes CAPTCHA test.
     *
     * @param string $token The value of 'g-recaptcha-v3-token' in the submitted form.
     * @param string $remoteIp The end user's IP address.
     *
     * @return GoogleRecaptchaV3Response
     */
    public function verify($token, $remoteIp = null)
    {
        if (empty($token)) {
            return new GoogleRecaptchaV3Response(false, ['missing-input-response']);
        }

        $params = new RequestParameters($this->secret, $token, $remoteIp, self::VERSION);
        $rawResponse = $this->requestMethod->submit($params);
        return GoogleRecaptchaV3Response::fromJson($rawResponse);
    }
}
