<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

namespace Tygh\Addons\LrAgeNormalizer\Service;

use Tygh\Addons\LrAgeNormalizer\Repository\AgeFeatureRepository;
use Tygh\Enum\ProductFeatures;

class AgeFeatureConverter
{
    /** @var AgeFeatureRepository */
    protected AgeFeatureRepository $repository;

    /** @var AgeFeatureNormalizer */
    protected AgeFeatureNormalizer $normalizer;

    /**
     * @param AgeFeatureRepository|null $repository Repository
     * @param AgeFeatureNormalizer|null    $normalizer Normalizer
     */
    public function __construct(?AgeFeatureRepository $repository = null, ?AgeFeatureNormalizer $normalizer = null)
    {
        $this->repository = $repository ?: AgeFeatureRepository::create();
        $this->normalizer = $normalizer ?: new AgeFeatureNormalizer();
    }

    /**
     * Builds preview data for source-to-target age mapping.
     *
     * @param int $source_feature_id Source feature identifier
     * @param int $target_feature_id Target feature identifier
     *
     * @return array<string, mixed>
     */
    public function preview(int $source_feature_id, int $target_feature_id): array
    {
        $source_feature_data = $this->repository->getFeatureData($source_feature_id);
        $target_feature_data = $this->repository->getFeatureData($target_feature_id);
        $source_variants = $this->repository->getFeatureVariants($source_feature_id);
        $source_descriptions = $this->repository->getFeatureVariantDescriptions($source_feature_id);
        $source_descriptions_by_variant = $this->groupDescriptionsByVariant($source_descriptions);
        $target_variant_map = $this->buildTargetVariantMap($target_feature_id);

        $rows = [];
        $summary = [
            'source_total' => 0,
            'source_mapped' => 0,
            'source_unmapped' => 0,
        ];

        foreach ($source_variants as $variant) {
            $variant_id = (int) $variant['variant_id'];
            $variant_descriptions = $source_descriptions_by_variant[$variant_id] ?? [];
            $resolution = $this->resolveVariantMapping($variant_descriptions);
            $resolution['target_variant_ids'] = [];
            $resolution['missing_target_ages'] = [];

            foreach ($resolution['ages'] as $age) {
                if (isset($target_variant_map[$age])) {
                    $resolution['target_variant_ids'][] = $target_variant_map[$age];
                } else {
                    $resolution['missing_target_ages'][] = $age;
                }
            }

            $summary['source_total']++;
            if ($resolution['status'] === 'unmapped') {
                $summary['source_unmapped']++;
            } else {
                $summary['source_mapped']++;
            }

            $rows[] = [
                'variant_id' => $variant_id,
                'status' => $resolution['status'],
                'ages' => $resolution['ages'],
                'target_variant_ids' => $resolution['target_variant_ids'],
                'missing_target_ages' => $resolution['missing_target_ages'],
                'matched_value' => $resolution['matched_value'],
                'matched_lang_code' => $resolution['matched_lang_code'],
                'descriptions' => $variant_descriptions,
            ];
        }

        return [
            'source_feature_id' => $source_feature_id,
            'target_feature_id' => $target_feature_id,
            'source_feature_type' => $source_feature_data['feature_type'] ?? null,
            'target_feature_type' => $target_feature_data['feature_type'] ?? null,
            'is_supported_source_feature_type' => in_array(
                $source_feature_data['feature_type'] ?? null,
                ProductFeatures::getSelectableList(),
                true
            ),
            'is_supported_target_feature_type'
                => ($target_feature_data['feature_type'] ?? null) === ProductFeatures::MULTIPLE_CHECKBOX,
            'summary' => $summary,
            'target_variant_ids_by_age' => $target_variant_map,
            'rows' => $rows,
        ];
    }

    /**
     * Synchronizes target ages for a single product.
     *
     * @param int $product_id         Product identifier
     * @param int $source_feature_id  Source feature identifier
     * @param int $target_feature_id  Target feature identifier
     *
     * @return array<string, mixed>
     */
    public function syncProduct(int $product_id, int $source_feature_id, int $target_feature_id): array
    {
        $preview = $this->preview($source_feature_id, $target_feature_id);
        return $this->syncProductByPreview($product_id, $target_feature_id, $preview);
    }

    /**
     * Synchronizes target ages for all products that have source values.
     *
     * @param int $source_feature_id Source feature identifier
     * @param int $target_feature_id Target feature identifier
     *
     * @return array<string, mixed>
     */
    public function convert(int $source_feature_id, int $target_feature_id): array
    {
        $preview = $this->preview($source_feature_id, $target_feature_id);
        $product_ids = $this->repository->getAffectedProductIds($source_feature_id);
        $result = [
            'source_feature_id' => $source_feature_id,
            'target_feature_id' => $target_feature_id,
            'summary' => [
                'products_total' => count($product_ids),
                'products_updated' => 0,
                'products_skipped_unmapped' => 0,
                'products_skipped_missing_target_variants' => 0,
                'products_unchanged' => 0,
            ],
            'skipped_products' => [],
            'updated_products' => [],
        ];

        foreach ($product_ids as $product_id) {
            $sync_result = $this->syncProductByPreview($product_id, $target_feature_id, $preview);

            if ($sync_result['status'] === 'updated') {
                $result['summary']['products_updated']++;
                $result['updated_products'][] = $sync_result;
                continue;
            }

            if ($sync_result['status'] === 'skipped_unmapped') {
                $result['summary']['products_skipped_unmapped']++;
                $result['skipped_products'][] = $sync_result;
                continue;
            }

            if ($sync_result['status'] === 'skipped_missing_target_variants') {
                $result['summary']['products_skipped_missing_target_variants']++;
                $result['skipped_products'][] = $sync_result;
                continue;
            }

            $result['summary']['products_unchanged']++;
        }

        return $result;
    }

    /**
     * Synchronizes a single product using prebuilt preview data to avoid
     * recalculating source and target mapping for each product in bulk mode.
     *
     * @param int                 $product_id         Product identifier
     * @param int                 $target_feature_id  Target feature identifier
     * @param array<string, mixed> $preview           Prebuilt preview data
     *
     * @return array<string, mixed>
     */
    protected function syncProductByPreview(int $product_id, int $target_feature_id, array $preview): array
    {
        $rows_by_variant_id = [];

        foreach ($preview['rows'] as $row) {
            $rows_by_variant_id[$row['variant_id']] = $row;
        }

        $selected_variant_ids = array_map(
            'intval',
            $this->repository->getSelectedVariantIds((int) $preview['source_feature_id'], $product_id)
        );
        $result = [
            'status' => 'unchanged',
            'product_id' => $product_id,
            'target_variant_ids' => [],
            'unmapped_values' => [],
            'missing_target_ages' => [],
        ];

        if (empty($selected_variant_ids)) {
            return $result;
        }

        $target_ages = [];

        foreach ($selected_variant_ids as $selected_variant_id) {
            $row = $rows_by_variant_id[$selected_variant_id] ?? null;
            if ($row === null || $row['status'] === 'unmapped') {
                $result['status'] = 'skipped_unmapped';
                $result['unmapped_values'][] = $this->getVariantDisplayValue($row);
                continue;
            }

            if (!empty($row['missing_target_ages'])) {
                $result['status'] = 'skipped_missing_target_variants';
                foreach ($row['missing_target_ages'] as $missing_target_age) {
                    $result['missing_target_ages'][] = $missing_target_age;
                }
                continue;
            }

            foreach ($row['ages'] as $age) {
                $target_ages[] = $age;
            }
        }

        $result['unmapped_values'] = array_values(array_unique(array_filter($result['unmapped_values'])));
        sort($result['missing_target_ages']);
        $result['missing_target_ages'] = array_values(array_unique($result['missing_target_ages']));

        if ($result['status'] !== 'unchanged') {
            return $result;
        }

        sort($target_ages);
        $target_ages = array_values(array_unique($target_ages));
        $target_variant_ids = [];

        foreach ($target_ages as $age) {
            $target_variant_ids[] = (int) $preview['target_variant_ids_by_age'][$age];
        }

        sort($target_variant_ids);
        $result['target_variant_ids'] = $target_variant_ids;

        $current_target_variant_ids = array_map(
            'intval',
            $this->repository->getSelectedVariantIds($target_feature_id, $product_id)
        );
        sort($current_target_variant_ids);

        if ($current_target_variant_ids === $target_variant_ids) {
            return $result;
        }

        fn_update_product_features_value(
            $product_id,
            [$target_feature_id => $target_variant_ids],
            [],
            DESCR_SL
        );

        $result['status'] = 'updated';

        return $result;
    }

    /**
     * Groups localized descriptions by source variant identifier.
     *
     * @param array<int, array<string, mixed>> $descriptions Raw descriptions
     *
     * @return array<int, array<int, array<string, string>>>
     */
    protected function groupDescriptionsByVariant(array $descriptions): array
    {
        $result = [];

        foreach ($descriptions as $description) {
            $variant_id = (int) $description['variant_id'];
            $value = trim((string) $description['variant']);

            if ($value === '') {
                continue;
            }

            $result[$variant_id][] = [
                'lang_code' => (string) $description['lang_code'],
                'value' => $value,
            ];
        }

        return $result;
    }

    /**
     * Resolves mapping for one source variant across all localized labels.
     *
     * @param array<int, array<string, string>> $descriptions Variant descriptions
     *
     * @return array<string, mixed>
     */
    protected function resolveVariantMapping(array $descriptions): array
    {
        foreach ($descriptions as $description) {
            $value = trim((string) $description['value']);
            $normalized = $this->normalizer->normalize([$value]);

            if (empty($normalized['ages']) || !empty($normalized['unparsed_values'])) {
                continue;
            }

            return [
                'status' => 'mapped',
                'ages' => $normalized['ages'],
                'matched_value' => $value,
                'matched_lang_code' => (string) $description['lang_code'],
            ];
        }

        return [
            'status' => 'unmapped',
            'ages' => [],
            'matched_value' => null,
            'matched_lang_code' => null,
        ];
    }

    /**
     * Builds age-to-variant map for the target exact-age feature.
     *
     * @param int $target_feature_id Target feature identifier
     *
     * @return array<int, int>
     */
    protected function buildTargetVariantMap(int $target_feature_id): array
    {
        $target_variants = $this->repository->getFeatureVariants($target_feature_id);
        $target_descriptions = $this->repository->getFeatureVariantDescriptions($target_feature_id);
        $target_descriptions_by_variant = $this->groupDescriptionsByVariant($target_descriptions);
        $result = [];

        foreach ($target_variants as $target_variant) {
            $variant_id = (int) $target_variant['variant_id'];
            $descriptions = $target_descriptions_by_variant[$variant_id] ?? [];

            foreach ($descriptions as $description) {
                $age = $this->normalizer->extractCanonicalAge($description['value']);
                if ($age === null || isset($result[$age])) {
                    continue;
                }

                $result[$age] = $variant_id;
            }
        }

        return $result;
    }

    /**
     * Returns the most suitable display value for diagnostics.
     *
     * @param array<string, mixed>|null $row Preview row
     *
     * @return string
     */
    protected function getVariantDisplayValue(?array $row = null): string
    {
        if ($row === null) {
            return '';
        }

        if (!empty($row['matched_value'])) {
            return (string) $row['matched_value'];
        }

        if (!empty($row['descriptions'][0]['value'])) {
            return (string) $row['descriptions'][0]['value'];
        }

        return '';
    }
}
