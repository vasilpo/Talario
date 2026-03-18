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

namespace Tygh\Api\Entities\v40;

use Tygh\Api\Entities\Categories;

class SraCategories extends Categories
{
    protected $icon_size_small = [500, 500];

    protected $icon_size_big = [1000, 1000];

    /** @inheritdoc */
    public function index($id = 0, $params = [])
    {
        $params['get_images'] = $this->safeGet($params, 'get_images', true);

        $result = parent::index($id, $params);

        $params['icon_sizes'] = $this->safeGet($params, 'icon_sizes', [
            'main_pair'   => [$this->icon_size_big, $this->icon_size_small],
            'image_pairs' => [$this->icon_size_small],
        ]);

        $categories = [];
        if ($id && !empty($result['data'])) {
            if ($this->safeGet($params, 'get_subcategories', false)) {
                $result['data']['subcategories'] = fn_storefront_rest_api_set_categories_icons(
                    fn_get_subcategories($id),
                    $params['icon_sizes']
                );

                $result['data']['subcategories'] = array_values($result['data']['subcategories']);
            }

            $categories = [$result['data']['category_id'] => $result['data']];
        } elseif (!empty($result['data']['categories'])) {
            $categories = $result['data']['categories'];
        }

        $categories = fn_storefront_rest_api_set_categories_icons($categories, $params['icon_sizes']);

        if ($id) {
            $result['data'] = reset($categories);
        } else {
            $result['data']['categories'] = $categories;
        }

        return $result;
    }
}
