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


namespace Tygh\Template\Snippet\Table;

use Tygh\Template\IContext;

/**
 * The context class for an item represented in a table.
 *
 * @package Tygh\Template\Snippet\Table
 */
class ItemContext implements IContext
{
    /** @var IContext */
    protected $parent_context;

    /** @var mixed */
    protected $item;

    /** @var int */
    protected $counter;

    /**
     * ItemContext constructor.
     *
     * @param IContext          $context Instance of parent context.
     * @param array<string|int> $item    Item data.
     * @param int               $counter Sequential item counter.
     */
    public function __construct(IContext $context, $item, $counter = 0)
    {
        /**
         * Allows to change the table item context for the render of the data table snippet.
         *
         * @param self                    $this    Instance of current context
         * @param \Tygh\Template\IContext $context Instance of parent context
         * @param array<string|int>       $item    Item data
         * @param int                     $counter Sequential item counter
         */
        fn_set_hook('template_snippet_table_item_context_init', $this, $context, $item, $counter);

        $this->parent_context = $context;
        $this->counter = $counter;
        $this->item = $item;
    }

    /**
     * Gets item.
     *
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Gets parent context.
     *
     * @return IContext
     */
    public function getParentContext()
    {
        return $this->parent_context;
    }
    
    /**
     * @inheritDoc
     */
    public function getLangCode()
    {
        return $this->parent_context->getLangCode();
    }

    /**
     * @inheritDoc
     */
    public function getLanguageDirection()
    {
        return fn_is_rtl_language($this->parent_context->getLangCode()) ? 'rtl' : 'ltr';
    }

    /**
     * Fetches item's sequence counter
     *
     * @return int
     */
    public function getCounter()
    {
        return $this->counter;
    }

    /**
     * @inheritDoc
     */
    public function getArea()
    {
        return $this->parent_context->getArea();
    }
}
