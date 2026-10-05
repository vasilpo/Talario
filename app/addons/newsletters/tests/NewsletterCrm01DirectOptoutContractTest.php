<?php

declare(strict_types=1);

namespace Tygh\Tests\Unit\Addons\Newsletters;

use PHPUnit\Framework\TestCase;

final class NewsletterCrm01DirectOptoutContractTest extends TestCase
{
    public function testSignedDirectUserOptoutIsWiredAndExcluded(): void
    {
        $base = dirname(__DIR__);
        $func = (string) file_get_contents($base . '/func.php');
        $frontend = (string) file_get_contents($base . '/controllers/frontend/newsletters.php');

        self::assertStringContainsString('fn_talario_crm_generate_unsubscribe_token', $func);
        self::assertStringContainsString('fn_talario_crm_generate_unsubscribe_link', $func);
        self::assertStringContainsString("hash_hmac(", $func);
        self::assertStringContainsString("crm_optout.type = ?s AND crm_optout.data = ?s", $func);
        self::assertStringContainsString("crm_optout.user_id IS NULL", $func);
        self::assertStringContainsString("'crm01_optout'", $func);

        self::assertStringContainsString("if ($mode == 'crm_unsubscribe')", $frontend);
        self::assertStringContainsString('hash_equals($expected_token, $token)', $frontend);
        self::assertStringContainsString("db_replace_into('user_data'", $frontend);
        self::assertStringContainsString("'type' => 'Z'", $frontend);
        self::assertStringContainsString("'data' => 'crm01_optout'", $frontend);
    }
}
