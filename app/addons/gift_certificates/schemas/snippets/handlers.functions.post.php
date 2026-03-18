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
 * Initialize snippet product table variable for gift certificate.
 *
 * @param \Tygh\Template\Snippet\Snippet                                    $snippet
 * @param \Tygh\Addons\GiftCertificates\Documents\GiftCertificate\Context   $context
 * @param \Tygh\Template\Collection                                         $variable_collection
 */
function fn_gift_certificates_init_product_table_variable($snippet, $context, $variable_collection)
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

    $gift_certificate = $context->getCertificateData();
    $products = !empty($gift_certificate['products']) ? $gift_certificate['products'] : array();

    $variable = new \Tygh\Template\VariableProxy(
        $config,
        $context,
        $object_factory,
        array('snippet' => $snippet, 'items' => $products)
    );

    $variable_collection->add('products_table', $variable);
}
