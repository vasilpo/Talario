<?php

declare(strict_types=1);

namespace Tygh\Tests\Unit\Addons\TalarioAnalytics;

use PHPUnit\Framework\TestCase;

final class PartnerSyncReadApiContractTest extends TestCase
{
    private string $controller;
    private string $settings_actions;

    protected function setUp(): void
    {
        $controller_path = dirname(__DIR__) . '/controllers/frontend/talario_analytics.php';
        $settings_path = dirname(__DIR__) . '/schemas/settings/actions.functions.php';
        $this->controller = (string) file_get_contents($controller_path);
        $this->settings_actions = (string) file_get_contents($settings_path);
    }

    public function testPartnerSyncUsesDedicatedTokenAndCannotAuthorizeOrders(): void
    {
        self::assertStringContainsString("'partner_sync_token'", $this->controller);
        self::assertStringContainsString('$token_setting = $mode === \'catalog\' ? \'partner_sync_token\' : \'api_token\';', $this->controller);
        self::assertStringContainsString('hash_equals($stored_token_hash, $provided_hash)', $this->controller);
        self::assertStringContainsString('fn_talario_analytics_token_is_distinct', $this->settings_actions);
    }

    public function testAnalyticsTokenCannotAuthorizeCatalog(): void
    {
        self::assertStringContainsString('if ($mode === \'catalog\')', $this->controller);
        self::assertStringContainsString("Registry::get('addons.talario_analytics.' . $token_setting)", $this->controller);
        self::assertStringContainsString("partner_sync_api_not_configured", $this->controller);
    }

    public function testPartnerScopeConstrainsProductsVariationsResourcesAndSchedule(): void
    {
        self::assertStringContainsString("p.company_id = ?i", $this->controller);
        self::assertStringContainsString("p.product_id > ?i", $this->controller);
        self::assertStringContainsString("rp.product_id IN (?n)", $this->controller);
        self::assertStringContainsString("rp_scope.product_id IN (?n)", $this->controller);
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

    public function testCatalogIsDevelopmentCopyOnly(): void
    {
        self::assertStringContainsString("fn_is_development()", $this->controller);
        self::assertStringContainsString('TALARIO_PARTNER_SYNC_DEV_COPY', $this->controller);
        self::assertStringContainsString("['error' => 'not_found']", $this->controller);
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
