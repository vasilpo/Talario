<?php

declare(strict_types=1);

namespace Tygh\Tests\Unit\Addons\Newsletters;

use PHPUnit\Framework\TestCase;

final class NewsletterCrm01HeaderContractTest extends TestCase
{
    private string $func;
    private string $controller;

    protected function setUp(): void
    {
        $base = dirname(__DIR__);
        $this->func = (string) file_get_contents($base . '/func.php');
        $this->controller = (string) file_get_contents($base . '/controllers/backend/newsletters.php');
    }

    public function testCrm01KeepsFooterAndOverridesOnlyHeader(): void
    {
        self::assertStringContainsString('if ((int) $newsletter_id === 5', $this->func);
        self::assertStringContainsString('Talario_Logo_WL.png', $this->func);
        self::assertStringContainsString('width="196"', $this->func);
        self::assertStringContainsString('height:auto', $this->func);
        self::assertStringContainsString('padding:18px 16px 8px', $this->func);
        self::assertStringContainsString('{{ body }}', $this->func);
        self::assertStringContainsString('{{ snippet("footer") }}', $this->func);
        self::assertStringNotContainsString('{{ snippet("header") }}', $this->func);
    }

    public function testBatchAndTestSendPassNewsletterId(): void
    {
        self::assertStringContainsString('$recipient[\'reply_to\'], false, (int) $send_id', $this->controller);
        self::assertStringContainsString('true, (int) $_REQUEST[\'newsletter_id\']', $this->controller);
    }
}
