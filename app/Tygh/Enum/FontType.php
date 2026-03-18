<?php
/***************************************************************************
*                                                                          *
*   © 2012 ООО "Эком Системы"                                              *
*                                                                          *
* Это коммерческое программное обеспечение. Только пользователи, которые   *
* приобрели действующую лицензию и согласились с условиями лицензионного   *
* соглашения, могут устанавливать и использовать эту программу.            *
*                                                                          *
****************************************************************************
* ПОЖАЛУЙСТА, ВНИМАТЕЛЬНО ПРОЧТИТЕ ПОЛНЫЙ ТЕКСТ ЛИЦЕНЗИОННОГО СОГЛАШЕНИЯ   *
* В ФАЙЛЕ "copyright.txt", ПРЕДОСТАВЛЕННОМ ВМЕСТЕ С ЭТИМ ДИСТРИБУТИВОМ.    *
***************************************************************************/

namespace Tygh\Enum;

/**
 * Contains font types.
 *
 * @package Tygh\Enum
 */
class FontType
{
    const WOFF = 'font/woff';
    const WOFF2 = 'font/woff2';
    const TTF = 'font/ttf';
    const OTF = 'font/otf';
    const SVG = 'image/svg+xml';
    const FALLBACK = null;

    const EXT_WOFF = 'woff';
    const EXT_WOFF2 = 'woff2';
    const EXT_TTF = 'ttf';
    const EXT_OTF = 'otf';
    const EXT_SVG = 'svg';

    /**
     * Gets font types sorted by their support, the most supported first.
     *
     * @return array<string|null>
     */
    public static function getAllBySupport()
    {
        // woff2 and woff seem to be the most supported font types, so use them first
        return [
            self::WOFF2,
            self::WOFF,
            self::TTF,
            self::OTF,
            self::SVG,
            self::FALLBACK,
        ];
    }

    /**
     * Checks whether type is the fallback one.
     *
     * @param string|null $type Font type
     *
     * @return bool
     */
    public static function isFallback($type)
    {
        return $type === self::FALLBACK;
    }

    /**
     * Gets font type by font file extension.
     *
     * @param string $extension Extension
     *
     * @return string|null
     */
    public static function getByExtension($extension)
    {
        switch ($extension) {
            case self::EXT_WOFF2:
                return self::WOFF2;
            case self::EXT_WOFF:
                return self::WOFF;
            case self::EXT_TTF:
                return self::TTF;
            case self::EXT_OTF:
                return self::OTF;
            case self::EXT_SVG:
                return self::SVG;
            default:
                return self::FALLBACK;
        }
    }
}
