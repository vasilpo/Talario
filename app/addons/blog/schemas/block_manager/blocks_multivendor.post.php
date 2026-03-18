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

use Tygh\Enum\ObjectStatuses;

defined('BOOTSTRAP') or die('Access denied');

/**
 * @var array<string, array> $schema
 */
$schema['blog']['is_managed_by'] = ['ROOT'];

$schema['vendor_blog'] = [
    'content' => [
        'items' => [
            'type'           => 'enum',
            'object'         => 'pages',
            'items_function' => 'fn_get_pages',
            'remove_indent'  => true,
            'hide_label'     => true,
            'fillings'       => [
                'blog.recent_posts_scroller' => [
                    'params' => [
                        'simple'     => true,
                        'sort_by'    => 'timestamp',
                        'sort_order' => 'desc',
                        'status'     => ObjectStatuses::ACTIVE,
                        'page_type'  => PAGE_TYPE_BLOG,
                        'get_image'  => true,
                        'request'    => [
                            'company_id' => '%COMPANY_ID%',
                        ],
                    ],
                ],
                'blog.recent_posts' => [
                    'params' => [
                        'simple'     => true,
                        'sort_by'    => 'timestamp',
                        'sort_order' => 'desc',
                        'status'     => ObjectStatuses::ACTIVE,
                        'page_type'  => PAGE_TYPE_BLOG,
                        'request'    => [
                            'company_id' => '%COMPANY_ID%',
                        ],
                    ],
                ],
                'blog.text_links' => [
                    'params' => [
                        'simple'     => true,
                        'sort_by'    => 'timestamp',
                        'sort_order' => 'desc',
                        'status'     => ObjectStatuses::ACTIVE,
                        'page_type'  => PAGE_TYPE_BLOG,
                        'request'    => [
                            'company_id' => '%COMPANY_ID%',
                        ],
                    ],
                    'settings' => [
                        'parent_page_id' => [
                            'type'          => 'picker',
                            'default_value' => '0',
                            'picker'        => 'pickers/pages/picker.tpl',
                            'picker_params' => [
                                'multiple'     => false,
                                'use_keys'     => 'N',
                                'default_name' => __('root_level'),
                                'extra_url'    => '&page_type=' . PAGE_TYPE_BLOG
                            ],
                        ],
                        'limit' => [
                            'type' => 'input',
                            'default_value' => 10
                        ],
                    ],
                ],
            ],
        ],
    ],
    'templates' => 'addons/blog/blocks',
    'wrappers'  => 'blocks/wrappers',
    'cache'     => [
        'update_handlers'  => ['pages', 'page_descriptions'],
        'request_handlers' => ['%PAGE_ID%', '%COMPANY_ID%']
    ],
    'brief_info_function' => 'fn_block_get_blog_info',
    'is_managed_by' => ['ROOT', 'VENDOR']
];

return $schema;
