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
        self::assertStringContainsString('/usr/local/bin/php8.2 "$RUNNER_PATH"', $this->dev_dispatcher);
        self::assertStringContainsString('20971520', $this->dev_dispatcher);
        self::assertStringContainsString('DRY_RUN_REQUIRED', $this->dev_dispatcher);
        self::assertStringContainsString('PAYLOAD_READ_FAILED', $this->dev_dispatcher);
        self::assertStringNotContainsString('PATH="/usr/local/bin:/usr/bin:/bin"', $this->dev_dispatcher);
        self::assertStringContainsString('git -C "$DEV_COPY" status --porcelain --untracked-files=all', $this->dev_dispatcher);
        self::assertStringContainsString('git -C "$DEV_COPY" show "HEAD:$RUNNER_REL"', $this->dev_dispatcher);
        self::assertStringContainsString('dev_copy worktree must be clean for partner sync', $this->dev_dispatcher);
        self::assertStringContainsString('partner sync CLI runner integrity check failed', $this->dev_dispatcher);
        self::assertStringNotContainsString('talario-partner-sync-apply', $this->dev_dispatcher);
        self::assertStringContainsString('/usr/bin/timeout 30s /usr/bin/head -c 20971521', $this->dev_dispatcher);
    }

    public function testDevDispatcherDoesNotExposeGenericShellForPartnerSync(): void
    {
        self::assertStringNotContainsString('eval ', $this->dev_dispatcher);
        self::assertStringNotContainsString('bash -c "$REQUEST"', $this->dev_dispatcher);
        self::assertStringContainsString('fail "SSH command is not allowlisted"', $this->dev_dispatcher);
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
