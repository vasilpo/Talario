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

    protected function setUp(): void
    {
        $controller_path = dirname(__DIR__) . '/controllers/frontend/talario_analytics.php';
        $addon_path = dirname(__DIR__) . '/addon.xml';
        $trusted_controllers_path = dirname(__DIR__) . '/schemas/permissions/trusted_controllers.post.php';
        $this->controller = (string) file_get_contents($controller_path);
        $this->addon_xml = (string) file_get_contents($addon_path);
        $this->trusted_controllers = (string) file_get_contents($trusted_controllers_path);
        $this->write_capability = (string) file_get_contents(dirname(__DIR__) . '/partner_sync_write.php');
    }

    public function testPartnerSyncUsesDedicatedServerConfigToken(): void
    {
        self::assertStringContainsString('TALARIO_PARTNER_SYNC_TOKEN_HASH', $this->controller);
        self::assertStringContainsString('partner_sync_api_not_configured', $this->controller);
        self::assertStringContainsString('partner_sync_api_misconfigured', $this->controller);
        self::assertStringContainsString("!preg_match('/^sha256:[a-f0-9]{64}$/', \$analytics_token_hash)", $this->controller);
        self::assertStringContainsString("\$analytics_token_hash = 'sha256:' . hash('sha256', \$analytics_token_hash);", $this->controller);
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

    public function testPartnerSyncWriteIsPostOnlyAndDevCopyOnly(): void
    {
        self::assertStringContainsString("$partner_sync_write_mode = $mode === 'catalog_apply'", $this->controller);
        self::assertStringContainsString("REQUEST_METHOD'] !== 'POST'", $this->controller);
        self::assertStringContainsString("if ($mode === 'catalog_apply' && !$dev_copy_enabled)", $this->controller);
        self::assertStringNotContainsString('TALARIO_PARTNER_SYNC_PROD_WRITE', $this->controller);
        self::assertStringContainsString("'catalog_apply' => true", $this->trusted_controllers);
    }

    public function testPartnerSyncWriteRequiresSeparateDevWriteGateAndApproval(): void
    {
        self::assertStringContainsString('TALARIO_PARTNER_SYNC_DEV_WRITE', $this->write_capability);
        self::assertStringContainsString("['error' => 'partner_sync_write_disabled']", $this->write_capability);
        self::assertStringContainsString("['error' => 'approval_id_required']", $this->write_capability);
        self::assertStringContainsString("'dry_run' => true", $this->write_capability);
    }

    public function testPartnerSyncWriteUsesCoreProductAndEcarterHooks(): void
    {
        self::assertStringContainsString('fn_update_product(', $this->write_capability);
        self::assertStringContainsString("$product_data['booking_data'] = $booking_data", $this->write_capability);
        self::assertStringContainsString('?:ec_table_booking_system', $this->write_capability);
        self::assertStringContainsString("'schema_version' => 'partner-sync.write-result.v1'", $this->write_capability);
    }

    public function testPartnerSyncCreateDefaultsToHidden(): void
    {
        self::assertStringContainsString("$data['status'] = 'H';", $this->write_capability);
        self::assertStringContainsString('New Partner Sync cards are hidden by default', $this->write_capability);
    }

    public function testPartnerSyncWriteSupportsBoundedPrivateImageImport(): void
    {
        self::assertStringContainsString('content_base64', $this->write_capability);
        self::assertStringContainsString('getimagesizefromstring', $this->write_capability);
        self::assertStringContainsString("['image/jpeg', 'image/png', 'image/webp']", $this->write_capability);
        self::assertStringContainsString('fn_create_temp_file()', $this->write_capability);
        self::assertStringContainsString("fn_attach_image_pairs", (string) file_get_contents(dirname(dirname(dirname(dirname(__DIR__)))) . '/functions/fn.products.php'));
        self::assertStringContainsString("'images' => [", $this->write_capability);
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
