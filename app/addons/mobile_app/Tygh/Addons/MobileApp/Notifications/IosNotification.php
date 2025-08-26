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

class IosNotification implements INotification
{
    /**
     * @var string $target_screen
     */
    protected $target_screen;

    /**
     * @var string $priority
     */
    protected $priority = '10';

    /**
     * @var string $title
     */
    protected $title;

    /**
     * @var string $message
     */
    protected $message;

    /**
     * @var array<string, string> $data
     */
    protected $data = [];

    /** @inheritdoc */
    public function getBody()
    {
        return [
            'message' => [
                'notification' => [
                    'title' => $this->getTitle(),
                    'body'  => $this->getMessage(),
                ],
                'data'         => $this->getData(),
                'apns'         => [
                    'headers' => [
                        'apns-priority' => $this->priority,
                    ],
                    'payload' => [
                        'aps' => [
                            'sound'              => 'default',
                            'interruption-level' => 'active',
                        ],
                    ],
                ],
            ],
        ];
    }

    /** @inheritdoc */
    public function setTargetScreen($screen)
    {
        $this->target_screen = $screen;
    }

    /** @inheritdoc */
    public function getTargetScreen()
    {
        return $this->target_screen;
    }

    /** @inheritdoc */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /** @inheritdoc */
    public function getTitle()
    {
        return $this->title;
    }

    /** @inheritdoc */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /** @inheritdoc */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param array<string, string> $data Data
     */
    public function setData(array $data = []): void
    {
        $this->data = $data;
    }

    /**
     * @return array<string, string>
     */
    public function getData()
    {
        $this->data['targetScreen'] = $this->getTargetScreen();

        return $this->data;
    }
}