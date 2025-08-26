<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

namespace Tygh\Addons\MobileApp\Notifications;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;

class Sender
{
    /**
     * @var Client $http
     */
    protected $http;

    /**
     * @var string $service_url
     */
    protected $service_url = 'https://fcm.googleapis.com/v1/projects/%s/messages:send';

    /**
     * @var array $addon_settings
     */
    protected $addon_settings;

    /**
     * @var \GuzzleHttp\Promise\PromiseInterface[] $notifications_pull
     */
    protected $notifications_pull;

    /**
     * Sender constructor.
     *
     * @param array{app_settings: array{utility: array{fcm_json_key: array{project_id: string}}}} $addon_settings Mobile app add-on settings
     * @param Client                                                                              $http           Network requester
     */
    public function __construct(array $addon_settings, Client $http)
    {
        $this->addon_settings = $addon_settings;
        $this->http = $http;
        $this->service_url = sprintf($this->service_url, $this->addon_settings['app_settings']['utility']['fcm_json_key']['project_id']);
    }

    /**
     * Puts notification into a pull.
     *
     * @param string                                             $receiver     Unique device token
     * @param \Tygh\Addons\MobileApp\Notifications\INotification $notification Configured notification
     *
     * @return \GuzzleHttp\Promise\PromiseInterface Thread ID on success, false on failure
     */
    public function addNotification($receiver, INotification $notification)
    {
        $data = $this->getPayload($receiver, $notification);
        $headers = $this->getHeaders();

        $promise = $this->http->postAsync(
            $this->service_url,
            [
                'headers' => $headers,
                'body'    => $data,
            ]
        );

        if ($promise) {
            $this->notifications_pull[] = $promise;
        }

        return $promise;
    }

    /**
     * Sends current notifications pull.
     *
     * @return bool Whether notification have been sent
     */
    public function send()
    {
        $are_notification_sent = false;

        if ($this->notifications_pull) {
            try {
                Utils::unwrap($this->notifications_pull);
            } catch (\Exception $e) {
                // silently ignore
            }

            $are_notification_sent = true;
        }

        $this->notifications_pull = [];

        return $are_notification_sent;
    }

    /**
     * Provides headers for notifications request.
     *
     * @return string[]
     */
    protected function getHeaders()
    {
        return [
            'Content-Type: application/json',
        ];
    }

    /**
     * Provides notification request payload.
     *
     * @param string                                             $receiver     Unique device token
     * @param \Tygh\Addons\MobileApp\Notifications\INotification $notification Configured notification
     *
     * @return string
     */
    protected function getPayload($receiver, INotification $notification)
    {
        $payload = $notification->getBody();

        $payload['message']['token'] = $receiver;

        return json_encode($payload);
    }
}
