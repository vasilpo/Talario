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
 * Updates content settings of the block banners with filled by manually.
 *
 * @param array $block              Block data
 * @param int   $from_company_id    Base company identifier
 * @param int   $to_company_id      Target company identifier
 * @param array $cloning_results    List of cloned identifiers by object type and base identifier of object (categories => [from_id => to_id], etc)
 *
 * @return array|false  Returns block data if need update, otherwise false
 */
function fn_ult_clone_layout_block_banners_filling_by_manually($block, $from_company_id, $to_company_id, $cloning_results)
{
    if (!empty($block['content']['items']['item_ids'])) {
        $item_ids = explode(',', $block['content']['items']['item_ids']);

        foreach ($item_ids as $key => $item_id) {
            if (!fn_ult_is_shared_object('banners', $item_id, $to_company_id)) {
                unset($item_ids[$key]);
            }
        }

        $block['content']['items']['item_ids'] = implode(',', $item_ids);

        return $block;
    }

    return false;
}


