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

namespace Tygh\Addons\RewardPoints\Documents\Order;


use Tygh\Template\IVariable;
use Tygh\Template\Snippet\Table\ItemContext;
use Tygh\Tools\Formatter;

/**
 * Class RewardPointProductVariable
 * @package Tygh\Addons\RewarPoints\Documents\Order
 */
class RewardPointProductVariable implements IVariable
{
    public $points;
    public $text;

    /**
     * RewardPointProductVariable constructor.
     * 
     * @param ItemContext   $context    Instance of table column context.
     * @param Formatter     $formatter  Instance of 
     */
    public function __construct(ItemContext $context, Formatter $formatter)
    {
        $product = $context->getItem();

        if (!empty($product['extra']['points_info']['price'])) {
            $this->points = $product['extra']['points_info']['price'];
            $this->text = __('price_in_points', array(), $context->getLangCode()) . ': ' . $this->points;
        }
    }
}