<?php

declare(strict_types=1);

namespace Tygh\Tests\Unit\Addons\Newsletters;

use PHPUnit\Framework\TestCase;

final class NewsletterFirstnameContractTest extends TestCase
{
    private string $func;

    protected function setUp(): void
    {
        $base = dirname(__DIR__);
        $this->func = (string) file_get_contents($base . '/func.php');
    }

    public function testRecipientQueriesCarryFirstname(): void
    {
        self::assertStringContainsString("COALESCE(users.firstname, '') as firstname", $this->func);
        self::assertStringContainsString("users.firstname FROM ?:users AS users", $this->func);
    }

    public function testRendererSupportsFirstnameAndSafeGreeting(): void
    {
        self::assertStringContainsString("['%FIRSTNAME']", $this->func);
        self::assertStringContainsString("['%FIRSTNAME_GREETING%']", $this->func);
        self::assertStringContainsString("'Здравствуйте!'", $this->func);
    }

    public function testMailingListConsentFilterRemainsIntact(): void
    {
        self::assertStringContainsString(
            'user_mailing_lists.confirmed = ?i OR mailing_lists.register_autoresponder = ?i',
            $this->func
        );
    }
}
