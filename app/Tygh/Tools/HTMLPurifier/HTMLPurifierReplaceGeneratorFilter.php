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

namespace Tygh\Tools\HTMLPurifier;

use HTMLPurifier;
use HTMLPurifier_Config;
use HTMLPurifier_Context;
use HTMLPurifier_Exception;

class HTMLPurifierReplaceGeneratorFilter
{
    /**
     * @var HTMLPurifier
     */
    private $purifier;

    /**
     * @param HTMLPurifier $purifier HTML Purifier instance
     */
    public function __construct(HTMLPurifier $purifier)
    {
        $this->purifier = $purifier;
    }

    /**
     * Pre-processor function, handles HTML before HTML Purifier
     *
     * @param string               $html    HTML
     * @param HTMLPurifier_Config  $config  Config
     * @param HTMLPurifier_Context $context Context
     *
     * @return string
     */
    public function preFilter($html, HTMLPurifier_Config $config, HTMLPurifier_Context $context)
    {
        $replacer = function () use ($config, $context) {
            /** @psalm-suppress UndefinedThisPropertyAssignment */
            $this->generator = new HTMLPurifierGenerator($config, $context);
        };

        $replacer->call($this->purifier);

        return $html;
    }

    /**
     * Post-processor function, handles HTML after HTML Purifier
     *
     * @param string               $html    HTML
     * @param HTMLPurifier_Config  $config  Config
     * @param HTMLPurifier_Context $context Context
     *
     * @return string
     */
    public function postFilter($html, HTMLPurifier_Config $config, HTMLPurifier_Context $context)
    {
        return $html;
    }

    /**
     * Adds HTMLPurifier filter
     *
     * @param HTMLPurifier $purifier HTML Purifier instance
     *
     * @return void
     */
    public static function addFilter(HTMLPurifier $purifier)
    {
        $self = new self($purifier);

        $config = $purifier->config;

        try {
            $filters = (array) $config->get('Filter.Custom');
        } catch (HTMLPurifier_Exception $exception) {
            $filters = [];
        }

        $filters[] = $self;

        $config->set('Filter.Custom', $filters);
    }
}
