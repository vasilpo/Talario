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

namespace Tygh\Notifications\Transports\Mail\ReceiverFinders;

use Tygh\Database\Connection;
use Tygh\Enum\YesNo;
use Tygh\Notifications\Transports\Mail\MailMessageSchema;

class VendorEmailFinder implements ReceiverFinderInterface
{
    /**
     * @var \Tygh\Database\Connection
     */
    protected $db;

    /**
     * VendorEmailFinder constructor.
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * @inheritDoc
     */
    public function find($criterion, MailMessageSchema $message_schema)
    {
        $company_id = $this->getCompanyId($message_schema);
        if (!$company_id) {
            return [];
        }

        if (!$this->isDefaultEmailField((int) $criterion)) {
            $conditions = [
                'field_id'  => $criterion,
                'object_id' => $company_id,
            ];

            return $this->db->getColumn(
                'SELECT value'
                . ' FROM ?:profile_fields_data'
                . ' WHERE ?w',
                $conditions
            );
        }

        $conditions = [
            'company_id' => $company_id,
        ];

        return $this->db->getColumn(
            'SELECT email'
            . ' FROM ?:companies'
            . ' WHERE ?w',
            $conditions
        );
    }

    /**
     * Gets company ID.
     *
     * @param \Tygh\Notifications\Transports\Mail\MailMessageSchema $schema Message schema
     *
     * @return int|null
     */
    private function getCompanyId(MailMessageSchema $schema)
    {
        return $schema->to_company_id
            ? $schema->to_company_id
            : null;
    }

    /**
     * Checks if the email field is the default.
     *
     * @param int $criterion E-mail field ID
     *
     * @return bool
     */
    private function isDefaultEmailField($criterion)
    {
        $email_field = fn_get_profile_field($criterion);

        return YesNo::toBool($email_field['is_default']);
    }
}
