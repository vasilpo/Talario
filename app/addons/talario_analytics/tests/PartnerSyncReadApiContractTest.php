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

    public function testPartnerSyncWriteIsInternalCliOnly(): void
    {
        self::assertStringContainsString("PHP_SAPI !== 'cli'", $this->cli_runner);
        self::assertStringContainsString("'/talario.ru/dev_copy'", $this->cli_runner);
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
        self::assertStringContainsString('EXPECTED_RUNNER_SHA256="dd94ab1a5f5c9774511408cdf8d54a406643848b351d2666d8f8092ff23f584d"', $this->dev_dispatcher);
        self::assertStringContainsString('partner sync CLI runner is not allowlisted', $this->dev_dispatcher);
        self::assertStringContainsString('/usr/bin/timeout --signal=TERM --kill-after=5s 60s', $this->dev_dispatcher);
        self::assertStringContainsString('/usr/bin/env -i HOME="$HOME" PATH="/usr/bin:/bin"', $this->dev_dispatcher);
        self::assertStringContainsString('partner sync dry-run execution timeout', $this->dev_dispatcher);
        self::assertStringNotContainsString('talario-partner-sync-apply', $this->dev_dispatcher);
        self::assertStringContainsString('/usr/bin/timeout 30s /usr/bin/head -c 20971521', $this->dev_dispatcher);
    }

    public function testDevDispatcherDoesNotExposeGenericShellForPartnerSync(): void
    {
        self::assertStringNotContainsString('eval ', $this->dev_dispatcher);
        self::assertStringNotContainsString('bash -c "$REQUEST"', $this->dev_dispatcher);
        self::assertStringContainsString('fail "SSH command is not allowlisted"', $this->dev_dispatcher);
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
        self::assertStringContainsString("count(\$payload['variation_plan']) > 64", $this->write_capability);
        self::assertStringContainsString("'error' => 'duplicate_variation_item'", $this->write_capability);
        self::assertStringContainsString("'error' => 'invalid_variation_schedule_item'", $this->write_capability);
        self::assertStringContainsString("'error' => 'variation_schedule_total_limit'", $this->write_capability);
        self::assertStringContainsString("'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'", $this->write_capability);
        self::assertStringContainsString("\$duration > 1440", $this->write_capability);
    }

    public function testPartnerSyncCreateUsesProductVariationsServiceAndExactEcarterSlots(): void
    {
        self::assertStringContainsString(
            '\\Tygh\\Addons\\ProductVariations\\ServiceProvider::getService()',
            $this->write_capability
        );
        self::assertStringContainsString(
            'GenerateProductsAndCreateGroupRequest',
            $this->write_capability
        );
        self::assertStringContainsString(
            'GroupFeatureCollection::createFromFeatureList',
            $this->write_capability
        );
        self::assertStringContainsString('generateProductsAndCreateGroup', $this->write_capability);
        self::assertStringContainsString('fn_update_product(', $this->write_capability);
        self::assertStringContainsString('fn_ec_save_booking_data_by_amount', $this->write_capability);
        self::assertStringContainsString("'error' => 'variation_update_not_implemented'", $this->write_capability);
    }

    public function testPartnerSyncCrossAddonVariationPhaseRunsAfterBaseCommit(): void
    {
        $response_offset = strpos(
            $this->write_capability,
            'function fn_talario_analytics_partner_sync_write_response'
        );
        self::assertNotFalse($response_offset);
        $response = substr($this->write_capability, $response_offset);

        $commit_offset = strpos($response, "db_query('COMMIT')");
        $variation_offset = strpos(
            $response,
            'fn_talario_analytics_partner_sync_apply_create_variations('
        );
        self::assertNotFalse($commit_offset);
        self::assertNotFalse($variation_offset);
        self::assertLessThan($variation_offset, $commit_offset);

        self::assertStringContainsString(
            'fn_talario_analytics_partner_sync_cleanup_created_variations',
            $this->write_capability
        );
        self::assertStringContainsString(
            'fn_talario_analytics_partner_sync_validate_resolved_variants',
            $this->write_capability
        );
        self::assertStringContainsString(
            "throw new RuntimeException('variation_variant_membership_invalid')",
            $this->write_capability
        );
    }

    public function testPartnerSyncVariationRollbackCleansEcarterProductState(): void
    {
        self::assertStringContainsString(
            'fn_talario_analytics_partner_sync_cleanup_ecarter_product',
            $this->write_capability
        );
        self::assertStringContainsString(
            'DELETE FROM ?:ec_table_booking_system_price WHERE product_id = ?i',
            $this->write_capability
        );
        self::assertStringContainsString(
            'DELETE FROM ?:ec_table_booking_system_booking_info WHERE product_id = ?i',
            $this->write_capability
        );
        self::assertStringContainsString(
            'DELETE FROM ?:ec_table_booking_system WHERE product_id = ?i',
            $this->write_capability
        );
        self::assertStringContainsString(
            'cleanup_status' => 'ecarter_not_clean',
            $this->write_capability
        );
        self::assertStringContainsString('for ($attempt = 1; $attempt <= 3; $attempt++)', $this->write_capability);
        self::assertStringContainsString(
            'SELECT product_id FROM ?:products WHERE product_id = ?i',
            $this->write_capability
        );

        $cleanup_offset = strpos(
            $this->write_capability,
            'function fn_talario_analytics_partner_sync_cleanup_created_variations'
        );
        self::assertNotFalse($cleanup_offset);
        $cleanup = substr($this->write_capability, $cleanup_offset, 3600);
        $ecarter_offset = strpos($cleanup, 'fn_talario_analytics_partner_sync_cleanup_ecarter_product');
        $delete_offset = strpos($cleanup, 'fn_delete_product(');
        self::assertNotFalse($ecarter_offset);
        self::assertNotFalse($delete_offset);
        self::assertLessThan($delete_offset, $ecarter_offset);
    }

    public function testPartnerSyncAuditLogDoesNotPersistVariationIds(): void
    {
        $log_offset = strrpos($this->write_capability, "fn_log_event('general', 'runtime'");
        self::assertNotFalse($log_offset);
        $log_section = substr($this->write_capability, $log_offset, 1400);
        self::assertStringContainsString("'variation_count' =>", $log_section);
        self::assertStringNotContainsString("'variations' => $variation_apply", $log_section);
    }

    public function testPartnerSyncVariationApplyDoesNotDirectlyMutateVariationMetadataTables(): void
    {
        self::assertStringNotContainsString('INSERT INTO ?:product_variation', $this->write_capability);
        self::assertStringNotContainsString('UPDATE ?:product_variation', $this->write_capability);
        self::assertStringNotContainsString('INSERT INTO ?:product_feature_variants', $this->write_capability);
        self::assertStringNotContainsString('fn_update_product_feature_variant(', $this->write_capability);
    }

    public function testPartnerSyncVariationMatrixAndResultCountAreBounded(): void
    {
        self::assertStringContainsString("'error' => 'variation_plan_must_be_full_matrix'", $this->write_capability);
        self::assertStringContainsString('$expected_combinations > 64', $this->write_capability);
        self::assertStringContainsString("throw new RuntimeException('variation_product_limit_exceeded')", $this->write_capability);
        self::assertStringContainsString("throw new RuntimeException('invalid_variation_schedule_day')", $this->write_capability);
    }

    public function testPartnerSyncVariationCapacityIsRequiredForApply(): void
    {
        self::assertStringContainsString("'error' => 'invalid_variation_capacity'", $this->write_capability);
        self::assertStringContainsString("throw new RuntimeException('variation_capacity_required')", $this->write_capability);
        self::assertStringContainsString("'capacities_complete' =>", $this->write_capability);
        self::assertStringContainsString('(int) $capacity > 500', $this->write_capability);
        self::assertStringContainsString("'prices_complete' =>", $this->write_capability);
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
}
