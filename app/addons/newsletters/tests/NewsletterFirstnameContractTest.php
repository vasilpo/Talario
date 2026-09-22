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

    public function testDirectUserRecipientQueriesCarryFirstname(): void
    {
        self::assertStringContainsString(
            'NULL as list_id, NULL as subscriber_id, users.firstname FROM ?:users AS users',
            $this->func
        );
    }

    public function testMailingListPayloadDoesNotJoinCustomerProfile(): void
    {
        self::assertStringContainsString(
            'SELECT 0 as user_id, subscribers.email, subscribers.lang_code, mailing_lists.list_id',
            $this->func
        );
        self::assertStringNotContainsString(
            'COALESCE(users.firstname',
            $this->func
        );
    }

    public function testRendererEscapesFirstnameWithoutDatabaseLookup(): void
    {
        self::assertStringContainsString("['%FIRSTNAME%']", $this->func);
        self::assertStringContainsString("['%FIRSTNAME_GREETING%']", $this->func);
        self::assertStringContainsString('htmlspecialchars(', $this->func);
        self::assertStringNotContainsString(
            'SELECT confirmed FROM ?:user_mailing_lists WHERE list_id = ?i AND subscriber_id = ?i',
            $this->func
        );
    }

    public function testMailingListConsentFilterRemainsIntact(): void
    {
        self::assertStringContainsString(
            'user_mailing_lists.confirmed = ?i OR mailing_lists.register_autoresponder = ?i',
            $this->func
        );
    }
}
