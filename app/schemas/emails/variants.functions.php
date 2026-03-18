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
 * Gets available order documents for order notification
 *
 * @return array
 */
function fn_emails_get_order_document_variants()
{
    /** @var \Tygh\Template\Document\Repository $repository */
    $repository = \Tygh::$app['template.document.repository'];

    $result = array();
    $documents = $repository->findByType('order');

    foreach ($documents as $document) {
        $result[$document->getCode()] = $document->getName();
    }

    return $result;
}