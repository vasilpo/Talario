<?php

declare(strict_types=1);

namespace Tygh\Tests\Unit\Addons\TalarioAnalytics;

use PHPUnit\Framework\TestCase;

final class PartnerSyncReadApiContractTest extends TestCase
{
    private string $controller;
    private string $addon_xml;

    protected function setUp(): void
    {
        $controller_path = dirname(__DIR__) . '/controllers/frontend/talario_analytics.php';
        $addon_path = dirname(__DIR__) . '/addon.xml';
        $this->controller = (string) file_get_contents($controller_path);
        $this->addon_xml = (string) file_get_contents($addon_path);
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

    public function testOrdersUseProductionHashOverrideWithSettingFallback(): void
    {
        self::assertStringContainsString("defined('TALARIO_ANALYTICS_TOKEN_HASH')", $this->controller);
        self::assertStringContainsString('trim((string) TALARIO_ANALYTICS_TOKEN_HASH)', $this->controller);
        self::assertStringContainsString("Registry::get('addons.talario_analytics.api_token')", $this->controller);
        self::assertStringContainsString('analytics_api_not_configured', $this->controller);
    }


    public function testOrdersExposeOnlyAggregateLegacyBookingCounts(): void
    {
        self::assertStringContainsString(
            'SELECT order_id, COUNT(*) AS booking_count FROM ?:ec_table_booking_system_booking_info',
            $this->controller
        );
        self::assertStringContainsString("'legacy_booking_count' =>", $this->controller);
        self::assertStringNotContainsString("'booking_info' =>", $this->controller);
        self::assertStringNotContainsString("'start_date' =>", $this->controller);
        self::assertStringNotContainsString("'slot' =>", $this->controller);
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

    public function testLegacyScheduleIgnoresEcarterPerWeekdaySlotMetadata(): void
    {
        self::assertStringContainsString("days_data[weekday]['time_by_amount']", $this->controller);
        self::assertStringContainsString(
            "preg_match('/^(monday|tuesday|wednesday|thursday|friday|saturday|sunday)$/', \$key)",
            $this->controller
        );
        self::assertStringContainsString('&& is_array($value)', $this->controller);
    }

    public function testPartnerScopeConstrainsProductsVariationsResourcesAndSchedule(): void
    {
        self::assertStringContainsString("p.company_id = ?i", $this->controller);
        self::assertStringContainsString("p.product_id > ?i", $this->controller);
        self::assertStringContainsString("rp.product_id IN (?n)", $this->controller);
        self::assertStringContainsString("rp_scope.product_id IN (?n)", $this->controller);
        self::assertStringNotContainsString("l.address AS location_address", $this->controller);
    }

    public function testMissingResourceTablesFallBackToLegacySchedule(): void
    {
        self::assertStringContainsString("function fn_talario_analytics_resource_tables_available", $this->controller);
        self::assertStringContainsString("SHOW TABLES LIKE '?:?p'", $this->controller);
        self::assertStringContainsString("if ($resource_tables_available)", $this->controller);
        self::assertStringContainsString("if (fn_talario_analytics_resource_tables_available())", $this->controller);
    }

    public function testResourceScheduleIsPreferredAndLegacyIsFallback(): void
    {
        self::assertStringContainsString("talario_resource_occurrences", $this->controller);
        self::assertStringContainsString("NOT EXISTS (SELECT 1 FROM ?:talario_resource_products rp", $this->controller);
        self::assertStringContainsString("'source' => 'legacy_ecarter'", $this->controller);
        self::assertStringContainsString('array_merge($schedule, $legacy_schedule)', $this->controller);
    }

    public function testWriteMethodsAreRejected(): void
    {
        self::assertStringContainsString("REQUEST_METHOD'] !== 'GET'", $this->controller);
        self::assertStringContainsString("['error' => 'method_not_allowed']", $this->controller);
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

    public function testProductionCatalogHasTightResourceLimits(): void
    {
        self::assertStringContainsString("partner_sync_catalog:global", $this->controller);
        self::assertStringContainsString("partner_sync_catalog:credential:", $this->controller);
        self::assertStringContainsString("fn_talario_analytics_catalog_rate_limit($provided_hash)", $this->controller);
        self::assertStringContainsString("max_limit' => 100", $this->controller);
        self::assertStringContainsString("LIMIT 501", $this->controller);
        self::assertStringContainsString("array_slice($schedule, 0, 500)", $this->controller);
        self::assertStringContainsString("strlen($serialized_days_data) > 8192", $this->controller);
        self::assertStringContainsString("'max_depth' => 8", $this->controller);
    }

    public function testSnapshotDeclaresTruncationAndCursor(): void
    {
        self::assertStringContainsString("'truncated' =>", $this->controller);
        self::assertStringContainsString("'has_more' =>", $this->controller);
        self::assertStringContainsString("'next_product_id' =>", $this->controller);
        self::assertStringContainsString("'next_schedule_marker' =>", $this->controller);
    }

    public function testRateLimitStorageIsDeclaredByAddonInstallSchema(): void
    {
        self::assertStringContainsString('CREATE TABLE `?:talario_analytics_rate_limits`', $this->addon_xml);
        self::assertStringContainsString('PRIMARY KEY (`scope_hash`, `minute_bucket`)', $this->addon_xml);
    }

    public function testAuditLogExcludesCredentialsAndSourceIp(): void
    {
        $audit_offset = strpos($this->controller, 'Talario Partner Sync catalog request completed');
        self::assertNotFalse($audit_offset);
        $audit = substr($this->controller, $audit_offset);
        $response_offset = strpos($audit, 'fn_talario_analytics_json_response(200');
        self::assertNotFalse($response_offset);
        $audit = substr($audit, 0, $response_offset);
        self::assertStringNotContainsString('source_ip_hash', $audit);
        self::assertStringNotContainsString('$provided_token', $audit);
        self::assertStringNotContainsString('$provided_hash', $audit);
    }
}
