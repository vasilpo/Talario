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


namespace Tygh\Template\Document\Variables;


use Tygh\Template\IContext;
use Tygh\Template\IVariable;

/**
 * The class that allows to specify the variables available in the document editor with a schema, without the need to create separate classes.
 *
 * @package Tygh\Template\Document\Variables
 */
class GenericVariable implements IVariable, \ArrayAccess
{
    /** @var array  */
    protected $data = array();

    /**
     * GenericVariable constructor.
     * @param IContext  $context
     * @param array     $config
     */
    public function __construct(IContext $context, array $config)
    {
        if (isset($config['data'])) {
            if ($config['data'] instanceof \Closure) {
                $this->data = $config['data']($context);
            } else {
                $this->data = $config['data'];
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        $this->data[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }
}