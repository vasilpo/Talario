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

namespace Tygh\Addons\AdvancedImport\Modifiers\Parsers;

/**
 * The class decorates a parser and allows to cache the parsed results.
 *
 * @package Tygh\Addons\AdvancedImport\Modifiers\Parsers
 */
class CachingModifierParser implements IModifierParser
{
    /** @var IModifierParser */
    protected $parser;

    /** @var array Array that contains parsed modifiers */
    protected $cache = array();

    /**
     * CachingModifierParser constructor.
     *
     * @param \Tygh\Addons\AdvancedImport\Modifiers\Parsers\IModifierParser $parser
     */
    public function __construct(IModifierParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @inheritdoc
     */
    public function parse($modifier)
    {
        $hash = $this->getModifierHash($modifier);

        if (!isset($this->cache[$hash])) {
            $this->cache[$hash] = $this->parser->parse($modifier);
        }

        return $this->cache[$hash];
    }

    /**
     * Generates modifier hash
     *
     * @param string $modifier Modifier
     *
     * @return string
     */
    protected function getModifierHash($modifier)
    {
        return md5(trim($modifier));
    }
}