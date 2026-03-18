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

namespace Tygh\Notifications\Transports;

use Pimple\Container;

/**
 * Class TransportFactory implements a transport factory that loads transports from the application container.
 *
 * @package Tygh\Notifications\Transports
 */
class TransportFactory implements ITransportFactory
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
     * @param string $transport_id
     *
     * @return \Tygh\Notifications\Transports\ITransport
     */
    public function create($transport_id)
    {
        return $this->container['event.transports.' . $transport_id];
    }
}
