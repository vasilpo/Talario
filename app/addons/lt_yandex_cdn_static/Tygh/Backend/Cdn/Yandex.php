<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

namespace Tygh\Backend\Cdn;

class Yandex extends ABackend
{
    /**
     * {@inheritdoc}
     */
    public function getOption($key)
    {
        $value = parent::getOption($key);

        if ($key !== 'is_enabled') {
            return $value;
        }

        return (bool) $value
            && !empty($this->_options['is_active'])
            && !empty($this->_options['cname'])
            && defined('AREA')
            && AREA === 'C';
    }

    /**
     * {@inheritdoc}
     */
    public function createDistribution($host, $options = [])
    {
        return [
            'host'      => '',
            'id'        => '',
            'is_active' => !empty($options['cname']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateDistribution($host, $options)
    {
        return $this->createDistribution($host, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDistribution()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return !empty($this->_options['cname']);
    }
}
