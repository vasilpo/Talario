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

use Tygh\BlockManager\Block;

function fn_le_update_block($field, $value, $id, $lang_code, $object_data = array()) {
    $block = Block::instance()->getById($id, 0, $object_data);
    $data = array(
        'block_id' => $id,
        'type' => $block['type'],
    );
    $description = array();
    if ($field == 'content') {
        $data['content_data'] = array(
            'lang_code' => $lang_code,
            'content' => array(
                'content' => $value
            ),
        );
    } elseif ($field == 'name') {
        $description = array(
            'lang_code' => $lang_code,
            'name' => $value,
        );
        $data['description'] = $description;
    } else {
        return;
    }

    if (!empty($block['object_id'])) {
        $data['content_data']['object_id'] = $block['object_id'];
    }

    if (!empty($block['object_type'])) {
        $data['content_data']['object_type'] = $block['object_type'];
    }

    Block::instance()->update($data, $description);
}
