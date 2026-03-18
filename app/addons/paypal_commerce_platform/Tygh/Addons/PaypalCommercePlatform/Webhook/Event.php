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

namespace Tygh\Addons\PaypalCommercePlatform\Webhook;

use stdClass;

#[\AllowDynamicProperties]
abstract class Event
{
    /** @var string $id */
    protected $id;

    /** @var string $summary */
    protected $summary;

    /** @var \stdClass $resource */
    protected $resource;

    /**
     * Event constructor.
     *
     * @param \stdClass $payload Payload data
     */
    public function __construct(stdClass $payload)
    {
        foreach ($payload as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * Provides webhook event ID.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Provides webhook event summary.
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Provides webhook event associated resource.
     *
     * @return \stdClass
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Whether an event has been processed.
     *
     * @return bool
     */
    public function isProcessed()
    {
        return false;
    }
}