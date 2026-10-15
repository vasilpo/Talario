<?php

use PHPUnit\Framework\TestCase;

final class ScheduleOpenCaptureContractTest extends TestCase
{
    public function testDatePickerUsesCaptureFallbackForScheduleOpen(): void
    {
        $addon = dirname(__DIR__);
        $root = dirname($addon, 3);
        $behavior = file_get_contents($root . '/js/addons/talario_analytics/behavior.js');

        self::assertNotFalse($behavior);
        self::assertStringContainsString('function trackScheduleOpen()', $behavior);
        self::assertStringContainsString("document.addEventListener('click'", $behavior);
        self::assertStringContainsString('}, true);', $behavior);
        self::assertStringContainsString('.date-picker-wrapper', $behavior);
    }
}
