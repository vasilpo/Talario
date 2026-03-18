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
 * Initialize snippet product table variable for supplier invoice.
 *
 * @param \Tygh\Template\Snippet\Snippet                    $snippet
 * @param \Tygh\Addons\Suppliers\Documents\SupplierOrder\Context  $context
 * @param \Tygh\Template\Collection                         $variable_collection
 */
function fn_supplier_init_snippet_products_table_variable($snippet, $context, $variable_collection)
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
