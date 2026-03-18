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

$schema['polls'] = array (
    'content' => array (
        'items' => array (
            'remove_indent' => true,
            'hide_label' => true,
            'type' => 'enum',
            'object' => 'polls',
            'items_function' => 'fn_get_polls',
            'fillings' => array (
                'manually' => array (
                    'picker' => 'addons/polls/pickers/polls/picker.tpl',
                    'picker_params' => array (
                        'multiple' => true,
                        'positions' => true,
                    ),
                ),
            ),
        ),
    ),
    'templates' => 'addons/polls/blocks',
    'wrappers' => 'blocks/wrappers',
    'cache' => array (
        'update_handlers' => array ('polls', 'polls_answers', 'polls_votes', 'poll_descriptions', 'poll_items'),
    ),
    'brief_info_function' => 'fn_block_get_block_with_items_info'
);

return $schema;
