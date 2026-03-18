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

namespace Tygh\Notifications\Transports\Internal;

use Tygh\Exceptions\DeveloperException;
use Tygh\Notifications\Transports\BaseMessageSchema;
use Tygh\Notifications\Transports\ITransport;
use Tygh\Tools\Url;

/**
 * Class InternalTransport implements a transport that creates notifications in the Notifications center
 * based on an event message.
 *
 * @package Tygh\Events\Transports
 */
class InternalTransport implements ITransport
{
    /**
     * @var \Tygh\NotificationsCenter\NotificationsCenter
     */
    protected $notifications_center;

    /**
     * @var \Tygh\Database\Connection
     */
    protected $db;

    /**
     * @var \Tygh\NotificationsCenter\IFactory
     */
    protected $factory;

    /**
     * @var \Tygh\Notifications\Transports\Internal\ReceiverFinderFactory
     */
    protected $receiver_finder_factory;

    public function __construct(
        $notifications_center,
        ReceiverFinderFactory $receiver_finder_factory
    ) {
        $this->notifications_center = $notifications_center;
        $this->receiver_finder_factory = $receiver_finder_factory;
    }

    public static function getId()
    {
        return 'internal';
    }

    /**
     * @inheritDoc
     */
    public function process(BaseMessageSchema $schema, array $receiver_search_conditions)
    {
        if (!$schema instanceof InternalMessageSchema) {
            throw new DeveloperException('Input data should be instance of InternalMessageSchema');
        }

        $receivers = $this->getReceivers($receiver_search_conditions, $schema);

        foreach ($receivers as $user_id => $area) {
            $notificaion_data = array_filter([
                'user_id'       => $user_id,
                'title'         => $schema->title,
                'message'       => $schema->message,
                'severity'      => $schema->severity,
                'section'       => $schema->section,
                'tag'           => $schema->tag,
                'area'          => $area,
                'action_url'    => $schema->action_url,
                'is_read'       => $schema->is_read,
                'timestamp'     => $schema->timestamp,
                'language_code' => $schema->language_code,
                'template_code' => $schema->template_code,
                'data'          => $schema->data,
            ]);

            $this->notifications_center->add($notificaion_data);
        }

        return true;
    }

    /**
     * Gets message receivers.
     *
     * @param \Tygh\Notifications\Receivers\SearchCondition[]               $receiver_search_conditions Receiver search conditions.
     * @param \Tygh\Notifications\Transports\Internal\InternalMessageSchema $schema                     Internal message schema
     *
     * @return array<int, string>
     */
    protected function getReceivers(array $receiver_search_conditions, InternalMessageSchema $schema)
    {
        $users = [];
        foreach ($receiver_search_conditions as $condition) {
            $finder = $this->receiver_finder_factory->get($condition->getMethod());
            $users += $finder->find($condition->getCriterion(), $schema);
        }

        $users = $this->filterReceiversByPermissons($users, $schema);

        return $users;
    }

    /**
     * Filters receivers considering the availability of the message action url based on privileges.
     *
     * @param array<int, string>                                            $users  Recievers of message.
     * @param \Tygh\Notifications\Transports\Internal\InternalMessageSchema $schema Internal message schema
     *
     * @return array<int, string>
     */
    protected function filterReceiversByPermissons(array $users, InternalMessageSchema $schema)
    {
        if (empty($schema->action_url)) {
            return $users;
        }

        $url = fn_url($schema->action_url, $schema->area);
        $url_object = new Url($url);
        $url_dispatch = $url_object->getQueryParams();
        list(, $controller, $mode) = fn_get_dispatch_routing($url_dispatch);

        foreach (array_keys($users) as $user_id) {
            $is_available = fn_check_permissions($controller, $mode, 'admin', '', $url_dispatch, $schema->area, $user_id);

            if (!$is_available) {
                /** @psalm-suppress InvalidArrayOffset */
                unset($users[$user_id]);
            }
        }

        return $users;
    }
}
