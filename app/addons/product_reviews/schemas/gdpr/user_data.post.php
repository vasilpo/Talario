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

defined('BOOTSTRAP') or die('Access denied');

use Tygh\Addons\ProductReviews\ServiceProvider as ProductReviewsProvider;

/** @var array $schema */
$schema['product_reviews'] = [
    'collect_data_callback' => static function ($params) {
        $product_reviews = [];

        if (isset($params['user_id'])) {
            $product_reviews_repository = ProductReviewsProvider::getProductReviewRepository();
            list($product_reviews,) = $product_reviews_repository->find(['user_id' => (int) $params['user_id']]);
        }

        return $product_reviews;
    },
    'update_data_callback' => static function ($product_reviews) {
        if (!is_array($product_reviews)) {
            return;
        }

        foreach ($product_reviews as $review) {
            // phpcs:ignore
            if (!empty($review['product_review_id'])) {
                db_replace_into(
                    'product_reviews',
                    [
                        'product_review_id' => $review['product_review_id'],
                        'name' => $review['user_data']['name'],
                        'ip_address' => $review['user_data']['ip_address'],
                        'country_code' => $review['user_data']['country_code'],
                        'city' => $review['user_data']['city']
                    ]
                );
            }
        }
    },
    'params'        => [
        'fields_list' => [
            'name',
            'ip_address',
            'country_code',
            'city'
        ],
    ],
];

return $schema;
