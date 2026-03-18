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

/**
 * Fetches current product id for blocks with variations_filling
 *
 * @param array $block_data
 *
 * @return int
 */
function fn_product_variations_blocks_get_current_product_id($block_data)
{
    if (
        !isset($block_data['content']['items']['filling'])
        || $block_data['content']['items']['filling'] !== 'product_variations.variations_filling'
    ) {
        return 0;
    }

    return isset($_REQUEST['product_id']) ? (int) $_REQUEST['product_id'] : 0;
}