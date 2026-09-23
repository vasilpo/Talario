<?php

declare(strict_types=1);

namespace Tygh\Tests\Unit\Addons\Newsletters;

use PHPUnit\Framework\TestCase;

final class NewsletterCrm01UnsubscribeContractTest extends TestCase
{
    private string $func;

    protected function setUp(): void
    {
        $base = dirname(__DIR__);
        $this->func = (string) file_get_contents($base . '/func.php');
    }

    public function testDirectUsersReuseOnlyConfirmedExistingSubscriptions(): void
    {
        self::assertStringContainsString(
            'MIN(mailing_lists.list_id) as list_id, subscribers.subscriber_id',
            $this->func
        );
        self::assertStringContainsString(
            'LOWER(TRIM(subscribers.email)) = LOWER(TRIM(users.email))',
            $this->func
        );
        self::assertStringContainsString(
            'user_mailing_lists.confirmed = ?i',
            $this->func
        );
        self::assertStringContainsString(
            'mailing_lists.status IN (?a)',
            $this->func
        );
    }

    public function testUnsubscribeRenderingStillRequiresListAndSubscriber(): void
    {
        self::assertStringContainsString(
            "if (!empty($subscriber['list_id']) && !empty($subscriber['subscriber_id']))",
            $this->func
        );
        self::assertStringContainsString(
            "fn_generate_unsubscribe_link($subscriber['list_id'], $subscriber['subscriber_id'])",
            $this->func
        );
    }
}
