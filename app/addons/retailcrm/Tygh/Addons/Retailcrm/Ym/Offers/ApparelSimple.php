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

namespace Tygh\Addons\Retailcrm\Ym\Offers;

use Tygh\Ym\Offers\ApparelSimple as BaseApparelSimple;

/**
 * Class ApparelSimple
 *
 * @package Tygh\Addons\Retailcrm\Ym\Offers
 */
class ApparelSimple extends BaseApparelSimple
{
    /**
     * @inheritdoc
     */
    protected function getApparelOffer($product)
    {
        $this->schema[] = 'purchasePrice';
        if (!in_array('name', $this->schema)) {
            $this->schema[] = 'name';
        }

        $this->offer['attr'] = array_merge($this->offer['attr'], Simple::getRetailCrmOfferAttributes($product));
        $this->offer['items'] = Simple::getRetailCrmOfferItem($this->offer['items'], $product);
    }

    /**
     * @inheritdoc
     */
    protected function buildOfferCombination($product, $combination)
    {
        $result = parent::buildOfferCombination($product, $combination);

        if ($result) {
            $this->offer['attr'] = array_merge($this->offer['attr'], Simple::getRetailCrmOfferAttributes($product, $combination));
            $this->offer['items']['name'] = self::getProductCombinationName($product, $combination);
        }

        return $result;
    }

    /**
     * Gets product combination name.
     *
     * @param array $product        Product data
     * @param array $combination    Product combination data
     *
     * @return string
     */
    public static function getProductCombinationName($product, $combination)
    {
        $parts = array($product['product']);

        if (!empty($combination['combination'])) {
            foreach ($combination['combination'] as $option_id => $variant_id) {
                if (isset($product['product_options'][$option_id]['variants'][$variant_id])) {
                    $option = $product['product_options'][$option_id];
                    $variant = $option['variants'][$variant_id];

                    $parts[] = $option['option_name'] . ': ' . $variant['variant_name'];
                }
            }
        }

        return implode(', ', $parts);
    }
}