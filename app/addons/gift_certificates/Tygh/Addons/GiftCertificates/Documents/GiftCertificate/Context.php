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


namespace Tygh\Addons\GiftCertificates\Documents\GiftCertificate;


use Tygh\Template\IContext;

/**
 * Class Context
 * @package Tygh\Addons\GiftCertifications\Documents\GiftCertificate
 */
class Context implements IContext
{
    /** @var string */
    protected $lang_code;

    /** @var array{products?: array<array<string|int>>, company_id?: int} */
    protected $gift_certificate_data;

    /** @var string */
    protected $area;

    /**
     * Context constructor.
     *
     * @param array{products?: array<array<string|int>>, company_id?: int} $gift_certificate_data Gift certificate data.
     * @param string                                                       $lang_code             Language code.
     * @param string                                                       $area                  Area identifier.
     */
    public function __construct(array $gift_certificate_data, $lang_code, $area = AREA)
    {
        if (!empty($gift_certificate_data['products']) && isset($gift_certificate_data['company_id'])) {
            foreach ($gift_certificate_data['products'] as &$product) {
                $product['company_id'] = $gift_certificate_data['company_id'];
            }

            unset($product);
        }
        $this->gift_certificate_data = $gift_certificate_data;
        $this->lang_code = $lang_code;
        $this->area = $area;
    }

    /**
     * Gets gift certificate data.
     *
     * @return array
     */
    public function getCertificateData()
    {
        return $this->gift_certificate_data;
    }

    /**
     * @inheritDoc
     */
    public function getLangCode()
    {
        return $this->lang_code;
    }

    /**
     * @inheritDoc
     */
    public function getLanguageDirection()
    {
        return fn_is_rtl_language($this->lang_code) ? 'rtl' : 'ltr';
    }

    /**
     * @inheritDoc
     */
    public function getArea()
    {
        return $this->area;
    }
}
