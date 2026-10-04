<?php

declare(strict_types=1);

namespace Tygh\Tests\Unit\Addons\TalarioAnalytics;

use PHPUnit\Framework\TestCase;

final class BehaviorAnalyticsContractTest extends TestCase
{
    private string $behavior;

    protected function setUp(): void
    {
        $path = dirname(__DIR__, 4) . '/js/addons/talario_analytics/behavior.js';
        $this->behavior = (string) file_get_contents($path);
    }

    public function testArticleMarketplaceClickHasDedicatedGoalAndExplicitMarker(): void
    {
        self::assertStringContainsString('talario_article_marketplace_click: true', $this->behavior);
        self::assertStringContainsString("[data-talario-article-marketplace]", $this->behavior);
        self::assertStringContainsString("emit('talario_article_marketplace_click'", $this->behavior);
    }

    public function testArticleMarketplaceTrackingDoesNotAttachRawHrefOrUserData(): void
    {
        self::assertStringContainsString("path: pagePath()", $this->behavior);
        self::assertStringNotContainsString("href: $(this)", $this->behavior);
        self::assertStringNotContainsString("email:", $this->behavior);
        self::assertStringNotContainsString("phone:", $this->behavior);
    }
}
