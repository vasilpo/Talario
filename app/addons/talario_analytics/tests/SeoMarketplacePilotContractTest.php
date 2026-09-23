<?php

use PHPUnit\Framework\TestCase;

final class SeoMarketplacePilotContractTest extends TestCase
{
    private string $controller;
    private string $template;

    protected function setUp(): void
    {
        $addon = dirname(__DIR__);
        $root = dirname($addon, 3);
        $this->controller = file_get_contents($addon . '/controllers/frontend/pages.post.php');
        $this->template = file_get_contents(
            $root . '/design/themes/abt__unitheme2/templates/addons/talario_analytics/hooks/pages/page_extra.post.tpl'
        );
    }

    public function testPilotIsLimitedToFiveApprovedArticlePaths(): void
    {
        foreach ([
            '/sport/tablica-razrjadov-po-plavaniju-normativy-dlja-vseh-vozrastov',
            '/blog/poyasa-v-thekvondo',
            '/blog/thekvondo-dlya-detey',
            '/blog/edinoborstva-dlya-detey-kakoe-vybrat',
            '/blog/sambo-dlya-detey-chto-eto',
        ] as $path) {
            self::assertStringContainsString($path, $this->controller);
        }
    }

    public function testPilotReadsProductsAndDoesNotWriteBusinessData(): void
    {
        self::assertStringContainsString('fn_get_products', $this->controller);
        self::assertStringContainsString('fn_gather_additional_products_data', $this->controller);
        self::assertStringNotContainsString('db_query(', $this->controller);
        self::assertStringNotContainsString('fn_update_product', $this->controller);
        self::assertStringNotContainsString('fn_update_page', $this->controller);
    }

    public function testAllCommercialLinksCarryArticleMarketplaceMarker(): void
    {
        self::assertStringContainsString('data-talario-article-marketplace="inline"', $this->template);
        self::assertStringContainsString('data-talario-article-marketplace="cards"', $this->template);
    }
}
