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

defined('BOOTSTRAP') or die('Access denied');

/**
 * @psalm-var array $schema
 */
$schema['agreements'] = [
    'params'                => [
        'fields_list' => ['email'],
    ],
    'collect_data_callback' => static function ($params) {
        if (empty($params['user_id'])) {
            return [];
        }

        $conditions = db_quote('user_id = ?i', $params['user_id']);

        if (!empty($params['email'])) {
            $conditions .= db_quote(' OR email = ?s', $params['email']);
        }

        return db_get_array(
            'SELECT agreement_id, email FROM ?:gdpr_user_agreements WHERE ?p',
            $conditions
        );
    },
    'update_data_callback'  => static function ($agreements) {
        if (empty($agreements)) {
            return;
        }

        $agreement_ids = array_column($agreements, 'agreement_id');
        $first_agreement = reset($agreements);
        $email = isset($first_agreement['email'])
            ? $first_agreement['email']
            : '';

        if (!$email || !$agreement_ids) {
            return;
        }

        db_query(
            'UPDATE ?:gdpr_user_agreements SET ?u WHERE agreement_id IN (?n)',
            ['email' => $email],
            $agreement_ids
        );
    },
];

return $schema;
