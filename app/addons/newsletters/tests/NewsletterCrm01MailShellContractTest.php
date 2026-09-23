<?php

declare(strict_types=1);

namespace Tygh\Tests\Unit\Addons\Newsletters;

use PHPUnit\Framework\TestCase;

final class NewsletterCrm01MailShellContractTest extends TestCase
{
    private string $func;
    private string $controller;

    protected function setUp(): void
    {
        $base = dirname(__DIR__);
        $this->func = (string) file_get_contents($base . '/func.php');
        $this->controller = (string) file_get_contents($base . '/controllers/backend/newsletters.php');
    }

    public function testCrm01BypassesGlobalNewsletterWrapperOnlyForNewsletterFive(): void
    {
        self::assertStringContainsString('if ((int) $newsletter_id === 5)', $this->func);
        self::assertStringContainsString("$message['body'] = $body", $this->func);
        self::assertStringContainsString("$message['subject'] = $subj", $this->func);
        self::assertStringContainsString("$message['template_code'] = 'newsletters_newsletter'", $this->func);
        self::assertStringContainsString("$message['tpl'] = 'addons/newsletters/newsletter.tpl'", $this->func);
    }

    public function testBatchSendPassesNewsletterIdToMailer(): void
    {
        self::assertStringContainsString("$recipient['reply_to'], false, (int) $send_id", $this->controller);
    }

    public function testAdminTestSendPassesNewsletterIdToMailer(): void
    {
        self::assertStringContainsString("true, (int) $_REQUEST['newsletter_id']", $this->controller);
    }
}
