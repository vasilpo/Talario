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


namespace Tygh\Addons\GiftCertificates\Documents\GiftCertificate\Variables;

use Tygh\Addons\GiftCertificates\Documents\GiftCertificate\Context;

/**
 * Class CompanyVariable
 * @package Tygh\Addons\GiftCertificates\Documents\GiftCertificate\Variables
 */
class CompanyVariable extends \Tygh\Template\Document\Variables\CompanyVariable
{
    public function __construct(Context $context, array $config = array())
    {
        $gift_certificate = $context->getCertificateData();
        $company_id = isset($gift_certificate['company_id']) ? $gift_certificate['company_id'] : 0;

        parent::__construct($config, $company_id, $context->getLangCode(), $context);
    }
}
