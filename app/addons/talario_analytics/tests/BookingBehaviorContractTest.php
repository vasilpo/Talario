<?php

use PHPUnit\Framework\TestCase;

final class BookingBehaviorContractTest extends TestCase
{
    private string $behavior;
    private string $contextTemplate;

    protected function setUp(): void
    {
        $addon = dirname(__DIR__);
        $root = dirname($addon, 3);
        $this->behavior = file_get_contents($root . '/js/addons/talario_analytics/behavior.js');
        $this->contextTemplate = file_get_contents(
            $root . '/design/themes/abt__unitheme2/templates/addons/talario_analytics/hooks/index/scripts.post.tpl'
        );
    }

    public function testFinalBookingEventsAreAllowlisted(): void
    {
        foreach ([
            'talario_booking_complete',
            'talario_free_booking_complete',
            'talario_booking_error',
        ] as $event) {
            self::assertStringContainsString($event . ': true', $this->behavior);
        }
    }

    public function testCheckoutCompleteDoesNotEmitCheckoutStart(): void
    {
        self::assertStringContainsString("!ctx.is_completed_order", $this->behavior);
        self::assertStringNotContainsString("!ctx.order_id", $this->behavior);
    }

    public function testBookingCompletionSeparatesPaidAndFree(): void
    {
        self::assertStringContainsString("emit('talario_free_booking_complete'", $this->behavior);
        self::assertStringContainsString("emit('talario_booking_complete'", $this->behavior);
        self::assertStringContainsString('is_completed_order', $this->contextTemplate);
        self::assertStringContainsString('is_booking_order', $this->contextTemplate);
        self::assertStringContainsString('is_free_order', $this->contextTemplate);
    }

    public function testBookingErrorRequiresRecentBookingIntent(): void
    {
        self::assertStringContainsString("bookingPendingKey = 'talario_booking_pending_at'", $this->behavior);
        self::assertStringContainsString('hasRecentBookingPending()', $this->behavior);
        self::assertStringContainsString("ce.notificationshow", $this->behavior);
        self::assertStringContainsString("'.alert-error'", $this->behavior);
    }

    public function testEventsDoNotAttachOrderOrCustomerIdentifiers(): void
    {
        self::assertStringNotContainsString("order_id:", $this->behavior);
        self::assertStringNotContainsString("email:", $this->behavior);
        self::assertStringNotContainsString("phone:", $this->behavior);
    }

    public function testCatalogVariationFeatureInputsAreTracked(): void
    {
        self::assertStringContainsString(
            'input[name^="feature_"]',
            $this->behavior
        );
        self::assertStringContainsString("option: $field.attr('name')", $this->behavior);
        self::assertStringContainsString("variation_id: $field.val()", $this->behavior);
        self::assertStringContainsString("data-ca-variant-id", $this->behavior);
    }

    public function testProductOpenUsesCanonicalProductPageDetection(): void
    {
        self::assertStringContainsString("var isProductController = ctx.controller === 'products'", $this->behavior);
        self::assertStringContainsString("ctx.mode === 'view' || ctx.mode === 'options'", $this->behavior);
        self::assertStringContainsString("if (productId && (isProductController || hasProductForm))", $this->behavior);
        self::assertSame(1, substr_count($this->behavior, "emit('talario_product_open'"));
        self::assertStringNotContainsString("a[href*='products.view'][href*='product_id=']", $this->behavior);
    }

    public function testGuestCheckoutStartCoversLoginGate(): void
    {
        self::assertStringContainsString('a[href*="checkout.login_form"]', $this->behavior);
        self::assertStringContainsString('button[name^="dispatch[checkout.login_form"]', $this->behavior);
        self::assertStringContainsString('a[href*="checkout.checkout"]', $this->behavior);
    }

    public function testAddToCartRequiresSuccessfulCheckoutAddResponse(): void
    {
        self::assertStringContainsString("params.url.indexOf('checkout.add') === -1", $this->behavior);
        self::assertStringContainsString('data.yandex_metrika', $this->behavior);
        self::assertStringContainsString("emit('talario_add_to_cart'", $this->behavior);
    }

    public function testScheduleOpenUsesCaptureFallbackForDatePickerClicks(): void
    {
        self::assertStringContainsString('function trackScheduleOpen()', $this->behavior);
        self::assertStringContainsString("document.addEventListener('click'", $this->behavior);
        self::assertStringContainsString('}, true);', $this->behavior);
        self::assertStringContainsString("trackScheduleOpen();", $this->behavior);
    }
}
