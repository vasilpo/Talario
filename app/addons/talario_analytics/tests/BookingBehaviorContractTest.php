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
}
