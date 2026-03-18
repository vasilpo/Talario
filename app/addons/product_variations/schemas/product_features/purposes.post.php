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

use Tygh\Enum\ProductFeatures;
use Tygh\Enum\ProductFilterStyles;
use Tygh\Enum\ProductFeatureStyles;
use Tygh\Addons\ProductVariations\Product\FeaturePurposes;

/** @var array $schema */

$schema[FeaturePurposes::CREATE_CATALOG_ITEM] = [
    'position'   => 200,
    'styles_map' => [
        'dropdown_checkbox' => [
            'feature_style' => ProductFeatureStyles::DROP_DOWN,
            'filter_style'  => ProductFilterStyles::CHECKBOX,
            'feature_type'  => ProductFeatures::TEXT_SELECTBOX
        ],
        'dropdown_checkbox_images' => [
            'feature_style' => ProductFeatureStyles::DROP_DOWN_IMAGES,
            'filter_style'  => ProductFilterStyles::CHECKBOX,
            'feature_type'  => ProductFeatures::TEXT_SELECTBOX
        ],
        'dropdown_checkbox_labels' => [
            'feature_style' => ProductFeatureStyles::DROP_DOWN_LABELS,
            'filter_style'  => ProductFilterStyles::CHECKBOX,
            'feature_type'  => ProductFeatures::TEXT_SELECTBOX
        ],
        'dropdown_slider' => [
            'feature_style' => ProductFeatureStyles::DROP_DOWN,
            'filter_style'  => ProductFilterStyles::SLIDER,
            'feature_type'  => ProductFeatures::NUMBER_SELECTBOX
        ],
        'dropdown_slider_labels' => [
            'feature_style' => ProductFeatureStyles::DROP_DOWN_LABELS,
            'filter_style'  => ProductFilterStyles::SLIDER,
            'feature_type'  => ProductFeatures::NUMBER_SELECTBOX
        ],
        'dropdown_color' => [
            'feature_style' => ProductFeatureStyles::DROP_DOWN,
            'filter_style'  => ProductFilterStyles::COLOR,
            'feature_type'  => ProductFeatures::TEXT_SELECTBOX,
        ],
        'dropdown_color_images' => [
            'feature_style' => ProductFeatureStyles::DROP_DOWN_IMAGES,
            'filter_style'  => ProductFilterStyles::COLOR,
            'feature_type'  => ProductFeatures::TEXT_SELECTBOX,
        ],
        'dropdown_color_labels' => [
            'feature_style' => ProductFeatureStyles::DROP_DOWN_LABELS,
            'filter_style'  => ProductFilterStyles::COLOR,
            'feature_type'  => ProductFeatures::TEXT_SELECTBOX,
        ],
    ],
];

$schema[FeaturePurposes::CREATE_VARIATION_OF_CATALOG_ITEM] = [
    'position'   => 300,
    'styles_map' => [
        'dropdown_checkbox' => [
            'feature_style' => ProductFeatureStyles::DROP_DOWN,
            'filter_style'  => ProductFilterStyles::CHECKBOX,
            'feature_type'  => ProductFeatures::TEXT_SELECTBOX
        ],
        'dropdown_checkbox_images' => [
            'feature_style' => ProductFeatureStyles::DROP_DOWN_IMAGES,
            'filter_style'  => ProductFilterStyles::CHECKBOX,
            'feature_type'  => ProductFeatures::TEXT_SELECTBOX
        ],
        'dropdown_checkbox_labels' => [
            'feature_style' => ProductFeatureStyles::DROP_DOWN_LABELS,
            'filter_style'  => ProductFilterStyles::CHECKBOX,
            'feature_type'  => ProductFeatures::TEXT_SELECTBOX
        ],
        'dropdown_slider' => [
            'feature_style' => ProductFeatureStyles::DROP_DOWN,
            'filter_style'  => ProductFilterStyles::SLIDER,
            'feature_type'  => ProductFeatures::NUMBER_SELECTBOX
        ],
        'dropdown_slider_labels' => [
            'feature_style' => ProductFeatureStyles::DROP_DOWN_LABELS,
            'filter_style'  => ProductFilterStyles::SLIDER,
            'feature_type'  => ProductFeatures::NUMBER_SELECTBOX
        ],
        'dropdown_color' => [
            'feature_style' => ProductFeatureStyles::DROP_DOWN,
            'filter_style'  => ProductFilterStyles::COLOR,
            'feature_type'  => ProductFeatures::TEXT_SELECTBOX,
        ],
        'dropdown_color_images' => [
            'feature_style' => ProductFeatureStyles::DROP_DOWN_IMAGES,
            'filter_style'  => ProductFilterStyles::COLOR,
            'feature_type'  => ProductFeatures::TEXT_SELECTBOX,
        ],
        'dropdown_color_labels' => [
            'feature_style' => ProductFeatureStyles::DROP_DOWN_LABELS,
            'filter_style'  => ProductFilterStyles::COLOR,
            'feature_type'  => ProductFeatures::TEXT_SELECTBOX,
        ],
    ],
];

return $schema;
