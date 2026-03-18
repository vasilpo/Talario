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

namespace Tygh\Api\Entities;

use Tygh\Registry;
use Tygh\Addons\StorefrontRestApi\ASraEntity;
use Tygh\Api\Response;
use Tygh\BlockManager\Layout;

/**
 * Class SraBmLayouts
 *
 * @package Tygh\Api\Entities
 */
class SraBmLayouts extends ASraEntity
{
    /**
     * @inheritDoc
     */
    public function index($id = '', $params = array())
    {
        $status = Response::STATUS_OK;
        $layout = $this->getLayoutRepository();

        if ($id) {
            $data = $layout->get($id);

            if (empty($data)) {
                $status = Response::STATUS_NOT_FOUND;
            }

            if (isset($data['company_id']) && fn_allowed_for('ULTIMATE')) {
                Registry::set('runtime.company_id', $data['company_id']);
            }
        } else {
            $data = $layout->getList($params);
        }

        return array(
            'status' => $status,
            'data'   => $data
        );
    }

    /**
     * @inheritDoc
     */
    public function create($params)
    {
        return array(
            'status' => Response::STATUS_METHOD_NOT_ALLOWED,
        );
    }

    /**
     * @inheritDoc
     */
    public function update($id, $params)
    {
        return array(
            'status' => Response::STATUS_METHOD_NOT_ALLOWED,
        );
    }

    /**
     * @inheritDoc
     */
    public function delete($id)
    {
        return array(
            'status' => Response::STATUS_METHOD_NOT_ALLOWED,
        );
    }

    /**
     * @inheritDoc
     */
    public function privilegesCustomer()
    {
        return array(
            'index'  => true,
            'create' => false,
            'update' => false,
            'delete' => false,
        );
    }

    /**
     * @inheritDoc
     */
    public function privileges()
    {
        return array(
            'index'  => true,
            'create' => 'edit_blocks',
            'update' => 'edit_blocks',
            'delete' => 'edit_blocks',
        );
    }

    /**
     * @return Layout
     */
    protected function getLayoutRepository()
    {
        return Layout::instance($this->getCompanyId());
    }
}
