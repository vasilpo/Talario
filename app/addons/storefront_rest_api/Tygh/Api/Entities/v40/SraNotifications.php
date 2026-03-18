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

namespace Tygh\Api\Entities\v40;

use Tygh\Addons\StorefrontRestApi\ASraEntity;
use Tygh\Api\Response;
use Tygh\Providers\StorefrontProvider;
use Tygh\Registry;

class SraNotifications extends ASraEntity
{
    /** @inheritdoc */
    public function index($id = '', $params = array())
    {
        return array(
            'status' => Response::STATUS_METHOD_NOT_ALLOWED,
            'data'   => array(),
        );
    }

    /** @inheritdoc */
    public function create($params)
    {
        $status = Response::STATUS_BAD_REQUEST;
        $data = [];

        $params = $this->buildSubscriptionDetails($params);

        $is_valid = true;
        foreach ($params as $param_name => $param) {
            if (!$param) {
                $is_valid = false;
                $data['message'] = __('api_required_field', ['[field]' => $param_name]);
                break;
            }
        }

        if ($is_valid) {
            $data['id'] = $this->addSubscription($params);
            $status = Response::STATUS_CREATED;
        }

        return [
            'status' => $status,
            'data'   => $data,
        ];
    }

    /** @inheritdoc */
    public function update($id, $params)
    {
        return array(
            'status' => Response::STATUS_METHOD_NOT_ALLOWED,
            'data'   => array(),
        );
    }

    /** @inheritdoc */
    public function delete($id)
    {
        $status = Response::STATUS_NOT_FOUND;
        if ($this->removeSubscription($id)) {
            $status = Response::STATUS_NO_CONTENT;
        }

        return array(
            'status' => $status,
            'data'   => array(),
        );
    }

    /** @inheritdoc */
    public function privileges()
    {
        if (!static::isAddonEnabled()) {
            return array();
        }

        $privileges = array(
            'create' => true,
            'delete' => true,
        );

        return $privileges;
    }

    /** @inheritdoc */
    public function privilegesCustomer()
    {
        if (!static::isAddonEnabled()) {
            return array();
        }

        $privileges = array(
            'create' => true,
            'delete' => true,
        );

        return $privileges;
    }

    /**
     * Adds a notifications subscription.
     *
     * @param array $params Subscription information
     *
     * @return int Subscription ID
     */
    protected function addSubscription(array $params)
    {
        return fn_mobile_app_update_notification_subscription(
            $this->auth['user_id'],
            $params['device_id'],
            $params['platform'],
            $params['locale'],
            $params['token'],
            $params['storefront_id']
        );
    }

    /**
     * Removes a notifications subscription.
     *
     * @param int $id Subscription ID
     *
     * @return int Number of removed subscriptions
     */
    protected function removeSubscription($id)
    {
        return fn_mobile_app_remove_notification_subscriptions(array(
            'user_id'         => $this->auth['user_id'],
            'subscription_id' => $id,
        ));
    }

    /**
     * Populates and sanitizes details of a notification subscription.
     *
     * @param array $params Parameters passed in API request
     *
     * @return array Notification details
     */
    protected function buildSubscriptionDetails(array $params)
    {
        $params = [
            'device_id'     => $this->safeGet($params, 'device_id', null),
            'platform'      => $this->safeGet($params, 'platform', null),
            'locale'        => $this->safeGet($params, 'locale', null),
            'token'         => $this->safeGet($params, 'token', null),
            'storefront_id' => $this->safeGet($params, 'storefront_id', StorefrontProvider::getStorefront()->storefront_id),
        ];

        fn_trim_helper($params);

        return $params;
    }

    /**
     * Checks whether the Mobile app add-on enabled.
     *
     * @return bool
     */
    public static function isAddonEnabled()
    {
        return Registry::ifGet('addons.mobile_app.status', 'D') === 'A';
    }
}
