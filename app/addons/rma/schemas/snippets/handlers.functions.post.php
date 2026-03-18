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


/**
 * Initialize snippet products table variable for rma packing slip.
 *
 * @param \Tygh\Template\Snippet\Snippet                    $snippet
 * @param \Tygh\Addons\Rma\Documents\PackingSlip\Context    $context
 * @param \Tygh\Template\Collection                         $variable_collection
 */
function fn_rma_init_snippet_rma_products_table_variable($snippet, $context, $variable_collection)
{
    $object_factory = Tygh::$app['template.object_factory'];
    $config = array(
        'class' => '\Tygh\Template\Snippet\Table\TableVariable',
        'arguments' => array(
            '#context', '#snippet', '@template.renderer',
            '@template.snippet.table.column_repository',
            '@template.variable_collection_factory',
            '#items'
        ),
        'name' => 'products_table'
    );

    $variable = new \Tygh\Template\VariableProxy(
        $config,
        $context,
        $object_factory,
        array('snippet' => $snippet, 'items' => $context->getProducts())
    );

    $variable_collection->add('products_table', $variable);
}

/**
 * Initialize snippet returned products table variable for invoice.
 *
 * @param \Tygh\Template\Snippet\Snippet                    $snippet
 * @param \Tygh\Addons\Rma\Documents\PackingSlip\Context    $context
 * @param \Tygh\Template\Collection                         $variable_collection
 */
function fn_rma_init_snippet_returned_products_table_variable($snippet, $context, $variable_collection)
{
    $object_factory = Tygh::$app['template.object_factory'];
    $order = $context->getOrder();

    $products = !empty($order->data['returned_products']) ? $order->data['returned_products'] : array();

    $config = array(
        'class' => '\Tygh\Template\Snippet\Table\TableVariable',
        'arguments' => array(
            '#context', '#snippet', '@template.renderer',
            '@template.snippet.table.column_repository',
            '@template.variable_collection_factory',
            '#items'
        ),
        'name' => 'products_table'
    );

    foreach ($products as &$item) {
        $item['display_subtotal'] = $item['subtotal'];

        if (isset($item['extra']['base_price'])) {
            $item['base_price'] = $item['extra']['base_price'];
            $item['original_price'] = $item['extra']['base_price'];
        }
    }
    unset($item);

    $variable = new \Tygh\Template\VariableProxy(
        $config,
        $context,
        $object_factory,
        array('snippet' => $snippet, 'items' => $products)
    );

    $variable_collection->add('products_table', $variable);
}