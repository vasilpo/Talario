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

namespace Tygh\UpgradeCenter\Migrations;

use Phinx\Db\Adapter\AdapterFactory;
use Tygh\Exceptions\DatabaseException;
use Tygh\Registry;
use PDOException;
use Tygh\UpgradeCenter\Console\UpgradePhinxApplication;

/**
 * Migration class that allows to use Phinx migrations not via console
 *
 * @method migrate(int $minimal_date)
 */
class Migration
{
    /**
     * @var self $instance
     */
    private static $instance;

    /**
     * Migrations rules
     *
     * @var array $config
     */
    protected $config = array();

    /**
     * Returns instance of Migration class
     *
     * @return self
     */
    public static function instance($params = array())
    {
        if (empty(self::$instance)) {
            self::$instance = new self($params);
        }

        return self::$instance;
    }

    public function __construct($config)
    {
        $this->config = $config;

        Registry::set('config.dir.migrations', $config['migration_dir']);
    }

    public function __call($name, $arguments)
    {
        $config_path = Registry::get('config.dir.root') . '/app/Tygh/UpgradeCenter/Migrations/config.migrations.php';

        switch ($name) {
            case 'migrate':
                $_SERVER['argv'] = array(
                    'phinx',
                    'migrate',
                    '-c' . $config_path,
                    '-edevelopment',
                );
                break;

            case 'rollback':
                $_SERVER['argv'] = array(
                    'phinx',
                    'rollback',
                    '-c' . $config_path,
                    '-edevelopment',
                );
                break;

            default:
                return false;
        }

        $output = new Output;
        $output->setConfig($this->config);
        $output->setVerbosity(Output::VERBOSITY_VERBOSE);

        AdapterFactory::instance()
            ->registerAdapter('mysqli', '\Tygh\UpgradeCenter\Phinx\MysqliAdapter')
            ->registerAdapter('mysql', '\Tygh\UpgradeCenter\Phinx\MysqlAdapter');

        $app = new UpgradePhinxApplication();
        $app->setAutoExit(false);
        $app->setCatchExceptions(false);

        try {
            $exit_code = $app->run(null, $output);
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), (int) $e->getCode(), $e);
        }

        return ($exit_code === 0);
    }
}
