<?php

declare(strict_types=1);

namespace Tygh\Tests\Unit\Addons\TalarioAnalytics;

use PHPUnit\Framework\TestCase;

final class CrmReadApiContractTest extends TestCase
{
    private string $controller;
    private string $crm_read;
    private string $trusted_controllers;

    protected function setUp(): void
    {
        $base = dirname(__DIR__);
        $this->controller = (string) file_get_contents($base . '/controllers/frontend/talario_analytics.php');
        $this->crm_read = (string) file_get_contents($base . '/crm_read.php');
        $this->trusted_controllers = (string) file_get_contents($base . '/schemas/permissions/trusted_controllers.post.php');
    }

    public function testCrmModeUsesDedicatedCredentialAndExplicitGates(): void
    {
        self::assertStringContainsString('TALARIO_CRM_TOKEN_HASH', $this->controller);
        self::assertStringContainsString('TALARIO_CRM_DEV_COPY', $this->controller);
        self::assertStringContainsString('TALARIO_CRM_PROD_READ', $this->controller);
        self::assertStringContainsString('crm_api_not_configured', $this->controller);
        self::assertStringContainsString('crm_api_misconfigured', $this->controller);
        self::assertStringContainsString('fn_talario_analytics_canonical_token_hash', $this->controller);
        self::assertStringContainsString('$provided_credential_hash', $this->controller);
        self::assertStringContainsString("'crm' => true", $this->trusted_controllers);
    }

    public function testCrmEndpointIsReadOnlyAndBounded(): void
    {
        self::assertStringContainsString("if (\$_SERVER['REQUEST_METHOD'] !== 'GET')", $this->controller);
        self::assertStringContainsString("'max_limit' => 100", $this->crm_read);
        self::assertStringContainsString('LIMIT 2000', $this->crm_read);
        self::assertStringNotContainsString('db_query(', substr($this->crm_read, strpos($this->crm_read, 'fn_talario_analytics_crm_response')));
    }

    public function testCrmEndpointReturnsOnlyRequiredCustomerPii(): void
    {
        self::assertStringContainsString("'email' =>", $this->crm_read);
        self::assertStringContainsString("'firstname' =>", $this->crm_read);
        self::assertStringContainsString("'lastname' =>", $this->crm_read);
        self::assertStringContainsString("'phone' =>", $this->crm_read);
        self::assertStringContainsString('TALARIO_CRM_PHONE_READ', $this->crm_read);
        self::assertStringContainsString('TALARIO_CRM_PHONE_TOKEN_HASH', $this->crm_read);
        self::assertStringContainsString('HTTP_X_TALARIO_CRM_PHONE_TOKEN', $this->crm_read);
        self::assertStringContainsString("'crm_phone_read_disabled'", $this->crm_read);
        self::assertStringContainsString("'crm_phone_api_not_configured'", $this->crm_read);
        self::assertStringContainsString("'crm_phone_unauthorized'", $this->crm_read);
        self::assertStringContainsString("(\$include_phone ? ' u.phone,' : '')", $this->crm_read);
        self::assertStringContainsString("'phone_included' => \$include_phone", $this->crm_read);
        self::assertStringNotContainsString("\$_GET['include_phone']", $this->crm_read);
        self::assertStringNotContainsString("'address' =>", $this->crm_read);
        self::assertStringNotContainsString('payment', strtolower($this->crm_read));
    }

    public function testCrmCustomer360SourcesAreExplicit(): void
    {
        self::assertStringContainsString('?:users', $this->crm_read);
        self::assertStringContainsString("ud.type = 'W'", $this->crm_read);
        self::assertStringContainsString('?:orders', $this->crm_read);
        self::assertStringContainsString('?:order_details', $this->crm_read);
        self::assertStringContainsString('?:user_session_products', $this->crm_read);
        self::assertStringContainsString('?:subscribers', $this->crm_read);
        self::assertStringContainsString('?:user_mailing_lists', $this->crm_read);
        self::assertStringContainsString('?:mailing_lists', $this->crm_read);
        self::assertStringContainsString("'register_autoresponder' =>", $this->crm_read);
        self::assertStringContainsString("'list_status' =>", $this->crm_read);
    }

    public function testNewsletterConsentIsRawConfirmedStateNotEligibilityGuess(): void
    {
        self::assertStringContainsString("'confirmed' => (int) \$subscription['confirmed'] === 1", $this->crm_read);
        self::assertStringNotContainsString('send_eligible', $this->crm_read);
        self::assertStringNotContainsString('consent_granted', $this->crm_read);
    }

    public function testCrmAuditLogDoesNotExposeCredentialOrCustomerData(): void
    {
        $offset = strpos($this->crm_read, 'Talario CRM customer-360 authorized request completed');
        self::assertNotFalse($offset);
        $audit = substr($this->crm_read, $offset, 700);
        self::assertStringContainsString("source_ip_hash' => hash('sha256'", $audit);
        self::assertStringNotContainsString('$provided_token', $audit);
        self::assertStringNotContainsString("'email'", $audit);
    }
}
