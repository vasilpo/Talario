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

namespace Tygh\Addons\ProductReviews\ProductReview;

use Tygh\Enum\ImagePairTypes;
use Tygh\Enum\YesNo;

class ImagesService
{
    const OBJECT_TYPE = 'product_reviews';

    /** @var int */
    protected $max_images_upload;

    /** @var array<string> */
    protected $allowed_extensions;

    /**
     * ImagesService constructor.
     *
     * @param array<string, string> $allowed_extensions List of allowed image extensions
     * @param int                   $max_images_upload  Maximum number of uploaded images
     *
     * @return void
     */
    public function __construct(array $allowed_extensions, $max_images_upload = 10)
    {
        $this->allowed_extensions = $allowed_extensions;
        $this->max_images_upload = $max_images_upload;
    }

    /**
     * @param int|array<int> $product_review_ids Product Review identifiers
     *
     * @return array<array<string|int|array<string|int>>>|array<array<array<string|int|array<string|int>>>>
     */
    public function getImagePairs($product_review_ids)
    {
        if (!$product_review_ids) {
            return [];
        }

        return fn_get_image_pairs($product_review_ids, self::OBJECT_TYPE, ImagePairTypes::ADDITIONAL, true, true);
    }

    /**
     * @param int $product_review_id Product review identifier
     *
     * @return void
     */
    public function deleteImagePairsByProductReviewId($product_review_id)
    {
        fn_delete_image_pairs($product_review_id, self::OBJECT_TYPE);
    }

    /**
     * @param int $product_review_id Product review identifier
     *
     * @return array<int>
     */
    public function attachImages($product_review_id)
    {
        $allowed_file_size_bytes = fn_get_allowed_image_file_size();
        $filtered = fn_filter_uploaded_data('product_review_data', $this->allowed_extensions, true, true, $allowed_file_size_bytes);
        $filtered = fn_filter_by_image_resolution($filtered);
        $filtered = array_slice($filtered, 0, $this->max_images_upload);

        $pairs_data = [];
        $position = 1;
        foreach (array_keys($filtered) as $key) {
            $pairs_data[$key] = [
                'type'      => ImagePairTypes::ADDITIONAL,
                'object_id' => 0,
                'position'  => $position++,
                'is_new'    => YesNo::YES,
            ];
        }

        return fn_update_image_pairs([], $filtered, $pairs_data, $product_review_id, self::OBJECT_TYPE);
    }

    /**
     * @param int|int[] $pair_ids Image pairs identifiers
     *
     * @return void
     */
    public function deleteImagePairs($pair_ids)
    {
        foreach ((array) $pair_ids as $pair_id) {
            fn_delete_image_pair($pair_id, self::OBJECT_TYPE);
        }
    }
}
