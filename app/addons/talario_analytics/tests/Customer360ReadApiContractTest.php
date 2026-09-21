<?php

declare(strict_types=1);

namespace Tygh\Tests\Unit\Addons\TalarioAnalytics;

use PHPUnit\Framework\TestCase;

final class Customer360ReadApiContractTest extends TestCase
{
    private string $controller;
    private string $trusted_controllers;

    protected function setUp(): void
    {
        $this->controller = (string) file_get_contents(
            dirname(__DIR__) . '/controllers/frontend/talario_analytics.php'
        );
        $this->trusted_controllers = (string) file_get_contents(
            dirname(__DIR__) . '/schemas/permissions/trusted_controllers.post.php'
        );
    }

    public function testCustomer360HasDedicatedCredentialAndEnvironmentGate(): void
    {
        self::assertStringContainsString('TALARIO_CRM_TOKEN_HASH', $this->controller);
        self::assertStringContainsString('TALARIO_CRM_DEV_COPY', $this->controller);
        self::assertStringContainsString('TALARIO_CRM_PROD_READ', $this->controller);
        self::assertStringContainsString('crm_api_not_configured', $this->controller);
        self::assertStringContainsString('crm_api_misconfigured', $this->controller);
        self::assertStringContainsString("'customer360' => true", $this->trusted_controllers);
    }

    public function testCustomer360IsReadOnlyAndBounded(): void
    {
        self::assertStringContainsString("['orders', 'catalog', 'customer360']", $this->controller);
        self::assertStringContainsString("'max_limit' => 100", $this->controller);
        self::assertStringContainsString("'next_user_id' =>", $this->controller);
        self::assertStringContainsString("'has_more' =>", $this->controller);
        self::assertStringContainsString("if (\$_SERVER['REQUEST_METHOD'] !== 'GET')", $this->controller);
    }

    public function testCustomer360ContainsRequiredCrmSources(): void
    {
        self::assertStringContainsString('?:users', $this->controller);
        self::assertStringContainsString("type = ?s", $this->controller);
        self::assertStringContainsString('?:orders', $this->controller);
        self::assertStringContainsString('?:order_details', $this->controller);
        self::assertStringContainsString('?:user_session_products', $this->controller);
        self::assertStringContainsString('?:subscribers', $this->controller);
        self::assertStringContainsString('?:user_mailing_lists', $this->controller);
        self::assertStringContainsString('?:mailing_lists', $this->controller);
        self::assertStringContainsString('talario_resource_bookings', $this->controller);
        self::assertStringContainsString('ec_table_booking_system_booking_info', $this->controller);
    }

    public function testCustomer360ExplicitlyExcludesUnsupportedBehavioralViews(): void
    {
        self::assertStringContainsString("'product_views' => false", $this->controller);
        self::assertStringContainsString("'schema_version' => 'crm.customer360.v1'", $this->controller);
    }

    public function testCustomer360AuditLogDoesNotContainEmailOrBearerToken(): void
    {
        $offset = strpos($this->controller, 'Talario CRM customer360 request completed');
        self::assertNotFalse($offset);
        $audit = substr($this->controller, (int) $offset);
        $response_offset = strpos($audit, 'fn_talario_analytics_json_response(200');
        self::assertNotFalse($response_offset);
        $audit = substr($audit, 0, (int) $response_offset);
        self::assertStringContainsString("source_ip_hash' => hash('sha256'", $audit);
        self::assertStringNotContainsString('$provided_token', $audit);
        self::assertStringNotContainsString("'email' =>", $audit);
    }
}
