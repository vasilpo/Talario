<?php

declare(strict_types=1);

namespace Tygh\Tests\Unit\Addons\TalarioAnalytics;

use PHPUnit\Framework\TestCase;

final class PartnerSyncReadApiContractTest extends TestCase
{
    private string $controller;
    private string $addon_xml;
    private string $trusted_controllers;
    private string $write_capability;
    private string $dev_dispatcher;
    private string $dev_ops_workflow;
    private string $cli_runner;
    private string $feature_discovery_runner;
    private string $deploy_workflow;
    private string $preview_post_controller;

    protected function setUp(): void
    {
        $controller_path = dirname(__DIR__) . '/controllers/frontend/talario_analytics.php';
        $addon_path = dirname(__DIR__) . '/addon.xml';
        $trusted_controllers_path = dirname(__DIR__) . '/schemas/permissions/trusted_controllers.post.php';
        $this->controller = (string) file_get_contents($controller_path);
        $this->addon_xml = (string) file_get_contents($addon_path);
        $this->trusted_controllers = (string) file_get_contents($trusted_controllers_path);
        $this->write_capability = (string) file_get_contents(dirname(__DIR__) . '/partner_sync_write.php');
        $this->dev_dispatcher = (string) file_get_contents(dirname(__DIR__, 4) . '/ops/beget/talario-dev-github-dispatcher.sh');
        $this->dev_ops_workflow = (string) file_get_contents(dirname(__DIR__, 4) . '/.github/workflows/dev-copy-ops.yml');
        $this->cli_runner = (string) file_get_contents(dirname(__DIR__, 4) . '/ops/partner-sync-apply.php');
        $this->feature_discovery_runner = (string) file_get_contents(
            dirname(__DIR__, 4) . '/ops/partner-sync-feature-discovery.php'
        );
        $this->deploy_workflow = (string) file_get_contents(
            dirname(__DIR__, 4) . '/.github/workflows/deploy-development.yml'
        );
        $this->preview_post_controller = (string) file_get_contents(
            dirname(__DIR__) . '/controllers/frontend/products.post.php'
        );
    }

    public function testPartnerSyncUsesDedicatedServerConfigToken(): void
    {
        self::assertStringContainsString('TALARIO_PARTNER_SYNC_TOKEN_HASH', $this->controller);
        self::assertStringContainsString('partner_sync_api_not_configured', $this->controller);
        self::assertStringContainsString('partner_sync_api_misconfigured', $this->controller);
        self::assertStringContainsString('fn_talario_analytics_canonical_token_hash', $this->controller);
        self::assertStringContainsString("(string) Registry::get('addons.talario_analytics.api_token'),", $this->controller);
        self::assertStringContainsString('hash_equals($analytics_token_hash, $stored_token_hash)', $this->controller);
        self::assertStringNotContainsString('<item id="partner_sync_token">', $this->addon_xml);
    }

    public function testOrdersContinueUsingAnalyticsTokenSetting(): void
    {
        self::assertStringContainsString("Registry::get('addons.talario_analytics.api_token')", $this->controller);
        self::assertStringContainsString('analytics_api_not_configured', $this->controller);
    }

    public function testCatalogReadsBasePricesFromProductPricesTable(): void
    {
        self::assertStringContainsString('LEFT JOIN ?:product_prices pp ON pp.product_id = p.product_id', $this->controller);
        self::assertStringContainsString('pp.lower_limit = 1 AND pp.usergroup_id = 0', $this->controller);
        self::assertStringContainsString('LEFT JOIN ?:product_prices vpp ON vpp.product_id = p.product_id', $this->controller);
        self::assertStringContainsString('vpp.lower_limit = 1 AND vpp.usergroup_id = 0', $this->controller);
        self::assertStringNotContainsString(' p.price, p.status', $this->controller);
    }

    public function testCatalogExposesActiveCategoryTaxonomyAndProductAssignments(): void
    {
        self::assertStringContainsString('?:category_descriptions', $this->controller);
        self::assertStringContainsString("'category_id' => (int) \$row['category_id']", $this->controller);
        self::assertStringContainsString("'parent_id' => (int) \$row['parent_id']", $this->controller);
        self::assertStringContainsString('?:products_categories', $this->controller);
        self::assertStringContainsString("'category_ids' => []", $this->controller);
        self::assertStringContainsString("\$products[\$product_id]['category_ids'][] = (int) \$row['category_id'];", $this->controller);
        self::assertStringContainsString("'categories' => \$categories", $this->controller);
    }

    public function testCatalogExposesVariationFeatureTaxonomyWithoutInternalIds(): void
    {
        self::assertStringContainsString('?:product_features_descriptions', $this->controller);
        self::assertStringContainsString('?:product_feature_variants', $this->controller);
        self::assertStringContainsString('?:product_feature_variant_descriptions', $this->controller);
        self::assertStringContainsString("['group_catalog_item', 'group_variation_catalog_item']", $this->controller);
        self::assertStringContainsString("'variation_features' => \$variation_features", $this->controller);
        self::assertStringContainsString("'name' => (string) \$feature['description']", $this->controller);
        self::assertStringContainsString("'variants' => array_values(array_unique(\$variants))", $this->controller);
        self::assertStringNotContainsString("'feature_id' => (int) \$feature['feature_id']", $this->controller);
        self::assertStringNotContainsString("'variant_id' =>", $this->controller);
    }

    public function testDevCopyCatalogExposesFilterFeatureMappingWithoutChangingVariationTaxonomy(): void
    {
        self::assertStringContainsString("'dev_copy_filter_features'", $this->controller);
        self::assertStringContainsString("'dev_copy_filter_feature_samples'", $this->controller);
        self::assertStringContainsString('TALARIO_PARTNER_SYNC_DEV_COPY', $this->controller);
        self::assertStringContainsString('?:product_filters', $this->controller);
        self::assertStringContainsString('?:product_filter_descriptions', $this->controller);
        self::assertStringContainsString("'filter_feature_id' =>", $this->controller);
        self::assertStringContainsString("'filter_variant_id' =>", $this->controller);
        self::assertStringContainsString("'feature_type' =>", $this->controller);
        self::assertStringContainsString("'purpose' =>", $this->controller);
        self::assertStringContainsString("['%Возраст%', '%Катег%']", $this->controller);
        self::assertStringContainsString('?:product_features_values', $this->controller);
        self::assertStringContainsString("'category_ids' => array_map('intval'", $this->controller);

        $dev_gate = strpos($this->controller, '$include_dev_copy_filter_features = $is_development');
        $filter_query = strpos($this->controller, "' FROM ?:product_filters fl'");
        $response_gate = strpos($this->controller, 'if ($include_dev_copy_filter_features) {', $filter_query);
        self::assertNotFalse($dev_gate);
        self::assertNotFalse($filter_query);
        self::assertNotFalse($response_gate);
        self::assertLessThan($filter_query, $dev_gate);

        self::assertStringContainsString("'variation_features' => $variation_features", $this->controller);
        self::assertStringNotContainsString("'feature_id' => (int) \$feature['feature_id']", $this->controller);
        self::assertStringNotContainsString("'variant_id' =>", $this->controller);
    }

    public function testLegacyScheduleSupportsEcarterTimestampDates(): void
    {
        self::assertStringContainsString("is_numeric(\$raw_from)", $this->controller);
        self::assertStringContainsString("setTimestamp((int) \$raw_from)", $this->controller);
        self::assertStringContainsString("is_numeric(\$raw_to)", $this->controller);
        self::assertStringContainsString("setTimestamp((int) \$raw_to)", $this->controller);
    }

    public function testPartnerScopeConstrainsProductsVariationsResourcesAndSchedule(): void
    {
        self::assertStringContainsString("p.company_id = ?i", $this->controller);
        self::assertStringContainsString("p.product_id > ?i", $this->controller);
        self::assertStringContainsString("rp.product_id IN (?n)", $this->controller);
        self::assertStringContainsString("rp_scope.product_id IN (?n)", $this->controller);
        self::assertStringNotContainsString("l.address AS location_address", $this->controller);
    }

    public function testResourceScheduleIsPreferredAndLegacyIsFallback(): void
    {
        self::assertStringContainsString("talario_resource_occurrences", $this->controller);
        self::assertStringContainsString("NOT EXISTS (SELECT 1 FROM ?:talario_resource_products rp", $this->controller);
        self::assertStringContainsString("'source' => 'legacy_ecarter'", $this->controller);
        self::assertStringContainsString('array_merge($schedule, $legacy_schedule)', $this->controller);
    }

    public function testDevAgeVariantBootstrapIsExactAndDevOnly(): void
    {
        self::assertStringContainsString("'catalog_variant_bootstrap'", $this->controller);
        self::assertStringContainsString("\$_SERVER['REQUEST_METHOD'] !== 'POST'", $this->controller);
        self::assertStringContainsString('TALARIO_PARTNER_SYNC_DEV_COPY', $this->controller);
        self::assertStringContainsString('TALARIO_PARTNER_SYNC_DEV_WRITE', $this->controller);
        self::assertStringContainsString("'Возраст'", $this->controller);
        self::assertStringContainsString("'2-8 лет'", $this->controller);
        self::assertStringContainsString('fn_update_product_feature_variant(', $this->controller);
        self::assertStringContainsString("'catalog_variant_bootstrap' => true", $this->trusted_controllers);
    }

    public function testDispatcherStatusIsReadOnlyDevOnlyAndSanitized(): void
    {
        self::assertStringContainsString("'dispatcher_status'", $this->controller);
        self::assertStringContainsString("'dispatcher_status' => true", $this->trusted_controllers);
        self::assertStringContainsString('TALARIO_PARTNER_SYNC_DEV_COPY', $this->controller);
        self::assertStringContainsString("'schema_version' => 'partner-sync.dispatcher-status.v1'", $this->controller);
        self::assertStringContainsString("'target_matches_source'", $this->controller);
        self::assertStringContainsString("'target_has_dry_run'", $this->controller);
        self::assertStringContainsString("'target_has_enable_penaty'", $this->controller);
        self::assertStringContainsString("'authorized_v2_expected_command'", $this->controller);
        self::assertStringNotContainsString("'authorized_keys' =>", $this->controller);
        self::assertStringNotContainsString("'target_path' =>", $this->controller);
        self::assertStringNotContainsString("'source_path' =>", $this->controller);
    }

    public function testSignedPenatyPilotIsDevOnlyPartnerAuthenticatedAndCompanyScoped(): void
    {
        self::assertStringContainsString("'penaty_bootstrap'", $this->controller);
        self::assertStringContainsString("'partner_apply'", $this->controller);
        self::assertStringContainsString("'penaty_preview'", $this->controller);
        self::assertStringContainsString("'penaty_bootstrap' => true", $this->trusted_controllers);
        self::assertStringContainsString("'partner_apply' => true", $this->trusted_controllers);
        self::assertStringContainsString("'penaty_preview' => true", $this->trusted_controllers);
        self::assertStringContainsString("in_array(\$mode, ['catalog_variant_bootstrap', 'penaty_bootstrap', 'partner_apply', 'penaty_preview', 'penaty_preview_state'], true)", $this->controller);
        self::assertStringContainsString("['catalog', 'catalog_variant_bootstrap', 'dispatcher_status', 'penaty_bootstrap', 'partner_apply', 'penaty_preview']", $this->controller);
        self::assertStringContainsString("'part-sync-penaty-' . \$purpose . '-20260924'", $this->controller);
        self::assertStringContainsString("'HTTP_X_TALARIO_SIGNATURE'", $this->controller);
        self::assertStringContainsString("'github-actions-talario'", $this->controller);
        self::assertStringContainsString("'talario-part-sync'", $this->controller);
        self::assertStringContainsString("hash('sha256', \$raw_body)", $this->controller);
        self::assertStringContainsString("'approved_company_context_required'", $this->controller);
        self::assertStringContainsString("define('TALARIO_PARTNER_SYNC_DEV_WRITE', true)", $this->controller);
        self::assertStringContainsString("define('TALARIO_PARTNER_SYNC_DEV_WRITE_COMPANY_IDS', (string) ((int) \$approved_company_id))", $this->controller);
        self::assertStringContainsString('fn_talario_analytics_partner_sync_run_penaty_cli($raw, $approved_company_id)', $this->controller);
        self::assertStringContainsString("'/ops/partner-sync-apply.php'", $this->controller);
        self::assertStringContainsString("'TALARIO_PARTNER_SYNC_ROOT' => DIR_ROOT", $this->controller);
        self::assertStringContainsString("'TALARIO_PARTNER_SYNC_SIGNED_RAW_BODY'", $this->write_capability);
        self::assertStringContainsString("'TALARIO_PARTNER_SYNC_SIGNED_PENATY_WRITE'", $this->write_capability);
        self::assertStringContainsString("TALARIO_PARTNER_SYNC_DEV_WRITE_COMPANY_IDS", $this->write_capability);
        self::assertStringContainsString("function_exists('fn_talario_analytics_parse_date')", $this->write_capability);
        self::assertStringContainsString("DateTimeImmutable::createFromFormat('!Y-m-d'", $this->write_capability);
        self::assertStringContainsString('register_shutdown_function($cleanup);', $this->controller);
        self::assertStringNotContainsString('shell_exec(', $this->controller);
        self::assertStringNotContainsString('system(', $this->controller);
        self::assertStringNotContainsString('eval(', $this->controller);
    }

    public function testSignedPenatyPreviewIsSingleTargetDevOnlyAndNeverImpersonatesVendorAdmin(): void
    {
        self::assertStringContainsString('fn_talario_analytics_partner_sync_penaty_preview', $this->controller);
        self::assertStringContainsString("fn_talario_analytics_partner_sync_verify_penaty_signature('preview', \$raw)", $this->controller);
        self::assertStringContainsString("(int) \$payload['product_id'] !== 1158", $this->controller);
        self::assertStringContainsString("(int) \$product['company_id'] !== 39", $this->controller);
        self::assertStringContainsString("(string) \$product['status'] !== 'H'", $this->controller);
        self::assertStringNotContainsString('fn_get_company_root_admin_user_id', $this->controller);
        self::assertStringNotContainsString('fn_fill_auth(', $this->controller);
        self::assertStringNotContainsString('fn_init_user_session_data(', $this->controller);
        self::assertStringContainsString("(int) (\$probe_auth['user_id'] ?? 0) !== 0", $this->controller);
        self::assertStringContainsString("(int) (\$probe_auth['company_id'] ?? 0) !== 0", $this->controller);
        self::assertStringContainsString("(string) (\$probe_auth['user_type'] ?? '') !== 'C'", $this->controller);
        self::assertStringContainsString("(string) (\$probe_auth['area'] ?? '') !== 'C'", $this->controller);
        self::assertStringContainsString("(string) (\$probe_auth['is_root'] ?? '') !== 'N'", $this->controller);
        self::assertStringContainsString("!is_array(\$probe_auth['usergroup_ids'])", $this->controller);
        self::assertStringContainsString("'pilot_preview_guest_session_invalid'", $this->controller);
        self::assertStringContainsString("'auth' => \$probe_auth", $this->controller);
        self::assertStringContainsString("'cart' => []", $this->controller);
        self::assertStringNotContainsString("\$guest_auth['area'] = 'A'", $this->controller);
        self::assertStringContainsString("'store_access_key' => \$store_access_key", $this->controller);
        self::assertStringContainsString("\$storefront_repository->findByUrl('https://talario.ru/dev_copy/')", $this->controller);
        self::assertStringContainsString("\$storefront_repository->findDefault()", $this->controller);
        self::assertStringContainsString("\$storefront->getCompanyIds()", $this->controller);
        self::assertStringContainsString("'pilot_preview_storefront_scope_invalid'", $this->controller);
        self::assertStringNotContainsString("\$storefront_repository->findByCompanyId(39)", $this->controller);
        self::assertStringContainsString("'talario_partner_sync_preview' => [", $this->controller);
        self::assertStringContainsString("'purpose' => 'visual_acceptance'", $this->controller);
        self::assertStringContainsString("'&skey=' . rawurlencode(\$session_key)", $this->controller);
        self::assertStringContainsString("serialize(\$sess_data)", $this->controller);
        self::assertStringContainsString("'products.view?product_id=1158'", $this->controller);
        self::assertStringContainsString("'schema_version' => 'partner-sync.preview.v4'", $this->controller);
        self::assertStringContainsString("'single_use' => true", $this->controller);
        self::assertStringContainsString("'state_url' => \$state_url", $this->controller);
        self::assertStringContainsString(
            "'https://talario.ru/dev_copy/index.php?dispatch=talario_analytics.penaty_preview_state'",
            $this->controller
        );
        self::assertStringContainsString("'talario_partner_sync_preview_state_token' => \$state_token", $this->controller);
        self::assertStringContainsString("'talario_partner_sync_preview_state_issued_at' => time()", $this->controller);
        self::assertStringContainsString('fn_talario_analytics_partner_sync_penaty_preview_state', $this->controller);
        self::assertStringContainsString("strtoupper((string) (\$_SERVER['REQUEST_METHOD'] ?? '')) !== 'POST'", $this->controller);
        self::assertStringContainsString("HTTP_ORIGIN", $this->controller);
        self::assertStringContainsString("HTTP_REFERER", $this->controller);
        self::assertStringContainsString("HTTP_SEC_FETCH_SITE", $this->controller);
        self::assertStringContainsString("HTTP_SEC_FETCH_MODE", $this->controller);
        self::assertStringContainsString("'same-origin'", $this->controller);
        self::assertStringNotContainsString("penaty_preview_state?state_token=", $this->controller);
        self::assertStringNotContainsString("(\$_GET['state_token'] ?? '')", $this->controller);
        self::assertStringNotContainsString("(\$_POST['state_token'] ?? '')", $this->controller);
        self::assertStringContainsString("(\$now - \$issued_at) <= 300", $this->controller);
        self::assertStringContainsString("'session_handoff_token_valid' => true", $this->controller);
        self::assertStringContainsString("'preview_marker_exact' => \$preview_exact", $this->controller);
        self::assertStringContainsString("'store_access_key_present'", $this->controller);
        self::assertStringContainsString("'store_access_key_matches_runtime'", $this->controller);
        self::assertStringContainsString("'visibility' => [", $this->controller);
        self::assertStringContainsString("'normal' => !empty(\$probe_normal)", $this->controller);
        self::assertStringContainsString("'preview' => !empty(\$probe_preview)", $this->controller);
        self::assertStringContainsString("'company_status' => \$company_status", $this->controller);
        self::assertStringContainsString("'main_category_storefront_id'", $this->controller);
        self::assertStringContainsString("unset(Tygh::\$app['session']['store_access_key'])", $this->preview_post_controller);
        self::assertStringContainsString("unset(Tygh::\$app['session']['talario_partner_sync_preview'])", $this->preview_post_controller);
        self::assertStringContainsString("(int) \$_REQUEST['product_id'] === 1158", $this->preview_post_controller);
        self::assertStringContainsString("'visual_acceptance'", $this->preview_post_controller);
        self::assertStringNotContainsString("['auth']", $this->preview_post_controller);
    }

    public function testFeatureDiscoveryIsBoundedReadOnlyDevCopyProbe(): void
    {
        self::assertStringContainsString("PHP_SAPI !== 'cli'", $this->feature_discovery_runner);
        self::assertStringContainsString("'/talario.ru/public_html/dev_copy'", $this->feature_discovery_runner);
        self::assertStringContainsString('fn_is_development()', $this->feature_discovery_runner);
        self::assertStringContainsString('TALARIO_PARTNER_SYNC_DEV_COPY', $this->feature_discovery_runner);
        self::assertStringContainsString('?:product_filters', $this->feature_discovery_runner);
        self::assertStringContainsString('?:product_features_values', $this->feature_discovery_runner);
        self::assertStringContainsString('?:product_feature_variants', $this->feature_discovery_runner);
        self::assertStringContainsString('%Возраст%', $this->feature_discovery_runner);
        self::assertStringContainsString('%Катег%', $this->feature_discovery_runner);
        self::assertStringContainsString(
            "'schema_version' => 'partner-sync.feature-discovery.v1'",
            $this->feature_discovery_runner
        );
        self::assertStringNotContainsString('db_query(', $this->feature_discovery_runner);
        self::assertStringNotContainsString('fn_update_', $this->feature_discovery_runner);
        self::assertStringNotContainsString('INSERT INTO', $this->feature_discovery_runner);
        self::assertStringNotContainsString('DELETE FROM', $this->feature_discovery_runner);
        self::assertStringNotContainsString('UPDATE ?:', $this->feature_discovery_runner);

        self::assertStringContainsString(
            '"talario-partner-sync-feature-discovery")',
            $this->dev_dispatcher
        );
        $runner_hash = hash('sha256', $this->feature_discovery_runner);
        self::assertStringContainsString(
            'EXPECTED_RUNNER_SHA256="' . $runner_hash . '"',
            $this->dev_dispatcher
        );
        self::assertStringContainsString(
            'TALARIO_PARTNER_SYNC_ROOT="$DEV_COPY"',
            $this->dev_dispatcher
        );
        self::assertStringContainsString(
            'dev_copy worktree must be clean for feature discovery',
            $this->dev_dispatcher
        );

        self::assertStringContainsString(
            "contains(github.event.head_commit.message, 'PART-SYNC: feature discovery probe')",
            $this->deploy_workflow
        );
        self::assertStringContainsString(
            'talario-partner-sync-feature-discovery',
            $this->deploy_workflow
        );
        self::assertStringContainsString(
            'partner-sync.feature-discovery.v1',
            $this->deploy_workflow
        );
    }

    public function testPartnerSyncWriteIsInternalCliOnly(): void
    {
        self::assertStringContainsString("PHP_SAPI !== 'cli'", $this->cli_runner);
        self::assertStringContainsString("'TALARIO_PARTNER_SYNC_ROOT'", $this->cli_runner);
        self::assertStringContainsString("'/talario.ru/public_html/dev_copy'", $this->cli_runner);
        self::assertStringContainsString('PARTNER_SYNC_ROOT_OWNER_MISMATCH', $this->cli_runner);
        self::assertStringContainsString('PARTNER_SYNC_ROOT_PERMISSIONS_UNSAFE', $this->cli_runner);
        self::assertStringContainsString('PARTNER_SYNC_RUNNER_TRUST_FAILED', $this->cli_runner);
        self::assertStringContainsString("@fopen(__FILE__, 'rb')", $this->cli_runner);
        self::assertStringContainsString('@fstat($runner_handle)', $this->cli_runner);
        self::assertStringContainsString("'TALARIO_PARTNER_SYNC_RUNNER_UID'", $this->cli_runner);
        self::assertStringContainsString('PARTNER_SYNC_RUNNER_OWNER_MISMATCH', $this->cli_runner);
        self::assertStringNotContainsString('@lstat(__FILE__)', $this->cli_runner);
        self::assertStringNotContainsString('is_link(__FILE__)', $this->cli_runner);
        self::assertStringContainsString('$root_uid === 0', $this->cli_runner);
        self::assertStringContainsString('$root_mode !== 0700', $this->cli_runner);
        self::assertStringNotContainsString('/home/t/tyman5tb/', $this->cli_runner);
        self::assertStringContainsString('fn_is_development()', $this->cli_runner);
        self::assertStringContainsString('TALARIO_PARTNER_SYNC_DEV_COPY', $this->cli_runner);
        self::assertStringNotContainsString("'catalog_apply' => true", $this->trusted_controllers);
        self::assertStringNotContainsString("catalog_apply", $this->controller);
    }

    public function testPartnerSyncWriteRequiresSeparateDevWriteGateAndApproval(): void
    {
        self::assertStringContainsString('TALARIO_PARTNER_SYNC_DEV_WRITE', $this->write_capability);
        self::assertStringContainsString('TALARIO_PARTNER_SYNC_DEV_WRITE_COMPANY_IDS', $this->write_capability);
        self::assertStringContainsString("['error' => 'company_not_write_allowed']", $this->write_capability);
        self::assertStringContainsString("SELECT status FROM ?:companies WHERE company_id = ?i", $this->write_capability);
        self::assertStringContainsString("['approved_company_id']", $this->write_capability);
        self::assertStringContainsString("['error' => 'company_run_approval_required']", $this->write_capability);
        self::assertStringContainsString("['error' => 'company_not_active']", $this->write_capability);
        self::assertStringContainsString('fn_talario_analytics_partner_sync_write_response();', $this->cli_runner);
        self::assertStringContainsString("['error' => 'partner_sync_write_disabled']", $this->write_capability);
        self::assertStringContainsString("['error' => 'approval_id_required']", $this->write_capability);
        self::assertStringContainsString("'approval_id_hash' => \$approval_id_hash", $this->write_capability);
        self::assertStringNotContainsString("'approval_id' => \$approval_id", $this->write_capability);
        self::assertStringContainsString("'dry_run' => true", $this->write_capability);
    }

    public function testPartnerSyncNormalizesUnderThreeAgeCopyWithoutChangingVariationTaxonomy(): void
    {
        self::assertStringContainsString(
            'fn_talario_analytics_partner_sync_normalize_age_copy',
            $this->write_capability
        );
        self::assertStringContainsString(
            "'до 3х лет'",
            $this->write_capability
        );
        self::assertStringContainsString(
            "if (\$field === 'full_description')",
            $this->write_capability
        );
        self::assertStringContainsString(
            "fn_talario_analytics_partner_sync_normalize_age_copy(\$value)",
            $this->write_capability
        );
        self::assertStringContainsString(
            "['Возраст', 'Возрастная группа', 'Класс']",
            $this->write_capability
        );
    }

    public function testPartnerSyncStripsNumericAvailabilityFromDescriptionOnly(): void
    {
        self::assertStringContainsString(
            'fn_talario_analytics_partner_sync_strip_availability_count_copy',
            $this->write_capability
        );
        self::assertStringContainsString(
            'свободных\\\\s+мест',
            $this->write_capability
        );
        self::assertStringContainsString(
            'мест\\\\s+осталось',
            $this->write_capability
        );
        self::assertStringContainsString(
            'осталось\\\\s+\\\\d+\\\\s+мест',
            $this->write_capability
        );
        self::assertStringContainsString(
            "if (\$field === 'full_description')",
            $this->write_capability
        );
        self::assertStringContainsString(
            'fn_talario_analytics_partner_sync_strip_availability_count_copy($value)',
            $this->write_capability
        );
        self::assertStringContainsString(
            'fn_ec_save_booking_data_by_amount',
            $this->write_capability
        );
    }

    public function testPartnerSyncWriteUsesCoreProductAndEcarterHooks(): void
    {
        self::assertStringContainsString('fn_update_product(', $this->write_capability);
        self::assertStringContainsString("\$product_data['booking_data'] = \$booking_data", $this->write_capability);
        self::assertStringContainsString('?:ec_table_booking_system', $this->write_capability);
        self::assertStringContainsString("'schema_version' => 'partner-sync.write-result.v1'", $this->write_capability);
    }

    public function testPartnerSyncCreateUsesGlobalDetailsLayoutInheritance(): void
    {
        self::assertStringContainsString(
            "if (array_key_exists('details_layout', \$product))",
            $this->write_capability
        );
        self::assertStringContainsString(
            "\$data['details_layout'] = 'default';",
            $this->write_capability
        );
        self::assertStringContainsString(
            "'invalid_details_layout'",
            $this->write_capability
        );
        self::assertStringContainsString(
            "p.details_layout",
            $this->write_capability
        );
        self::assertStringContainsString(
            "'details_layout' => (string) \$row['details_layout']",
            $this->write_capability
        );

        $copy_schema = (string) file_get_contents(
            dirname(__DIR__, 2) . '/product_variations/schemas/product_variations/product_data_copy.php'
        );
        self::assertStringContainsString(
            "MainTable::create('products', 'product_id'",
            $copy_schema
        );
        self::assertStringNotContainsString("'details_layout'", $copy_schema);
    }

    public function testPartnerSyncCreateShortDescriptionContainsMinimumAge(): void
    {
        $helper_offset = strpos(
            $this->write_capability,
            'function fn_talario_analytics_partner_sync_minimum_age_short_description'
        );
        self::assertNotFalse($helper_offset);
        $helper_end = strpos(
            $this->write_capability,
            'function fn_talario_analytics_partner_sync_write_normalize_product',
            $helper_offset
        );
        self::assertNotFalse($helper_end);
        $helper_section = substr($this->write_capability, $helper_offset, $helper_end - $helper_offset);

        self::assertStringContainsString("['age_group']", $helper_section);
        self::assertStringContainsString("'с 1го года'", $helper_section);
        self::assertStringContainsString("\$minimum >= 2 && \$minimum <= 4", $helper_section);
        self::assertStringContainsString("'с ' . \$minimum . 'х лет'", $helper_section);
        self::assertStringContainsString("'с ' . \$minimum . ' лет'", $helper_section);
        self::assertStringContainsString('(?:го\\\\s*)?года', $helper_section);
        self::assertStringNotContainsString('fn_update_product_feature_variant(', $helper_section);
        self::assertStringNotContainsString('?:product_feature_variants', $helper_section);

        self::assertStringContainsString(
            "if (\$operation === 'create' && \$variation_plan !== null)",
            $this->write_capability
        );
        self::assertStringContainsString(
            'fn_talario_analytics_partner_sync_minimum_age_short_description(',
            $this->write_capability
        );
        self::assertStringContainsString(
            "'short_description' => (string) \$row['short_description']",
            $this->write_capability
        );
    }

    public function testPartnerSyncCreateEnforcesPopularityFloorBeforeVariations(): void
    {
        $helper_offset = strpos(
            $this->write_capability,
            'function fn_talario_analytics_partner_sync_ensure_popularity_floor'
        );
        self::assertNotFalse($helper_offset);
        $helper_end = strpos(
            $this->write_capability,
            'function fn_talario_analytics_partner_sync_write_readback',
            $helper_offset
        );
        self::assertNotFalse($helper_end);
        $helper_section = substr($this->write_capability, $helper_offset, $helper_end - $helper_offset);

        self::assertStringContainsString(
            'SELECT total FROM ?:product_popularity WHERE product_id = ?i',
            $helper_section
        );
        self::assertStringContainsString(
            'fn_update_product_popularity($product_id',
            $helper_section
        );
        self::assertStringContainsString(
            "'product_popularity_floor_failed'",
            $this->write_capability
        );

        $response_offset = strpos(
            $this->write_capability,
            'function fn_talario_analytics_partner_sync_write_response'
        );
        self::assertNotFalse($response_offset);
        $response_section = substr($this->write_capability, $response_offset);

        $popularity_offset = strpos(
            $response_section,
            'fn_talario_analytics_partner_sync_ensure_popularity_floor($product_id, 1000)'
        );
        $variation_offset = strpos(
            $response_section,
            'fn_talario_analytics_partner_sync_apply_variation_plan('
        );
        self::assertNotFalse($popularity_offset);
        self::assertNotFalse($variation_offset);
        self::assertLessThan($variation_offset, $popularity_offset);
        self::assertStringContainsString(
            "if (\$operation === 'create')",
            $response_section
        );
        self::assertStringContainsString(
            "'popularity_write'",
            $response_section
        );

        self::assertStringContainsString(
            'LEFT JOIN ?:product_popularity pop ON pop.product_id = p.product_id',
            $this->write_capability
        );
        self::assertStringContainsString(
            "'popularity' => (int) \$row['popularity']",
            $this->write_capability
        );

        $copy_schema = (string) file_get_contents(
            dirname(__DIR__, 2) . '/product_variations/schemas/product_variations/product_data_copy.php'
        );
        self::assertStringContainsString("'product_popularity'", $copy_schema);
    }

    public function testPartnerSyncCreateDefaultsToHidden(): void
    {
        self::assertStringContainsString("\$data['status'] = 'H';", $this->write_capability);
        self::assertStringContainsString('New Partner Sync cards are hidden by default', $this->write_capability);
    }

    public function testSignedPenatyApplyUsesIsolatedReviewedCliRunner(): void
    {
        self::assertStringContainsString('fn_talario_analytics_partner_sync_run_penaty_cli', $this->controller);
        self::assertStringContainsString("'/usr/local/bin/php8.2'", $this->controller);
        self::assertStringContainsString("'/usr/bin/php8.2'", $this->controller);
        self::assertStringContainsString("'/usr/local/php82/bin/php'", $this->controller);
        self::assertStringContainsString("is_link(\$php_candidate)", $this->controller);
        self::assertStringContainsString("(int) \$candidate_lstat['uid'] !== 0", $this->controller);
        self::assertStringContainsString("(int) \$parent_stat['uid'] !== 0", $this->controller);
        self::assertStringContainsString("(\$parent_stat['mode'] & 0022)", $this->controller);
        self::assertStringContainsString("(\$candidate_stat['mode'] & 06000)", $this->controller);
        self::assertStringContainsString("'/ops/partner-sync-apply.php'", $this->controller);
        self::assertStringContainsString("'74bb7882e0f40b7984e66ed12985cf497c257b1c10f22c91efaea36fba55d407'", $this->controller);
        self::assertStringContainsString("stream_get_contents(\$runner_handle", $this->controller);
        self::assertStringContainsString("hash_equals(\$expected_runner_sha256, hash('sha256', \$runner_source))", $this->controller);
        self::assertStringContainsString("'TALARIO_PARTNER_SYNC_ROOT' => DIR_ROOT", $this->controller);
        self::assertStringContainsString("'TALARIO_PARTNER_SYNC_RUNNER_UID'", $this->controller);
        self::assertStringContainsString("define('TALARIO_PARTNER_SYNC_DEV_WRITE', true)", $this->controller);
        self::assertStringNotContainsString("define('TALARIO_PARTNER_SYNC_DEV_WRITE_COMPANY_IDS', '39')", $this->controller);
        self::assertStringContainsString("'/usr/bin/timeout'", $this->controller);
        self::assertStringContainsString("'--kill-after=5s'", $this->controller);
        self::assertStringContainsString("'45s'", $this->controller);
        self::assertStringContainsString("'HOME' => $tmp_dir", $this->controller);
        self::assertStringContainsString("'TMPDIR' => $tmp_dir", $this->controller);
        self::assertStringContainsString("'pilot_cli_timeout'", $this->controller);
        self::assertStringContainsString("'pilot_cli_invalid_response'", $this->controller);
        self::assertStringContainsString("in_array(\$status, [200, 201], true)", $this->controller);
        self::assertStringNotContainsString("\$status = \$status >= 400 && \$status <= 599 ? \$status : (\$rc === 0 ? 200 : 500);", $this->controller);
        self::assertStringContainsString("$invalid_response_error . '_' . $runner_rc_code", $this->controller);
        self::assertStringContainsString("255 => 'rc255'", $this->controller);
        self::assertStringContainsString("?? 'rc_other'", $this->controller);
        self::assertStringContainsString("'runner_failure'", $this->controller);
        self::assertStringContainsString("'pilot_cli_child_exception'", $this->controller);
        self::assertStringContainsString("error_log('Talario Partner Sync Penaty child exception class='", $this->controller);
        self::assertStringNotContainsString('TALARIO_CHILD_FATAL=', $this->controller);
        self::assertStringNotContainsString("'error_log=/dev/stderr'", $this->controller);
        self::assertStringContainsString("'permission_denied'", $this->controller);
        self::assertStringContainsString("'required_file_failed'", $this->controller);
        self::assertStringContainsString("'undefined_function'", $this->controller);
        self::assertStringContainsString("'undefined_class'", $this->controller);
        self::assertStringContainsString("'undefined_constant'", $this->controller);
        self::assertStringContainsString("'parse_error'", $this->controller);
        self::assertStringContainsString("'memory_exhausted'", $this->controller);
        self::assertStringContainsString('$max_payload_bytes = 20971520;', $this->controller);
        self::assertStringContainsString("isset(\$_SERVER['CONTENT_LENGTH'])", $this->controller);
        self::assertStringContainsString('stream_get_contents($input, $max_payload_bytes + 1)', $this->controller);
        self::assertStringContainsString("'payload_too_large'", $this->controller);
        self::assertStringNotContainsString("'HOME' => (string) getenv('HOME')", $this->controller);
        self::assertStringContainsString('TALARIO_PARTNER_SYNC_PENATY_CLI_DIAGNOSTIC', $this->controller);
        self::assertStringContainsString("'TALARIO_PARTNER_SYNC_STAGE_FILE' => \$tmp_dir . DIRECTORY_SEPARATOR . 'stage'", $this->controller);
        self::assertStringContainsString('$runner_stage = null;', $this->controller);
        self::assertStringContainsString("@fopen(\$stage_file, 'rb')", $this->controller);
        self::assertStringContainsString('@fstat($stage_handle)', $this->controller);
        self::assertStringContainsString("(int) \$stage_lstat['ino'] === (int) \$stage_stat['ino']", $this->controller);
        self::assertStringContainsString('stream_get_contents($stage_handle, 65)', $this->controller);
        self::assertStringNotContainsString('@file_get_contents($stage_file)', $this->controller);
        self::assertStringContainsString('$invalid_response_code .= \'_\' . $runner_stage;', $this->controller);
        self::assertStringNotContainsString('register_shutdown_function(static function', $this->controller);
        self::assertStringContainsString("'base_product_write'", $this->controller);
        self::assertStringContainsString("'failure_cleanup'", $this->controller);
        self::assertStringContainsString('fn_talario_analytics_partner_sync_set_cli_stage', $this->write_capability);
        self::assertStringContainsString("getenv('TALARIO_PARTNER_SYNC_STAGE_FILE')", $this->write_capability);
        self::assertStringContainsString('@file_put_contents($stage_file, $stage, LOCK_EX)', $this->write_capability);
        self::assertStringContainsString('@chmod($stage_file, 0600)', $this->write_capability);
        self::assertStringContainsString("fn_talario_analytics_partner_sync_set_cli_stage('base_product_write', true)", $this->write_capability);
        self::assertStringContainsString("fn_talario_analytics_partner_sync_set_cli_stage('variation_product_write', true)", $this->write_capability);
        self::assertStringContainsString("fn_talario_analytics_partner_sync_set_cli_stage('failure_cleanup', true)", $this->write_capability);
        self::assertStringContainsString("fn_talario_analytics_partner_sync_set_cli_stage('variation_group_lookup', true)", $this->write_capability);
        self::assertStringContainsString("fn_talario_analytics_partner_sync_set_cli_stage('variation_group_feature_values', true)", $this->write_capability);
        self::assertStringContainsString("fn_talario_analytics_partner_sync_set_cli_stage('variation_group_product_ids', true)", $this->write_capability);
        self::assertStringContainsString("fn_talario_analytics_partner_sync_set_cli_stage('variation_booking_build', true)", $this->write_capability);
        self::assertStringContainsString('} finally {', $this->write_capability);
        self::assertStringContainsString("fn_talario_analytics_partner_sync_set_cli_stage('variation_booking_build', false)", $this->write_capability);
        self::assertStringContainsString("'variation_booking_build'", $this->controller);
        self::assertStringNotContainsString('unlink($stage_file)', $this->write_capability);
        self::assertStringContainsString("'variation_group_lookup'", $this->controller);
        self::assertStringContainsString("'variation_group_feature_values'", $this->controller);
    }

    public function testPartnerSyncWriteFailureDiagnosticIsBoundedAndLoggingCannotMaskIt(): void
    {
        self::assertStringContainsString('fn_talario_analytics_partner_sync_safe_write_error_detail', $this->write_capability);
        self::assertStringContainsString("'variation_group_create_failed'", $this->write_capability);
        self::assertStringContainsString("'variation_product_mapping_failed'", $this->write_capability);
        self::assertStringContainsString("'variation_product_update_failed'", $this->write_capability);
        self::assertStringContainsString("'image_update_failed'", $this->write_capability);
        self::assertStringContainsString("'image_temp_dir_mode_mismatch'", $this->write_capability);
        self::assertStringContainsString("'image_temp_dir_uid_changed'", $this->write_capability);
        self::assertStringContainsString("'image_temp_file_mode_mismatch'", $this->write_capability);
        self::assertStringContainsString("'image_temp_file_uid_mismatch'", $this->write_capability);
        self::assertStringContainsString("'image_temp_file_size_mismatch'", $this->write_capability);
        self::assertStringNotContainsString("'image_temp_file_unsafe'", $this->write_capability);
        self::assertStringContainsString('catch (Throwable $log_exception)', $this->write_capability);
        self::assertStringContainsString('$error_response[\'detail\'] = $safe_error_detail', $this->write_capability);
        self::assertStringNotContainsString('$exception->getTraceAsString()', $this->write_capability);
    }


    public function testPartnerSyncWriteSupportsBoundedPrivateImageImport(): void
    {
        self::assertStringContainsString('content_base64', $this->write_capability);
        self::assertStringContainsString('getimagesizefromstring', $this->write_capability);
        self::assertStringContainsString("['image/jpeg', 'image/png', 'image/webp']", $this->write_capability);

        self::assertStringContainsString("fn_get_cache_path(false)", $this->write_capability);
        self::assertStringContainsString('random_bytes(16)', $this->write_capability);
        self::assertStringContainsString("mkdir(\$temp_dir, 0700, false)", $this->write_capability);
        self::assertStringContainsString('if (!chmod($temp_dir, 0700))', $this->write_capability);
        self::assertStringContainsString("fopen(\$tmp, 'x+b')", $this->write_capability);
        self::assertStringContainsString('if (!chmod($tmp, 0600))', $this->write_capability);
        self::assertStringContainsString('flock($handle, LOCK_EX)', $this->write_capability);
        self::assertStringContainsString('fstat($handle)', $this->write_capability);
        self::assertStringContainsString("\$staging_uid = (int) \$temp_dir_stat['uid']", $this->write_capability);
        self::assertStringContainsString("(int) \$temp_dir_stat_after['ino'] !== \$verified_dir_ino", $this->write_capability);
        self::assertStringContainsString("(int) \$file_lstat['ino'] !== (int) \$file_stat['ino']", $this->write_capability);
        self::assertStringContainsString("((int) \$file_stat['mode'] & 0777) !== 0600", $this->write_capability);
        self::assertStringContainsString("(int) \$temp_dir_stat_after['uid'] !== \$staging_uid", $this->write_capability);
        self::assertStringContainsString("(int) \$file_stat['uid'] !== \$staging_uid", $this->write_capability);
        self::assertStringContainsString("(int) \$file_lstat['uid'] !== \$staging_uid", $this->write_capability);
        self::assertStringNotContainsString('$expected_uid', $this->write_capability);
        self::assertStringNotContainsString('@chmod($tmp, 0600)', $this->write_capability);
        self::assertStringContainsString("throw new RuntimeException('image_temp_file_fstat_failed')", $this->write_capability);
        self::assertStringContainsString("throw new RuntimeException('image_temp_file_ino_mismatch')", $this->write_capability);
        self::assertStringContainsString("throw new RuntimeException('image_temp_file_lstat_uid_mismatch')", $this->write_capability);
        self::assertStringContainsString("if (is_link(\$temp_dir)) {", $this->write_capability);
        self::assertStringContainsString("unlink(\$temp_dir);", $this->write_capability);

        self::assertStringContainsString('fn_update_image_pairs(', $this->write_capability);
        self::assertStringContainsString('update_alt_desc: true', $this->write_capability);
        self::assertStringContainsString("object_type: 'product'", $this->write_capability);
        self::assertStringContainsString("'is_new' => \$index === 0 ? 'Y' : 'N'", $this->write_capability);
        self::assertStringContainsString("'image_update_failed'", $this->write_capability);
        self::assertStringContainsString('SELECT pair_id, object_id, object_type, type, detailed_id', $this->write_capability);
        self::assertStringContainsString('$main_count !== 1', $this->write_capability);
        self::assertStringContainsString('$additional_count !== $expected_count - 1', $this->write_capability);
        self::assertStringContainsString('array_diff($current_pair_ids, $old_pair_ids)', $this->write_capability);
        self::assertStringContainsString('fn_delete_image_pair((int) $partial_pair_id)', $this->write_capability);

        self::assertStringNotContainsString('runtime.allow_upload_external_paths', $this->write_capability);
        self::assertStringNotContainsString('skip_area_checking', $this->write_capability);
        self::assertStringNotContainsString("'server'", $this->write_capability);

        $images_core = (string) file_get_contents(dirname(__DIR__, 3) . '/functions/fn.images.php');
        self::assertStringContainsString(
            'function fn_update_image_pairs($icons, $detailed, $pairs_data, $object_id = 0, $object_type = \'product_lists\', $object_ids = array(), $update_alt_desc = true, $lang_code = CART_LANGUAGE, $from_exist_pairs = false)',
            $images_core
        );
        self::assertStringContainsString("'images' => [", $this->write_capability);
    }

    public function testDevDispatcherHasFixedPartnerSyncCommands(): void
    {
        self::assertStringContainsString(
            '"talario-partner-sync-dry-run")',
            $this->dev_dispatcher
        );
        self::assertStringContainsString('"$PHP_REAL" "$RUNNER_TMP"', $this->dev_dispatcher);
        self::assertStringContainsString('20971520', $this->dev_dispatcher);
        self::assertStringContainsString('DRY_RUN_REQUIRED', $this->dev_dispatcher);
        self::assertStringContainsString('PAYLOAD_READ_FAILED', $this->dev_dispatcher);
        self::assertStringNotContainsString('PATH="/usr/local/bin:/usr/bin:/bin"', $this->dev_dispatcher);
        self::assertStringContainsString('/usr/bin/git -C "$DEV_COPY" status --porcelain --untracked-files=all', $this->dev_dispatcher);
        self::assertStringContainsString('/usr/bin/git -C "$DEV_COPY" rev-parse HEAD', $this->dev_dispatcher);
        self::assertStringContainsString('/usr/bin/git -C "$DEV_COPY" show "$RUNNER_COMMIT:$RUNNER_REL" > "$RUNNER_TMP"', $this->dev_dispatcher);
        self::assertStringContainsString('[ -x /usr/bin/git ] || fail "required git binary unavailable" 81', $this->dev_dispatcher);
        self::assertStringContainsString('dev_copy worktree must be clean for partner sync', $this->dev_dispatcher);
        self::assertStringContainsString('partner sync CLI runner integrity check failed', $this->dev_dispatcher);
        self::assertStringContainsString('trusted PHP binary owner mismatch', $this->dev_dispatcher);
        self::assertStringContainsString('trusted PHP binary is group/world writable', $this->dev_dispatcher);
        self::assertStringContainsString('mktemp "$STATE_DIR/runner.XXXXXX.php"', $this->dev_dispatcher);
        self::assertStringContainsString('EXPECTED_RUNNER_SHA256="74bb7882e0f40b7984e66ed12985cf497c257b1c10f22c91efaea36fba55d407"', $this->dev_dispatcher);
        self::assertStringContainsString('partner sync CLI runner is not allowlisted', $this->dev_dispatcher);
        $runner_hash = hash('sha256', $this->cli_runner);
        self::assertStringContainsString('EXPECTED_RUNNER_SHA256="' . $runner_hash . '"', $this->dev_dispatcher);
        self::assertStringContainsString('/usr/bin/timeout --signal=TERM --kill-after=5s 60s', $this->dev_dispatcher);
        self::assertStringContainsString('RUNNER_UID="$(/usr/bin/id -u)"', $this->dev_dispatcher);
        self::assertStringContainsString('TALARIO_PARTNER_SYNC_ROOT="$DEV_COPY" TALARIO_PARTNER_SYNC_RUNNER_UID="$RUNNER_UID"', $this->dev_dispatcher);
        self::assertStringContainsString('partner sync dry-run execution timeout', $this->dev_dispatcher);
        self::assertStringNotContainsString('talario-partner-sync-apply', $this->dev_dispatcher);
        self::assertStringContainsString('/usr/bin/timeout 30s /usr/bin/head -c 20971521', $this->dev_dispatcher);
    }

    public function testDevDryRunSmokeIsFixedAndServerEnforced(): void
    {
        self::assertStringContainsString('partner-sync-dry-run-smoke', $this->dev_ops_workflow);
        self::assertStringContainsString("REMOTE_COMMAND='talario-partner-sync-dry-run'", $this->dev_ops_workflow);
        self::assertStringNotContainsString('payload_b64', $this->dev_ops_workflow);
        self::assertStringContainsString('company_id: 39', $this->dev_ops_workflow);
        self::assertStringContainsString('category_ids: [270]', $this->dev_ops_workflow);
        self::assertStringContainsString('SAFE_ERROR=', $this->dev_ops_workflow);
        self::assertStringContainsString('{error, http_status: (.http_status // null)}', $this->dev_ops_workflow);

        self::assertStringContainsString('DRY_RUN_REQUIRED', $this->dev_dispatcher);
        self::assertStringContainsString('EXPECTED_RUNNER_SHA256=', $this->dev_dispatcher);
        self::assertStringNotContainsString('talario-partner-sync-apply', $this->dev_dispatcher);
    }

    public function testDevDispatcherDoesNotExposeGenericShellForPartnerSync(): void
    {
        self::assertStringNotContainsString('eval ', $this->dev_dispatcher);
        self::assertStringNotContainsString('bash -c "$REQUEST"', $this->dev_dispatcher);
        self::assertStringContainsString('"talario-partner-sync-enable-penaty-pilot")', $this->dev_dispatcher);
        self::assertStringContainsString('PILOT_COMPANY_ID=39', $this->dev_dispatcher);
        self::assertStringContainsString('TALARIO_PARTNER_SYNC_DEV_WRITE_COMPANY_IDS', $this->dev_dispatcher);
        self::assertStringContainsString('CONFIG_TRUST_FAILED', $this->dev_dispatcher);
        self::assertStringContainsString('flock($fh, LOCK_EX)', $this->dev_dispatcher);
        self::assertStringContainsString('fstat($fh)', $this->dev_dispatcher);
        self::assertStringContainsString('(int) $st["ino"] !== (int) $lst["ino"]', $this->dev_dispatcher);
        self::assertStringContainsString('[ "$DEV_COPY" = "$EXPECTED_PATH" ]', $this->dev_dispatcher);
        self::assertStringContainsString('partner-sync-enable-penaty-pilot', $this->dev_ops_workflow);
        self::assertStringContainsString('fail "SSH command is not allowlisted"', $this->dev_dispatcher);
    }


    public function testAnalyticsProdHashSyncIsFixedAndDoesNotExposeGenericShell(): void
    {
        self::assertStringContainsString('talario-analytics-prod-sync', $this->dev_dispatcher);
        self::assertStringContainsString('EXPECTED_PROD_SHA="2dea53c94eecc33d84980bab1b808a35258e03f7"', $this->dev_dispatcher);
        self::assertStringContainsString('/usr/bin/timeout 10s /usr/bin/head -c 66', $this->dev_dispatcher);
        self::assertStringContainsString('[[ "$HASH" =~ ^[a-f0-9]{64}$ ]]', $this->dev_dispatcher);
        self::assertStringContainsString('TALARIO_CONFIRM_PROD_DEPLOY="$TARGET"', $this->dev_dispatcher);
        self::assertStringContainsString('sync-analytics-prod-hash.sh', $this->dev_dispatcher);
        self::assertStringContainsString('ANALYTICS_PROD_SYNC=PASS', $this->dev_dispatcher);
        self::assertStringContainsString('STOREFRONT_HEALTH=PASS', $this->dev_dispatcher);
        self::assertStringNotContainsString('eval ', $this->dev_dispatcher);
        self::assertStringNotContainsString('bash -c "$REQUEST"', $this->dev_dispatcher);
    }

    public function testPartnerSyncVariationPlanUsesExistingFeatureVariantsOnly(): void
    {
        self::assertStringContainsString('fn_talario_analytics_partner_sync_normalize_variation_plan', $this->write_capability);
        self::assertStringContainsString('fn_talario_analytics_partner_sync_resolve_variation_axis', $this->write_capability);
        self::assertStringContainsString("['Возраст', 'Возрастная группа', 'Класс']", $this->write_capability);
        self::assertStringContainsString("['Занятия', 'Занятие']", $this->write_capability);
        self::assertStringContainsString('?:product_feature_variants', $this->write_capability);
        self::assertStringContainsString('?:product_feature_variant_descriptions', $this->write_capability);
        self::assertStringNotContainsString('fn_update_product_feature_variant(', $this->write_capability);
        self::assertStringContainsString("'error' => 'variation_resolution_required'", $this->write_capability);
        self::assertStringContainsString('fn_talario_analytics_partner_sync_public_variation_resolution', $this->write_capability);
        self::assertStringContainsString("'missing_variants' => array_values", $this->write_capability);
    }

    public function testPartnerSyncFilterFeaturesUseDynamicRangesNativeWriteAndNativeVariationCopy(): void
    {
        if (!defined('BOOTSTRAP')) {
            define('BOOTSTRAP', true);
        }
        require_once dirname(__DIR__) . '/partner_sync_write.php';

        $resolved = \fn_talario_analytics_partner_sync_filter_age_years([
            ['age_group' => '3–5 лет'],
            ['age_group' => '5-7 лет'],
            ['age_group' => '6-9 лет'],
        ]);
        self::assertTrue($resolved['resolved']);
        self::assertSame([3, 4, 5, 6, 7, 8, 9], $resolved['ages']);

        $unsupported = \fn_talario_analytics_partner_sync_filter_age_years([
            ['age_group' => 'до 3 лет'],
            ['age_group' => '1,5-3 года'],
            ['age_group' => '16+ лет'],
        ]);
        self::assertFalse($unsupported['resolved']);
        self::assertSame(['до 3 лет', '1,5-3 года', '16+ лет'], $unsupported['unsupported_age_groups']);

        self::assertSame(
            'Ранее развитие',
            \fn_talario_analytics_partner_sync_category_filter_label('Раннее развитие')
        );
        self::assertSame(
            'Программирование',
            \fn_talario_analytics_partner_sync_category_filter_label('Программирование для детей')
        );
        self::assertSame(
            'Робототехника',
            \fn_talario_analytics_partner_sync_category_filter_label('Робототехника для детей')
        );
        self::assertSame(
            'Языки',
            \fn_talario_analytics_partner_sync_category_filter_label('Иностранные языки')
        );

        self::assertMatchesRegularExpression(
            "/fn_talario_analytics_partner_sync_resolve_find_products_feature\\(\\s*'Возраст',\\s*'M'/u",
            $this->write_capability
        );
        self::assertMatchesRegularExpression(
            "/fn_talario_analytics_partner_sync_resolve_find_products_feature\\(\\s*'Категории',\\s*'S'/u",
            $this->write_capability
        );
        self::assertStringContainsString("'find_products'", $this->write_capability);
        self::assertStringContainsString('?:product_filters', $this->write_capability);
        self::assertStringContainsString('fn_update_product_features_value($product_id, $values, [], $lang_code)', $this->write_capability);
        self::assertStringContainsString('filter_features_readback_mismatch', $this->write_capability);
        self::assertStringContainsString('filter_features_variation_copy_mismatch', $this->write_capability);
        self::assertStringNotContainsString('fn_update_product_feature_variant(', $this->write_capability);

        $copy_schema = (string) file_get_contents(
            dirname(__DIR__, 2) . '/product_variations/schemas/product_variations/product_data_copy.php'
        );
        $copy_functions = (string) file_get_contents(
            dirname(__DIR__, 2) . '/product_variations/schemas/product_variations/functions.php'
        );
        self::assertStringContainsString("'product_features_values'", $copy_schema);
        self::assertStringContainsString(
            'fn_product_variations_get_product_sync_feature_conditions',
            $copy_schema
        );
        self::assertStringContainsString(
            "return [['NOT IN', 'feature_id', \$feature_ids]];",
            $copy_functions
        );

        $response_offset = strpos(
            $this->write_capability,
            'function fn_talario_analytics_partner_sync_write_response'
        );
        self::assertNotFalse($response_offset);
        $response_section = substr($this->write_capability, $response_offset);
        $filter_offset = strpos(
            $response_section,
            'fn_talario_analytics_partner_sync_apply_filter_features('
        );
        $variation_offset = strpos(
            $response_section,
            'fn_talario_analytics_partner_sync_apply_variation_plan('
        );
        self::assertNotFalse($filter_offset);
        self::assertNotFalse($variation_offset);
        self::assertLessThan($variation_offset, $filter_offset);

        $apply_offset = strpos(
            $this->write_capability,
            'function fn_talario_analytics_partner_sync_apply_variation_plan'
        );
        $prepare_offset = strpos(
            $this->write_capability,
            'function fn_talario_analytics_partner_sync_write_create_direct_image_temp_file',
            $apply_offset
        );
        self::assertNotFalse($apply_offset);
        self::assertNotFalse($prepare_offset);
        $variation_section = substr($this->write_capability, $apply_offset, $prepare_offset - $apply_offset);
        self::assertStringNotContainsString("'find_products'", $variation_section);
        self::assertStringNotContainsString("'Категории'", $variation_section);
    }

    public function testPartnerSyncVariationLabelMatchingNormalizesEquivalentCommercialLabels(): void
    {
        self::assertStringContainsString('fn_talario_analytics_partner_sync_variation_label_key', $this->write_capability);
        self::assertStringContainsString("['Занятия', 'Занятие']", $this->write_capability);
        self::assertStringContainsString("абонемент\\\\s+на\\\\s+", $this->write_capability);
        self::assertStringContainsString("абонемент $1 занятий", $this->write_capability);
        self::assertStringContainsString("preg_replace('/(?<=\\\\d)\\\\s*лет/u'", $this->write_capability);
        self::assertStringNotContainsString('fn_update_product_feature_variant(', $this->write_capability);
    }

    public function testPartnerSyncVariationGroupMappingUsesNativeStructuralRepository(): void
    {
        $map_offset = strpos(
            $this->write_capability,
            'function fn_talario_analytics_partner_sync_map_group_products'
        );
        self::assertNotFalse($map_offset);
        $map_end = strpos(
            $this->write_capability,
            'function fn_talario_analytics_partner_sync_assert_variation_write_gate',
            $map_offset
        );
        self::assertNotFalse($map_end);
        $map_section = substr($this->write_capability, $map_offset, $map_end - $map_offset);

        self::assertStringContainsString('ServiceProvider::getGroupRepository()', $map_section);
        self::assertStringContainsString('findGroupProductsFeaturesValues', $map_section);
        self::assertStringContainsString('$group->getProductIds()', $map_section);
        self::assertStringNotContainsString('ServiceProvider::getProductRepository()', $map_section);
        self::assertStringNotContainsString('->findProducts(', $map_section);
        self::assertStringNotContainsString('->loadProductsFeatures(', $map_section);
    }

    public function testPartnerSyncVariationResponseDoesNotExposeInternalIds(): void
    {
        $public_offset = strpos(
            $this->write_capability,
            'function fn_talario_analytics_partner_sync_public_variation_axis'
        );
        self::assertNotFalse($public_offset);
        $public_section = substr($this->write_capability, $public_offset, 1800);
        self::assertStringNotContainsString("'feature_id'", $public_section);
        self::assertStringNotContainsString("'variant_id'", $public_section);
        self::assertStringNotContainsString("'variants'", $public_section);
    }

    public function testPartnerSyncVariationPlanIsBoundedAndValidated(): void
    {
        self::assertStringContainsString("count(\$payload['variation_plan']) > 100", $this->write_capability);
        self::assertStringContainsString("'error' => 'duplicate_variation_item'", $this->write_capability);
        self::assertStringContainsString("'error' => 'invalid_variation_schedule_item'", $this->write_capability);
        self::assertStringContainsString("'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'", $this->write_capability);
        self::assertStringContainsString("\$duration > 1440", $this->write_capability);
        self::assertStringContainsString("'duration' => \$item['duration']", $this->write_capability);
    }

    public function testPartnerSyncWritesDedicatedLessonAddress(): void
    {
        self::assertStringContainsString(
            "array_key_exists('address', \\$product)",
            $this->write_capability
        );
        self::assertStringContainsString(
            "mb_strlen(\\$address, 'UTF-8') > 255",
            $this->write_capability
        );
        self::assertStringContainsString(
            "['error' => 'invalid_address']",
            $this->write_capability
        );
        self::assertStringContainsString(
            "'address' => (string) \\$row['address']",
            $this->write_capability
        );
        self::assertStringContainsString(
            'pd.full_description, pd.address, COALESCE(pp.price, 0)',
            $this->write_capability
        );
    }

    public function testPartnerSyncSeparatesEcarterBookingWindowFromActualSlot(): void
    {
        self::assertStringContainsString(
            'function fn_talario_analytics_partner_sync_build_variation_booking',
            $this->write_capability
        );
        self::assertStringContainsString(
            '$window_end_minutes = $session_end_minutes + 1;',
            $this->write_capability
        );
        self::assertStringContainsString(
            "'error' => 'booking_window_not_representable'",
            $this->write_capability
        );
        self::assertStringContainsString(
            "'reason' => 'duration_mismatch'",
            $this->write_capability
        );
        self::assertStringContainsString(
            "'variation_booking_windows' => \$public_variation_booking_windows",
            $this->write_capability
        );
        self::assertStringContainsString(
            "'booking_window' => [",
            $this->write_capability
        );
        self::assertStringContainsString(
            "'start_time' => \$session['start']",
            $this->write_capability
        );
        self::assertStringContainsString(
            "'end_time' => \$session['end']",
            $this->write_capability
        );
        self::assertStringContainsString(
            "'variation_readback_booking_window_mismatch'",
            $this->write_capability
        );
        self::assertStringContainsString(
            "'reason' => 'minute_out_of_range'",
            $this->write_capability
        );
        self::assertStringNotContainsString(
            "InvalidArgumentException('booking_window_minute_out_of_range')",
            $this->write_capability
        );
    }

    public function testPartnerSyncWriteRejectsPartnerReassignment(): void
    {
        self::assertStringContainsString("['error' => 'company_change_forbidden']", $this->write_capability);
    }

    public function testCatalogRequiresExplicitEnvironmentGate(): void
    {
        self::assertStringContainsString("fn_is_development()", $this->controller);
        self::assertStringContainsString('TALARIO_PARTNER_SYNC_DEV_COPY', $this->controller);
        self::assertStringContainsString('TALARIO_PARTNER_SYNC_PROD_READ', $this->controller);
        self::assertStringContainsString('$prod_read_enabled = !$is_development', $this->controller);
        self::assertStringContainsString('if (!$dev_copy_enabled && !$prod_read_enabled)', $this->controller);
        self::assertStringContainsString("['error' => 'not_found']", $this->controller);
    }

    public function testOnlyCatalogModeBypassesClosedStorefrontGate(): void
    {
        self::assertStringContainsString("\$schema['talario_analytics']", $this->trusted_controllers);
        self::assertStringContainsString("'catalog' => true", $this->trusted_controllers);
        self::assertStringContainsString("'default_allow' => false", $this->trusted_controllers);
        self::assertStringContainsString("'areas' => ['C']", $this->trusted_controllers);
        self::assertStringNotContainsString("'allow' => true", $this->trusted_controllers);
    }

    public function testSnapshotDeclaresTruncationAndCursor(): void
    {
        self::assertStringContainsString("'truncated' =>", $this->controller);
        self::assertStringContainsString("'has_more' =>", $this->controller);
        self::assertStringContainsString("'next_product_id' =>", $this->controller);
        self::assertStringContainsString("'next_schedule_marker' =>", $this->controller);
    }

    public function testAuditLogExcludesTokenAndUsesHashedSourceIp(): void
    {
        $audit_offset = strpos($this->controller, 'Talario Partner Sync catalog request completed');
        self::assertNotFalse($audit_offset);
        $audit = substr($this->controller, $audit_offset);
        $response_offset = strpos($audit, 'fn_talario_analytics_json_response(200');
        self::assertNotFalse($response_offset);
        $audit = substr($audit, 0, $response_offset);
        self::assertStringContainsString("source_ip_hash' => hash('sha256'", $audit);
        self::assertStringNotContainsString('$provided_token', $audit);
    }

    public function testPartnerSyncVariationApplyUsesCsCartServiceAndFailsClosed(): void
    {
        self::assertStringContainsString(
            'generateProductsAndCreateGroup($request)',
            $this->write_capability
        );
        self::assertStringContainsString(
            'fn_update_product_features_value($base_product_id',
            $this->write_capability
        );
        self::assertStringContainsString(
            "'error' => 'variation_plan_not_rectangular'",
            $this->write_capability
        );
        self::assertStringContainsString(
            "'error' => 'schedule_not_representable'",
            $this->write_capability
        );
        self::assertStringContainsString(
            "'reason' => 'multiple_sessions_same_day'",
            $this->write_capability
        );
        self::assertStringContainsString(
            "'error' => 'booking_required_for_variations'",
            $this->write_capability
        );
        self::assertStringContainsString(
            "'variation_structure_change_not_supported'",
            $this->write_capability
        );
        self::assertStringContainsString(
            'fn_ec_save_booking_data_by_amount',
            $this->write_capability
        );
    }

    public function testPartnerSyncVariationApplyDoesNotCreateFeatureVariants(): void
    {
        self::assertStringNotContainsString(
            'fn_update_product_feature_variant',
            $this->write_capability
        );
        self::assertStringNotContainsString(
            "'add_new_variant' =>",
            $this->write_capability
        );
        self::assertStringContainsString(
            'fn_talario_analytics_partner_sync_resolve_variation_axis',
            $this->write_capability
        );
    }

    public function testPartnerSyncVariationApplyHasExplicitDevOnlyGate(): void
    {
        self::assertStringContainsString(
            'fn_talario_analytics_partner_sync_assert_variation_write_gate',
            $this->write_capability
        );
        self::assertStringContainsString("PHP_SAPI !== 'cli'", $this->write_capability);
        self::assertStringContainsString('TALARIO_PARTNER_SYNC_DEV_COPY', $this->write_capability);
        self::assertStringContainsString('TALARIO_PARTNER_SYNC_DEV_WRITE', $this->write_capability);
        self::assertStringContainsString(
            "['error' => 'variation_write_not_available']",
            $this->write_capability
        );
    }

    public function testPartnerSyncVariationResultRedactsInternalVariationIds(): void
    {
        $apply_offset = strpos(
            $this->write_capability,
            'function fn_talario_analytics_partner_sync_apply_variation_plan'
        );
        self::assertNotFalse($apply_offset);
        $prepare_offset = strpos(
            $this->write_capability,
            'function fn_talario_analytics_partner_sync_write_prepare_images',
            $apply_offset
        );
        self::assertNotFalse($prepare_offset);
        $apply_section = substr($this->write_capability, $apply_offset, $prepare_offset - $apply_offset);

        self::assertStringNotContainsString("'product_id' => \$variation_product_id", $apply_section);
        self::assertStringNotContainsString("'group_id' =>", $apply_section);
        self::assertStringContainsString("'count' => count(\$updated)", $apply_section);
        self::assertStringContainsString("'items' => \$updated", $apply_section);
    }

    public function testPartnerSyncCreateAttachesImagesBeforeVariationGeneration(): void
    {
        $response_offset = strpos(
            $this->write_capability,
            'function fn_talario_analytics_partner_sync_write_response'
        );
        self::assertNotFalse($response_offset);
        $response_section = substr($this->write_capability, $response_offset);

        $image_before_offset = strpos(
            $response_section,
            "if (\$operation === 'create' && \$variation_resolution !== null && \$prepared_images !== null)"
        );
        $variation_offset = strpos(
            $response_section,
            'fn_talario_analytics_partner_sync_apply_variation_plan('
        );
        self::assertNotFalse($image_before_offset);
        self::assertNotFalse($variation_offset);
        self::assertLessThan($variation_offset, $image_before_offset);

        self::assertStringContainsString(
            "'image_write_before_variations'",
            $response_section
        );
        self::assertStringContainsString(
            "&& !(\$operation === 'create' && \$variation_resolution !== null)",
            $response_section
        );

        $copy_schema = (string) file_get_contents(
            dirname(__DIR__, 2) . '/product_variations/schemas/product_variations/product_data_copy.php'
        );
        self::assertStringContainsString("'images_links'", $copy_schema);
        self::assertStringContainsString("'object_type' => 'product'", $copy_schema);
    }

    public function testPartnerSyncVariationGenerationIsNotWrappedInLongOuterTransaction(): void
    {
        $response_offset = strpos(
            $this->write_capability,
            'function fn_talario_analytics_partner_sync_write_response'
        );
        self::assertNotFalse($response_offset);
        $response_section = substr($this->write_capability, $response_offset);

        self::assertStringNotContainsString("db_query('START TRANSACTION')", $response_section);
        self::assertStringNotContainsString("db_query('COMMIT')", $response_section);
        self::assertStringNotContainsString("db_query('ROLLBACK')", $response_section);
        self::assertStringContainsString(
            'Never keep an incomplete new card',
            $response_section
        );
    }

    public function testPartnerSyncVariationFailureHasCompensatingCreateCleanup(): void
    {
        self::assertStringContainsString(
            'fn_talario_analytics_partner_sync_cleanup_failed_create',
            $this->write_capability
        );
        self::assertStringContainsString(
            '$service->removeGroup($group->getId())',
            $this->write_capability
        );
        self::assertStringContainsString(
            'fn_delete_product($product_id)',
            $this->write_capability
        );
        self::assertStringContainsString(
            "'recovery' => \$operation === 'create' ? 'compensating_cleanup' : 'rerun_same_update'",
            $this->write_capability
        );
    }

    public function testPartnerSyncVariationUsesScopedPerVariationTransactions(): void
    {
        $apply_offset = strpos(
            $this->write_capability,
            'function fn_talario_analytics_partner_sync_apply_variation_plan'
        );
        self::assertNotFalse($apply_offset);
        $prepare_offset = strpos(
            $this->write_capability,
            'function fn_talario_analytics_partner_sync_write_prepare_images',
            $apply_offset
        );
        self::assertNotFalse($prepare_offset);
        $apply_section = substr($this->write_capability, $apply_offset, $prepare_offset - $apply_offset);

        self::assertStringContainsString("db_query('START TRANSACTION')", $apply_section);
        self::assertStringContainsString("db_query('COMMIT')", $apply_section);
        self::assertStringContainsString("db_query('ROLLBACK')", $apply_section);
        self::assertStringContainsString(
            'Keep the atomic scope narrow: one variation price + booking + capacity.',
            $apply_section
        );
    }


    public function testSignedPartnerApplyDispatchesBeforeBearerAuthentication(): void
    {
        $signed_dispatch = strpos($this->controller, "if (\$mode === 'partner_apply') {");
        $bearer_auth = strpos($this->controller, '$provided_token = fn_talario_analytics_bearer_token();');

        self::assertNotFalse($signed_dispatch);
        self::assertNotFalse($bearer_auth);
        self::assertLessThan($bearer_auth, $signed_dispatch);
        self::assertStringContainsString("fn_talario_analytics_partner_sync_verify_penaty_signature('apply', \$raw);", $this->controller);
    }
    public function testSignedPartnerApplyBootstrapsOnlyApprovedAgeTaxonomyAfterSignature(): void
    {
        $bootstrap_offset = strpos(
            $this->controller,
            'function fn_talario_analytics_partner_sync_bootstrap_approved_age_variants'
        );
        self::assertNotFalse($bootstrap_offset);
        $bootstrap_section = substr($this->controller, $bootstrap_offset, 5200);

        self::assertStringContainsString("['до 3 лет', '3-5 лет', '6-9 лет']", $bootstrap_section);
        self::assertStringContainsString("'age_variant_bootstrap_not_allowed'", $bootstrap_section);
        self::assertStringContainsString("'age_variant_bootstrap_scope_mismatch'", $bootstrap_section);
        self::assertStringContainsString("'Возраст'", $bootstrap_section);
        self::assertStringContainsString("'group_variation_catalog_item'", $bootstrap_section);
        self::assertStringContainsString('fn_update_product_feature_variant(', $bootstrap_section);

        $verify = strpos(
            $this->controller,
            "fn_talario_analytics_partner_sync_verify_penaty_signature('apply', \$raw);"
        );
        $bootstrap_call = strpos(
            $this->controller,
            'fn_talario_analytics_partner_sync_bootstrap_approved_age_variants($payload);',
            $verify
        );
        self::assertNotFalse($verify);
        self::assertNotFalse($bootstrap_call);
        self::assertGreaterThan($verify, $bootstrap_call);
    }

    public function testVariationWritePerformsDatabaseReadbackForPriceScheduleAndCapacity(): void
    {
        self::assertStringContainsString(
            'fn_talario_analytics_partner_sync_readback_variation_state',
            $this->write_capability
        );
        self::assertStringContainsString(
            "'variation_readback_mismatch'",
            $this->write_capability
        );
        self::assertStringContainsString(
            'fn_talario_analytics_partner_sync_write_readback($product_id)',
            $this->write_capability
        );
        self::assertStringContainsString(
            'SELECT days_data FROM ?:ec_table_booking_system WHERE product_id = ?i',
            $this->write_capability
        );
        self::assertStringContainsString(
            'time_by_amount',
            $this->write_capability
        );
        self::assertStringContainsString(
            "'schedule' => \$variation_readback['schedule']",
            $this->write_capability
        );
    }



    public function testPartnerSyncWriteFailureResponseIsBoundedAndStageAware(): void
    {
        self::assertStringContainsString(
            'function fn_talario_analytics_partner_sync_safe_write_error_kind',
            $this->write_capability
        );
        foreach (['image_prepare', 'base_product_write', 'popularity_write', 'filter_feature_write', 'variation_write', 'filter_feature_variation_readback', 'image_write', 'success_cleanup'] as $stage) {
            self::assertStringContainsString("'" . $stage . "'", $this->write_capability);
        }
        self::assertStringContainsString("'stage' => $failed_stage", $this->write_capability);
        self::assertStringContainsString("'kind' => $safe_error_kind", $this->write_capability);
        self::assertStringNotContainsString(
            "'message' => $exception->getMessage()",
            $this->write_capability
        );
    }


    public function testVariationCapacityReadbackUsesBoundedStructuredUnserialize(): void
    {
        $offset = strpos(
            $this->write_capability,
            'function fn_talario_analytics_partner_sync_readback_serialized_capacity'
        );
        self::assertNotFalse($offset);
        $next = strpos(
            $this->write_capability,
            'function fn_talario_analytics_partner_sync_readback_variation_state',
            $offset
        );
        self::assertNotFalse($next);
        $section = substr($this->write_capability, $offset, $next - $offset);

        self::assertStringContainsString('strlen($serialized) > 16384', $section);
        self::assertStringContainsString(
            "@unserialize(\$serialized, ['allowed_classes' => false, 'max_depth' => 8])",
            $section
        );
        self::assertStringContainsString("\$day_data['time_by_amount']", $section);
        self::assertStringContainsString('$match_count === 1', $section);
        self::assertStringNotContainsString('preg_match_all(', $section);

        foreach ([
            'variation_readback_price_mismatch',
            'variation_readback_booking_mismatch',
            'variation_readback_status_mismatch',
            'variation_readback_time_mismatch',
            'variation_readback_capacity_mismatch',
            'variation_readback_schedule_count_mismatch',
        ] as $detail) {
            self::assertStringContainsString("'".$detail."'", $this->write_capability);
        }
    }



    public function testPartnerSyncPersistsDedicatedLessonAddressAndReadback(): void
    {
        self::assertStringContainsString(
            "array_key_exists('address', \$product)",
            $this->write_capability
        );
        self::assertStringContainsString(
            "mb_strlen(\$address, 'UTF-8') > 255",
            $this->write_capability
        );
        self::assertStringContainsString(
            "\$data['address'] = \$address;",
            $this->write_capability
        );
        self::assertStringContainsString(
            'pd.full_description, pd.address',
            $this->write_capability
        );
        self::assertStringContainsString(
            "'address' => (string) \$row['address']",
            $this->write_capability
        );

        $design_addon = (string) file_get_contents(
            dirname(__DIR__, 2) . '/sd_design_changes/addon.xml'
        );
        self::assertStringContainsString(
            'ALTER TABLE ?:product_descriptions ADD address varchar(255)',
            $design_addon
        );
    }

}


