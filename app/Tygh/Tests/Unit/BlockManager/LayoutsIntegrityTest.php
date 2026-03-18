<?php

namespace Tygh\BlockManager;

class LayoutsIntegrityTest extends \Tygh\Tests\Unit\ATestCase
{
    /**
     * @dataProvider dpData
     */
    public function testAvailability($layout_files, $expected_hash)
    {
        $files_content = '';
        foreach ((array) $layout_files as $file_path) {
            $this->assertFileExists($file_path);

            $files_content .= file_get_contents($file_path);
        }

        $this->assertEquals(md5($files_content), $expected_hash);
    }

    public function dpData()
    {
        $path = realpath(__DIR__ . '/../../../../../') . '/';

        $responsive = $path . 'design/themes/responsive/layouts/layouts.xml';

        $bright_theme = $path . 'design/themes/bright_theme/layouts/layouts.xml';

        $ru_responsive = $path . 'var/builds/ru/files/var/themes_repository/responsive/layouts/layouts.xml';
        $ru_bright_theme = $path . 'var/builds/ru/files/var/themes_repository/bright_theme/layouts/layouts.xml';

        $booking_responsive = $path . 'var/builds/booking/files/var/themes_repository/responsive/layouts/layouts.xml';

        $nova_theme = $path . 'design/themes/nova_theme/layouts/layouts.xml';

        //WARNING: Before changing the test, add changes to all the necessary layouts!!!
        return [
            [$responsive,         'e09bcf3cc9060c650d5ce5963b55d65d'],
            [$bright_theme,       '421e5197e532c86dec1b13c558bf83fd'],
            [$ru_responsive,      'e1fbd9eb58a286e8cb2c94c1a267a93f'],
            [$ru_bright_theme,    'f7df1b2212e0a7c29362090c67416f63'],
            [$booking_responsive, 'd8bc076d360b43cc2ef418aabab236a1'],
            [$nova_theme,         '241168da68b6ad5513adea808564f888'],
            [
                [
                    $responsive,
                    $bright_theme,
                    $ru_responsive,
                    $ru_bright_theme,
                    $booking_responsive,
                    $nova_theme,
                    $nova_theme,
                ],
                'cf4d225e0509d3096ee11aa88d183ba6'
            ],
        ];
    }
}
