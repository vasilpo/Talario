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

    public function testSignedDispatcherInstallIsDevOnlyDualAuthenticatedAndFixed(): void
    {
        self::assertStringContainsString("'dispatcher_install'", $this->controller);
        self::assertStringContainsString("'dispatcher_install' => true", $this->trusted_controllers);
        self::assertStringContainsString("in_array(\$mode, ['catalog_variant_bootstrap', 'dispatcher_install'], true)", $this->controller);
        self::assertStringContainsString("['catalog', 'catalog_variant_bootstrap', 'dispatcher_status', 'dispatcher_install']", $this->controller);
        self::assertStringContainsString("'part-sync-dispatcher-install-20260924'", $this->controller);
        self::assertStringContainsString("'HTTP_X_TALARIO_SIGNATURE'", $this->controller);
        self::assertStringContainsString("'github-actions-talario'", $this->controller);
        self::assertStringContainsString("'talario-part-sync'", $this->controller);
        self::assertStringContainsString("'/usr/bin/ssh-keygen'", $this->controller);
        self::assertStringContainsString("'/usr/bin/bash'", $this->controller);
        self::assertStringContainsString("'/ops/beget/install-reviewed-dispatcher.sh'", $this->controller);
        self::assertStringContainsString("'b1641564b30833fcd211f394b227bd9115c84413'", $this->controller);
        self::assertStringContainsString("sha1('blob ' . strlen($installer_source)", $this->controller);
        self::assertStringContainsString('register_shutdown_function($cleanup);', $this->controller);
        self::assertStringContainsString("'DISPATCHER_INSTALL=PASS'", $this->controller);
        self::assertStringNotContainsString('shell_exec(', $this->controller);
        self::assertStringNotContainsString('system(', $this->controller);
        self::assertStringNotContainsString('exec(', $this->controller);
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
        self::assertStringContainsString('fn_talario_analytics_partner_sync_write_response();', $this->cli_runner);
        self::assertStringContainsString("['error' => 'partner_sync_write_disabled']", $this->write_capability);
        self::assertStringContainsString("['error' => 'approval_id_required']", $this->write_capability);
        self::assertStringContainsString("'approval_id_hash' => \$approval_id_hash", $this->write_capability);
        self::assertStringNotContainsString("'approval_id' => \$approval_id", $this->write_capability);
        self::assertStringContainsString("'dry_run' => true", $this->write_capability);
    }

    public function testPartnerSyncWriteUsesCoreProductAndEcarterHooks(): void
    {
        self::assertStringContainsString('fn_update_product(', $this->write_capability);
        self::assertStringContainsString("\$product_data['booking_data'] = \$booking_data", $this->write_capability);
        self::assertStringContainsString('?:ec_table_booking_system', $this->write_capability);
        self::assertStringContainsString("'schema_version' => 'partner-sync.write-result.v1'", $this->write_capability);
    }

    public function testPartnerSyncCreateDefaultsToHidden(): void
    {
        self::assertStringContainsString("\$data['status'] = 'H';", $this->write_capability);
        self::assertStringContainsString('New Partner Sync cards are hidden by default', $this->write_capability);
    }

    public function testPartnerSyncWriteSupportsBoundedPrivateImageImport(): void
    {
        self::assertStringContainsString('content_base64', $this->write_capability);
        self::assertStringContainsString('getimagesizefromstring', $this->write_capability);
        self::assertStringContainsString("['image/jpeg', 'image/png', 'image/webp']", $this->write_capability);
        self::assertStringContainsString('fn_create_temp_file()', $this->write_capability);
        self::assertStringContainsString('@chmod($tmp, 0600);', $this->write_capability);
        self::assertStringContainsString("fn_attach_image_pairs", (string) file_get_contents(dirname(__DIR__, 3) . '/functions/fn.products.php'));
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

    public function testPartnerSyncVariationLabelMatchingNormalizesEquivalentCommercialLabels(): void
    {
        self::assertStringContainsString('fn_talario_analytics_partner_sync_variation_label_key', $this->write_capability);
        self::assertStringContainsString("['Занятия', 'Занятие']", $this->write_capability);
        self::assertStringContainsString("абонемент\\\\s+на\\\\s+", $this->write_capability);
        self::assertStringContainsString("абонемент $1 занятий", $this->write_capability);
        self::assertStringContainsString("preg_replace('/(?<=\\\\d)\\\\s*лет/u'", $this->write_capability);
        self::assertStringNotContainsString('fn_update_product_feature_variant(', $this->write_capability);
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

}


