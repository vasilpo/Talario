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

namespace Tygh\Addons\Robokassa\Factories;

use Tygh\Addons\Robokassa\Payments\RobokassaSplit;
use Tygh\Database\Connection;

/**
 * @package Tygh\Addons\Robokassa\Factories
 */
class ProcessorFactory
{
    /** @var \Tygh\Database\Connection */
    protected $db;

    /** @var \Tygh\Addons\StripeConnect\PriceFormatter */
    protected $price_formatter;

    /**
     * ProcessorFactory constructor.
     *
     * @param \Tygh\Database\Connection $db Database connection
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Constructs payment method processor with default components by the payment method ID.
     *
     * @param int                        $payment_id       Payment method ID
     * @param array<string, string>|null $processor_params Payment method configuration
     *
     * @return \Tygh\Addons\Robokassa\Payments\RobokassaSplit|void
     */
    public function getByPaymentId($payment_id, $processor_params = null)
    {
        if (!$payment_id) {
            return;
        }

        $processor_script = $this->db->getField(
            'SELECT ?:payment_processors.processor_script'
            . ' FROM ?:payments'
            . ' LEFT JOIN ?:payment_processors'
                . ' ON ?:payments.processor_id = ?:payment_processors.processor_id'
            . ' WHERE payment_id = ?i',
            $payment_id
        );

        switch ($processor_script) {
            case RobokassaSplit::PROCESSOR_SCRIPT:
                return new RobokassaSplit(
                    $payment_id,
                    $this->db,
                    $processor_params
                );
            default:
                return;
        }
    }
}
