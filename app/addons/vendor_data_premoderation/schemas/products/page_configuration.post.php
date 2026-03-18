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

use Tygh\Addons\VendorDataPremoderation\State;
use Tygh\Addons\VendorDataPremoderation\StateFactory;
use Tygh\SmartyEngine\Core;

defined('BOOTSTRAP') or die('Access denied');

/** @var array $schema */
if (isset($schema['detailed']['sections']['information']['fields']['product'])) {
    $schema['detailed']['sections']['information']['fields']['product']['source'] = [
        'table'      => 'product_descriptions',
        'field'      => 'product',
        'conditions' => [
            'product_id' => StateFactory::OBJECT_ID_PLACEHOLDER,
            'lang_code'  => DESCR_SL,
        ],
    ];
}

if (isset($schema['detailed']['sections']['information']['fields']['category_ids'])) {
    $schema['detailed']['sections']['information']['fields']['category_ids']['source'] = [
        'table'      => 'products_categories',
        'conditions' => [
            'product_id' => StateFactory::OBJECT_ID_PLACEHOLDER,
        ],
        'processing' => static function ($product_id, $field_config, State $initial_state, State $current_state, Core $core) {
            $category_ids = array_column($initial_state->getSourceData($field_config['source']['table']), 'category_id');
            $categories_data = fn_get_categories_list_with_parents($category_ids, DESCR_SL);

            $category_paths = array_map(static function ($category) {
                $parents_path = [$category['category']];
                foreach ($category['parents'] as $parent) {
                    $parents_path[] = $parent['category'];
                }

                return implode(' / ', $parents_path);
            }, $categories_data);

            $core->assign('category_paths', $category_paths);

            /** @var \Tygh\SmartyEngine\Core $smarty */
            $smarty = $core->getSmarty();

            return $smarty->fetch('addons/vendor_data_premoderation/components/product_page/fields/category_ids.tpl', null, null, $core);
        },
    ];
}

if (isset($schema['detailed']['sections']['information']['fields']['price'])) {
    $schema['detailed']['sections']['information']['fields']['price']['source'] = [
        'table'      => 'product_prices',
        'field'      => 'price',
        'conditions' => [
            'product_id' => StateFactory::OBJECT_ID_PLACEHOLDER,
        ],
    ];
}

if (isset($schema['detailed']['sections']['information']['fields']['full_description'])) {
    $schema['detailed']['sections']['information']['fields']['full_description']['source'] = [
        'table'      => 'product_descriptions',
        'field'      => 'full_description',
        'conditions' => [
            'product_id' => StateFactory::OBJECT_ID_PLACEHOLDER,
            'lang_code' => DESCR_SL,
        ],
    ];
}

if (isset($schema['detailed']['sections']['information']['fields']['images'])) {
    $schema['detailed']['sections']['information']['fields']['images']['source'] = [
        'table'      => 'images_links',
        'conditions' => [
            'product_id' => StateFactory::OBJECT_ID_PLACEHOLDER,
        ],
        'processing' => static function ($product_id, $field_config, State $initial_state, State $current_state, Core $core) {
            $initial_pair_ids = array_column($initial_state->getSourceData($field_config['source']['table']), 'pair_id');
            $current_pair_ids = array_column($current_state->getSourceData($field_config['source']['table']), 'pair_id');

            $deleted_ids = array_diff($initial_pair_ids, $current_pair_ids);
            $added_ids = array_diff($current_pair_ids, $initial_pair_ids);

            $core->assign([
                'deleted_ids' => $deleted_ids,
                'added_ids'   => $added_ids,
            ]);

            /** @var \Tygh\SmartyEngine\Core $smarty */
            $smarty = $core->getSmarty();

            return $smarty->fetch('addons/vendor_data_premoderation/components/product_page/fields/images.tpl', null, null, $core);
        },
    ];
}

if ($schema['detailed']['sections']['information']['fields']['videos']) {
    $schema['detailed']['sections']['information']['fields']['videos']['source'] = [
        'table'      => 'videos_links',
        'conditions' => [
            'product_id' => StateFactory::OBJECT_ID_PLACEHOLDER,
        ],
        'processing' => static function ($product_id, $field_config, State $initial_state, State $current_state, Core $core) {
            $initial_pair_ids = array_column($initial_state->getSourceData($field_config['source']['table']), 'video_id');
            $current_pair_ids = array_column($current_state->getSourceData($field_config['source']['table']), 'video_id');

            $deleted_ids = array_diff($initial_pair_ids, $current_pair_ids);
            $added_ids = array_diff($current_pair_ids, $initial_pair_ids);

            $core->assign([
                'deleted_ids' => $deleted_ids,
                'added_ids'   => $added_ids,
            ]);

            /** @var \Tygh\SmartyEngine\Core $smarty */
            $smarty = $core->getSmarty();

            return $smarty->fetch('addons/vendor_data_premoderation/components/product_page/fields/videos.tpl', null, null, $core);
        },
    ];
}

if (isset($schema['detailed']['sections']['pricing_inventory']['fields']['product_code'])) {
    $schema['detailed']['sections']['pricing_inventory']['fields']['product_code']['source'] = [
        'table'      => 'products',
        'field'      => 'product_code',
        'conditions' => [
            'product_id' => StateFactory::OBJECT_ID_PLACEHOLDER,
        ],
    ];
}

if (isset($schema['detailed']['sections']['pricing_inventory']['fields']['list_price'])) {
    $schema['detailed']['sections']['pricing_inventory']['fields']['list_price']['source'] = [
        'table'      => 'products',
        'field'      => 'list_price',
        'conditions' => [
            'product_id' => StateFactory::OBJECT_ID_PLACEHOLDER,
        ],
    ];
}

if (isset($schema['detailed']['sections']['pricing_inventory']['fields']['amount'])) {
    $schema['detailed']['sections']['pricing_inventory']['fields']['amount']['source'] = [
        'table'      => 'products',
        'field'      => 'amount',
        'conditions' => [
            'product_id' => StateFactory::OBJECT_ID_PLACEHOLDER,
        ],
    ];
}

if (isset($schema['detailed']['sections']['extra']['fields']['short_description'])) {
    $schema['detailed']['sections']['extra']['fields']['short_description']['source'] = [
        'table'      => 'product_descriptions',
        'field'      => 'short_description',
        'conditions' => [
            'product_id' => StateFactory::OBJECT_ID_PLACEHOLDER,
            'lang_code'  => DESCR_SL,
        ],
    ];
}

if (isset($schema['detailed']['sections']['extra']['fields']['search_words'])) {
    $schema['detailed']['sections']['extra']['fields']['search_words']['source'] = [
        'table'      => 'product_descriptions',
        'field'      => 'search_words',
        'conditions' => [
            'product_id' => StateFactory::OBJECT_ID_PLACEHOLDER,
            'lang_code'  => DESCR_SL,
        ],
    ];
}

if (isset($schema['detailed']['sections']['extra']['fields']['promo_text'])) {
    $schema['detailed']['sections']['extra']['fields']['promo_text']['source'] = [
        'table'      => 'product_descriptions',
        'field'      => 'promo_text',
        'conditions' => [
            'product_id' => StateFactory::OBJECT_ID_PLACEHOLDER,
            'lang_code'  => DESCR_SL,
        ],
    ];
}

if (isset($schema['attachments']['sections']['main']['fields']['attachments'])) {
    $schema['attachments']['sections']['main']['fields']['attachments']['source'] = [
        'table'      => 'attachments',
        'field'      => 'attachment_id',
        'conditions' => [
            'object_id'   => StateFactory::OBJECT_ID_PLACEHOLDER,
            'object_type' => 'product',
        ],
        'processing' => static function ($product_id, $field_config, State $initial_state, State $current_state, Core $core) {
            $initial_attachment_ids = array_column($initial_state->getSourceData($field_config['source']['table']), 'attachment_id');
            $current_attachment_ids = array_column($current_state->getSourceData($field_config['source']['table']), 'attachment_id');

            $deleted_ids = array_diff($initial_attachment_ids, $current_attachment_ids);
            $added_ids = array_diff($current_attachment_ids, $initial_attachment_ids);

            /** @var \Tygh\SmartyEngine\Core $smarty */
            $smarty = $core->getSmarty();
            $smarty->assign([
                'deleted_ids' => $deleted_ids,
                'added_ids'   => $added_ids,
            ]);

            return $smarty->fetch('addons/vendor_data_premoderation/components/product_page/fields/attachments.tpl', null, null, $core);
        },
    ];
}

if (isset($schema['seo']['sections']['main']['fields']['seo_name_field'])) {
    $schema['seo']['sections']['main']['fields']['seo_name_field']['source'] = [
        'table'      => 'seo_names',
        'field'      => 'name',
        'conditions' => [
            'object_id' => StateFactory::OBJECT_ID_PLACEHOLDER,
            'type'      => 'p',
            'lang_code' => DESCR_SL,
        ],
    ];
}

return $schema;
