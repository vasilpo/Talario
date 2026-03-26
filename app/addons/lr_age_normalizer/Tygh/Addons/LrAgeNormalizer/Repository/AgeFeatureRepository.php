<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

namespace Tygh\Addons\LrAgeNormalizer\Repository;

class AgeFeatureRepository
{
    /**
     * Creates repository instance.
     *
     * @return self
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * Gets feature meta data.
     *
     * @param int $feature_id Feature identifier
     *
     * @return array<string, mixed>
     */
    public function getFeatureData(int $feature_id): array
    {
        return (array) db_get_row(
            'SELECT feature_id, feature_type FROM ?:product_features WHERE feature_id = ?i',
            $feature_id
        );
    }

    /**
     * Gets all variants of the feature.
     *
     * @param int $feature_id Feature identifier
     *
     * @return array<int, array<string, mixed>>
     */
    public function getFeatureVariants(int $feature_id): array
    {
        return db_get_array(
            'SELECT variant_id, feature_id, position'
            . ' FROM ?:product_feature_variants'
            . ' WHERE feature_id = ?i'
            . ' ORDER BY position, variant_id',
            $feature_id
        );
    }

    /**
     * Gets all localized descriptions for feature variants.
     *
     * @param int $feature_id Feature identifier
     *
     * @return array<int, array<string, mixed>>
     */
    public function getFeatureVariantDescriptions(int $feature_id): array
    {
        return db_get_array(
            'SELECT fv.variant_id, fvd.lang_code, fvd.variant'
            . ' FROM ?:product_feature_variants AS fv'
            . ' INNER JOIN ?:product_feature_variant_descriptions AS fvd ON fvd.variant_id = fv.variant_id'
            . ' WHERE fv.feature_id = ?i'
            . ' ORDER BY fv.position, fv.variant_id, fvd.lang_code',
            $feature_id
        );
    }

    /**
     * Gets product identifiers that contain source feature values.
     *
     * @param int $feature_id Feature identifier
     *
     * @return array<int, int>
     */
    public function getAffectedProductIds(int $feature_id): array
    {
        return db_get_fields(
            'SELECT DISTINCT product_id FROM ?:product_features_values WHERE feature_id = ?i ORDER BY product_id',
            $feature_id
        );
    }

    /**
     * Gets selected variant identifiers for a product feature.
     *
     * @param int $feature_id Feature identifier
     * @param int $product_id Product identifier
     *
     * @return array<int, int>
     */
    public function getSelectedVariantIds(int $feature_id, int $product_id): array
    {
        return db_get_fields(
            'SELECT DISTINCT variant_id FROM ?:product_features_values'
            . ' WHERE feature_id = ?i AND product_id = ?i AND variant_id > 0'
            . ' ORDER BY variant_id',
            $feature_id,
            $product_id
        );
    }
}
