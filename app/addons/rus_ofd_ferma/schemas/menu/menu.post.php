<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

$schema['top']['addons']['items']['ofd_ferma'] = array(
    'attrs' => array(
        'class'=>'is-addon'
    ),
    'href' => 'ofd_ferma.receipts',
    'position' => 600,
    'subitems' => array(
        'ofd_ferma.receipts' => array(
            'href' => 'ofd_ferma.receipts',
            'position' => 100,
        ),
        'ofd_ferma.manual' => array(
            'href' => 'ofd_ferma.manual',
            'position' => 200,
        )
    ),
);

return $schema;
