<?php

use PHPUnit\Framework\TestCase;

final class DispatcherInstallerContractTest extends TestCase
{
    private string $installer;

    protected function setUp(): void
    {
        $addon = dirname(__DIR__);
        $root = dirname($addon, 3);
        $this->installer = (string) file_get_contents(
            $root . '/ops/beget/install-reviewed-dispatcher.sh'
        );
    }

    public function testInstallerTargetsOnlyOutOfRepoDispatcher(): void
    {
        self::assertStringContainsString(
            'TARGET="$HOME/.local/bin/talario-dev-github-dispatcher"',
            $this->installer
        );
        self::assertStringContainsString(
            'SOURCE="$DEV_COPY/ops/beget/talario-dev-github-dispatcher.sh"',
            $this->installer
        );
        self::assertStringNotContainsString('authorized_keys', $this->installer);
    }

    public function testInstallerRequiresReviewedDispatcherCommitAndExactProdGuard(): void
    {
        self::assertStringContainsString(
            'REQUIRED_COMMIT="8e38f1d899593ba47d143aec5f20fc9a91500851"',
            $this->installer
        );
        self::assertStringContainsString(
            'EXPECTED_PROD_SHA="2dea53c94eecc33d84980bab1b808a35258e03f7"',
            $this->installer
        );
        self::assertStringContainsString(
            '"talario-analytics-prod-sync")',
            $this->installer
        );
        self::assertStringContainsString(
            'fail "SSH command is not allowlisted" 68',
            $this->installer
        );
    }

    public function testInstallerBacksUpAndRollsBackOnFailure(): void
    {
        self::assertStringContainsString('dispatcher-backups', $this->installer);
        self::assertStringContainsString('/usr/bin/cp -p "$TARGET" "$BACKUP"', $this->installer);
        self::assertStringContainsString('/usr/bin/cp -p "$BACKUP" "$TARGET"', $this->installer);
        self::assertStringContainsString('/usr/bin/bash -n "$SOURCE"', $this->installer);
        self::assertStringContainsString('/usr/bin/bash -n "$TARGET"', $this->installer);
    }

    public function testInstallerDoesNotInvokeNetworkOrGenericShell(): void
    {
        self::assertStringNotContainsString('ssh ', $this->installer);
        self::assertStringNotContainsString('curl ', $this->installer);
        self::assertStringNotContainsString('eval ', $this->installer);
        self::assertStringNotContainsString('bash -c', $this->installer);
    }
}
