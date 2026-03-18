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

namespace Tygh\Notifications\EventIdProviders;

/**
 * Class OrderProvider provides means to distinguish order-based notification event.
 *
 * @package Tygh\Notifications\EventIdProviders
 */
class OrderProvider implements IProvider
{
    /**
     * @var string
     */
    protected $prefix = 'order.';

    /**
     * @var string
     */
    protected $edp_suffix = '.edp';

    /**
     * @var string
     */
    protected $id;

    public function __construct(array $order, $edp_data = null)
    {
        $this->id = $this->prefix . $order['order_id'] . $order['status'];
        
        if ($edp_data) {
            $this->id .= $this->edp_suffix;
        }
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }
}
