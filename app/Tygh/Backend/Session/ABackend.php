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

namespace Tygh\Backend\Session;

/**
 * Class ABackend
 *
 * @package Tygh\Backend\Session
 *
 * phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
 * phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingTraversableTypeHintSpecification
 * phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingTraversableTypeHintSpecification
 * phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
 */
abstract class ABackend
{
    /**
     * @var array
     */
    protected $config;

    /**
     * Init backend
     *
     * @param array $config Global configuration params
     * @param array $params Additional params passed from Session class
     */
    public function __construct(array $config, array $params = [])
    {
        $this->config = $params;
    }

    /**
     * Read session data
     *
     * @param string $sess_id Session ID
     *
     * @return string|false Session data if exist, false otherwise
     */
    public function read($sess_id)
    {
        return false;
    }

    /**
     * Write session data
     *
     * @param string $sess_id Session ID
     * @param array  $data    Session data
     *
     * @return bool Always true
     */
    public function write($sess_id, $data)
    {
        return false;
    }

    /**
     * Update session ID
     *
     * @param string $old_id Old session ID
     * @param string $new_id New session ID
     *
     * @return bool Always true
     */
    public function regenerate($old_id, $new_id)
    {
        return false;
    }

    /**
     * Delete session data
     *
     * @param string $sess_id Session ID
     *
     * @return bool Always true
     */
    public function delete($sess_id)
    {
        return false;
    }

    /**
     * Garbage collector (do nothing as redis takes care about deletion of expired keys)
     *
     * @param int $max_lifetime Session lifetime
     *
     * @return bool Always true
     */
    public function gc($max_lifetime)
    {
        return false;
    }
}
