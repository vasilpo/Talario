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

namespace Tygh\Addons\GiftCertificates\Notifications\DataProviders;


use Tygh\Exceptions\DeveloperException;
use Tygh\Notifications\DataProviders\BaseDataProvider;

class GiftCertificateDataProvider extends BaseDataProvider
{
    protected $certificate = [];

    public function __construct(array $data)
    {
        if (empty($data['certificate_data'])) {
            throw new DeveloperException('The certificate must be defined.');
        }

        $this->certificate = $data['certificate_data'];

        $data['lang_code'] = $this->getLangCode();
        $data['certificate_status'] = $this->getCertificateStatus($data['lang_code']);
        $data['storefront_url'] = $this->getStorefrontUrl();
        $data['gift_cert_data'] = $this->certificate;

        parent::__construct($data);

    }

    protected function getLangCode()
    {
        $lang_code = CART_LANGUAGE;
        if (!empty($this->certificate['order_ids'])) {
            $order_info = fn_get_order_info(explode(',', $this->certificate['order_ids'])[0]);
            $lang_code = $order_info['lang_code'];
        }

        return $lang_code;
    }

    protected function getCertificateStatus($lang_code)
    {
        return fn_get_status_data(
            $this->certificate['status'],
            STATUSES_GIFT_CERTIFICATE,
            $this->certificate['gift_cert_id'],
            $lang_code
        );
    }

    protected function getStorefrontUrl()
    {
        $suffix = '';
        if (fn_allowed_for('ULTIMATE')) {
            $suffix = '?company_id=' . $this->certificate['company_id'];
        }

        return fn_url($suffix, 'C', 'http');
    }
}