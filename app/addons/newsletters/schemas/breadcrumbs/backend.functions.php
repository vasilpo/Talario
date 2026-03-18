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
 * Gets newsletters link
 *
 * @param int $newsletter_id Newsletter identifier
 * @param string $type Newsletter type
 * @return array Breadcrumb link data
 */
function fn_br_newsletters_link($newsletter_id, $type)
{
    if (empty($type) && !empty($newsletter_id)) {
        $data = fn_get_newsletter_data($newsletter_id);
        $type = !empty($data['type']) ? $data['type'] : '';
    }

    if ($type == NEWSLETTER_TYPE_AUTORESPONDER) {
        $object_name = __('autoresponders');
    } elseif ($type == NEWSLETTER_TYPE_TEMPLATE) {
        $object_name = __('templates');
    } else {
        $object_name = __('newsletters');
    }

    $result = array(
        'title' => $object_name,
        'link' => "newsletters.manage?type=$type"
    );

    return $result;
}
