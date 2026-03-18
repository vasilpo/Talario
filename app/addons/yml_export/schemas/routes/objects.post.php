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

$schema['/yml_generate/[i:price_id]?'] = array(
    'dispatch' => 'yml.generate'
);

$schema['/yml_generate/[i:price_id]/[i:offset]'] = array(
    'dispatch' => 'yml.generate'
);

$schema['/yml_generate/[a:access_key]'] = array(
    'dispatch' => 'yml.generate'
);

$schema['/yml_generate/[a:access_key]/[i:offset]'] = array(
    'dispatch' => 'yml.generate'
);

$schema['/yml_get/[i:price_id]?'] = array(
    'dispatch' => 'yml.get'
);

$schema['/yml_get/[a:access_key]'] = array(
    'dispatch' => 'yml.get'
);

return $schema;
