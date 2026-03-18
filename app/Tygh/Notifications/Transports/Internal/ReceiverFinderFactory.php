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

use Pimple\Container;
use Tygh\Exceptions\DeveloperException;

/**
 * Class ReceiverFinderFactory provides classes that find receivers for internal notifications.
 *
 * @package Tygh\Notifications\Transports\Internal
 */
class ReceiverFinderFactory
{
    /**
     * @var \Pimple\Container
     */
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $method
     *
     * @return \Tygh\Notifications\Transports\Internal\ReceiverFinders\ReceiverFinderInterface
     *
     * @throws \Tygh\Exceptions\DeveloperException
     */
    public function get($method)
    {
        $finder_id = 'event.transports.internal.receiver_finders.' . $method;
        if (!$this->container->has($finder_id)) {
            throw new DeveloperException('Unknown receiver finder method ' . $method);
        }

        return $this->container[$finder_id];
    }
}
