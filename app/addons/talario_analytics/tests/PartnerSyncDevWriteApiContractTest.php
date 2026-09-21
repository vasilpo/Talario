<?php

declare(strict_types=1);

namespace Tygh\Tests\Unit\Addons\TalarioAnalytics;

use PHPUnit\Framework\TestCase;

final class PartnerSyncDevWriteApiContractTest extends TestCase
{
    private string $controller;

    protected function setUp(): void
    {
        $path = dirname(__DIR__) . '/controllers/frontend/talario_partner_sync.php';
        $this->controller = (string) file_get_contents($path);
    }

    public function testWriteApiIsDevCopyOnly(): void
    {
        self::assertStringContainsString('fn_is_development()', $this->controller);
        self::assertStringContainsString('TALARIO_PARTNER_SYNC_DEV_COPY', $this->controller);
        self::assertStringContainsString("'/dev_copy/'", $this->controller);
        self::assertStringNotContainsString('TALARIO_PARTNER_SYNC_PROD_READ', $this->controller);
        self::assertStringContainsString("['error' => 'not_found']", $this->controller);
    }

    public function testWriteApiUsesSeparateCredential(): void
    {
        self::assertStringContainsString('TALARIO_PARTNER_SYNC_WRITE_TOKEN_HASH', $this->controller);
        self::assertStringContainsString('partner_sync_write_credential_reused', $this->controller);
        self::assertStringContainsString('TALARIO_PARTNER_SYNC_TOKEN_HASH', $this->controller);
    }

    public function testOnlyPostIsAccepted(): void
    {
        self::assertStringContainsString("REQUEST_METHOD'] !== 'POST'", $this->controller);
        self::assertStringContainsString("['error' => 'method_not_allowed']", $this->controller);
    }

    public function testProductWritesAreWhitelistedAndOwnershipChecked(): void
    {
        self::assertStringContainsString('array_intersect_key($fields, array_flip($allowed))', $this->controller);
        self::assertStringContainsString('product_company_mismatch', $this->controller);
        self::assertStringContainsString('fn_update_product($product_data', $this->controller);
        self::assertStringContainsString("\$product_data['status'] = \$product_data['status'] ?? 'H'", $this->controller);
    }

    public function testScheduleWritesUseLegacyEcarterApi(): void
    {
        self::assertStringContainsString('Fn_Ec_Table_Booking_System_Update_Booking_data', $this->controller);
        self::assertStringContainsString("'booking_type' => 'T'", $this->controller);
        self::assertStringContainsString('invalid_schedule_time', $this->controller);
    }

    public function testMediaWritesAreBoundedAndRasterOnly(): void
    {
        self::assertStringContainsString('count($images) > 8', $this->controller);
        self::assertStringContainsString('strlen($binary) > 4194304', $this->controller);
        self::assertStringContainsString("'image/jpeg' => 'jpg'", $this->controller);
        self::assertStringContainsString("'image/png' => 'png'", $this->controller);
        self::assertStringContainsString("'image/webp' => 'webp'", $this->controller);
        self::assertStringContainsString('new Products([], \'A\')', $this->controller);
        self::assertStringContainsString('fn_rm($path)', $this->controller);
        self::assertStringContainsString('$media_error = null', $this->controller);
    }
}
