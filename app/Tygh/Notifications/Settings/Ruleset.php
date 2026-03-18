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

namespace Tygh\Notifications\Settings;

/**
 * Class Ruleset provides means to manually set notification settings for specific transports and receivers.
 *
 * @package Tygh\Notifications\Settings
 */
class Ruleset
{
    /**
     * @var array|bool
     */
    protected $rules;

    /**
     * Settings constructor.
     *
     * @param bool[] $rules
     */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * Overrides event notification settings.
     *
     * @param array $event_settings
     *
     * @return array
     */
    public function apply(array $event_settings)
    {
        foreach ($event_settings as $receiver => &$notifications_group) {
            foreach ($notifications_group as $transport_id => &$is_message_required) {
                $is_message_required = $this->overrideByReceiverAndTransport(
                    $is_message_required,
                    $receiver,
                    $transport_id
                );
                $is_message_required = $this->overrideByReceiver(
                    $is_message_required,
                    $receiver
                );
            }
            unset($is_message_required);
        }
        unset($notifications_group);

        return $event_settings;
    }

    /**
     * Applies transport-specific override rules to a notification setting.
     *
     * @param bool   $is_message_required Initial notification setting value
     * @param string $receiver            Receiver
     * @param string $transport_id        Transport
     *
     * @return bool
     */
    protected function overrideByReceiverAndTransport($is_message_required, $receiver, $transport_id)
    {
        if (isset($this->rules[$receiver][$transport_id]) && $this->rules[$receiver][$transport_id] === false) {
            return $this->rules[$receiver][$transport_id];
        }

        return $is_message_required;
    }

    /**
     * Applies receiver-specific override rules to a notification setting.
     *
     * @param bool   $is_message_required Initial notification setting value
     * @param string $receiver            Receiver
     *
     * @return bool
     */
    protected function overrideByReceiver($is_message_required, $receiver)
    {
        if (isset($this->rules[$receiver]) && $this->rules[$receiver] === false) {
            $is_message_required = $this->rules[$receiver];
        }

        return $is_message_required;
    }
}
