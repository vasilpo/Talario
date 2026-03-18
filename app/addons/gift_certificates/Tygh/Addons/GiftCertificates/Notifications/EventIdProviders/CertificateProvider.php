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

namespace Tygh\Addons\GiftCertificates\Notifications\EventIdProviders;

use Tygh\Notifications\EventIdProviders\IProvider;

/**
 * Class CertificateProvider provides means to distinguish gift certificate-based notification event.
 *
 * @package Tygh\Addons\GiftCertificates\Notifications\EventIdProviders
 */
class CertificateProvider implements IProvider
{
    /**
     * @var string
     */
    protected $prefix = 'gift_certificate.';

    /**
     * @var string
     */
    protected $id;

    public function __construct(array $gift_cert_data)
    {
        $this->id = $this->prefix . $gift_cert_data['gift_cert_id'];
    }

    /** @inheritDoc */
    public function getId()
    {
        return $this->id;
    }
}
