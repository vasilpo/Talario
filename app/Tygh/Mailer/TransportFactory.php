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


namespace Tygh\Mailer;


use Pimple\Container;
use Tygh\Settings;

/**
 * The class responsible for creating the sender object.
 *
 * @package Tygh\Mailer
 */
class TransportFactory implements ITransportFactory, ICompanyTransportFactory
{
    /**
     * @var array<string, array<string, \Tygh\Mailer\ITransport>> Internal cach
     */
    protected $instances = [];

    /**
     * @var array<int, mixed> Company settings
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
     */
    protected $company_settings = [];

    /**
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * TransportFactory constructor.
     *
     * @param \Pimple\Container $container Dependency Injection Container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function createTransport($type, $settings)
    {
        $setting_hash = md5(serialize($settings));
        if (isset($this->instances[$type][$setting_hash])) {
            return $this->instances[$type][$setting_hash];
        }

        $key = 'mailer.transport.' . $type;

        if (isset($this->container[$key])) {
            $factory = $this->container[$key];
        } else {
            $factory = $this->container['mailer.transport.default'];
        }

        return $this->instances[$type][$setting_hash] = $factory($settings);
    }

    /**
     * @inheritdoc
     */
    public function createTransportByCompanyId($company_id)
    {
        if (!isset($this->company_settings[$company_id])) {
            $this->company_settings[$company_id] = Settings::instance($company_id)->getValues('Emails');
        }

        return $this->createTransport(
            isset($this->company_settings[$company_id]['mailer_send_method']) ? $this->company_settings[$company_id]['mailer_send_method'] : null,
            $this->company_settings[$company_id]
        );
    }
}
