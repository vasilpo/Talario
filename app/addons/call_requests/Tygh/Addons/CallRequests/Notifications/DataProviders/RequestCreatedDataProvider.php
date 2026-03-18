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

namespace Tygh\Addons\CallRequests\Notifications\DataProviders;

use Tygh\Enum\SiteArea;
use Tygh\Exceptions\DeveloperException;
use Tygh\Notifications\DataProviders\BaseDataProvider;

class RequestCreatedDataProvider extends BaseDataProvider
{
    protected $call_request = [];

    public function __construct(array $data)
    {
        if (empty($data['call_request_data'])) {
            throw new DeveloperException('The call request must be defined.');
        }

        $this->call_request = $data['call_request_data'];

        $data['url'] = $this->getUrl();
        $data['customer'] = $this->call_request['name'];
        $data['phone_number'] = $this->call_request['phone'];
        $data['time_from'] = $this->getTimeFrom();
        $data['time_to'] = $this->getTimeTo();

        parent::__construct($data);
    }

    /**
     * Gets Url
     *
     * @return string
     */
    protected function getUrl()
    {
        $area = SiteArea::ADMIN_PANEL;

        if (!empty($this->call_request['company_id']) && fn_allowed_for('MULTIVENDOR')) {
            $area = SiteArea::VENDOR_PANEL;
        }

        return fn_url('call_requests.manage?id=' . $this->call_request['request_id'], $area, 'current', $this->getLanguageCode());
    }

    protected function getLanguageCode()
    {
        $lang_code = fn_get_company_language($this->call_request['company_id']);
        if (empty($lang_code)) {
            $lang_code = CART_LANGUAGE;
        }

        return $lang_code;
    }

    protected function getTimeFrom()
    {
        if (isset($this->call_request['time_from'])) {
            return $this->call_request['time_from'] ?: CALL_REQUESTS_DEFAULT_TIME_FROM;
        }

        return null;
    }

    protected function getTimeTo()
    {
        if (isset($this->call_request['time_to'])) {
            return $this->call_request['time_to'] ?: CALL_REQUESTS_DEFAULT_TIME_TO;
        }

        return null;
    }
}