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

namespace Tygh\Shippings;

/**
 * Shipping pickup services interface
 */
interface IPickupService
{
    /**
     * Gets minimal cost among available pickup points
     *
     * @return float|false
     */
    public function getPickupMinCost();

    /**
     * This method is for later use. For now it's better to return an empty array
     * because the structure of pickup points list is not unified yet.
     *
     * @return array
     */
    public function getPickupPoints();

    /**
     * Gets pickup points quantity
     *
     * @return int|false
     */
    public function getPickupPointsQuantity();
}
