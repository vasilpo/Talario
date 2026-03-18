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

namespace Tygh\Api\Entities\v20;

use Tygh\Api\Response;
use Tygh\Api\Entities\Products as BaseProducts;

/**
 * Represents api v2.0 of the products resource
 *
 * @package Tygh\Api\Entities\v20
 */
class Products extends BaseProducts
{
    /**
     * @inheritdoc
     */
    public function index($id = 0, $params = array())
    {
        if (empty($id)) {
            return parent::index($id, $params);
        }

        $lang_code = $this->getLanguageCode($params);

        if ($this->getParentName() == 'categories') {
            $parent_category = $this->getParentData();
            $params['cid'] = $parent_category['category_id'];
        }

        $data = fn_get_product_data($id, $this->auth, $lang_code, '', true, true, true, false, false, false, false);

        if (empty($data)) {
            $status = Response::STATUS_NOT_FOUND;
        } else {
            $status = Response::STATUS_OK;
        }

        return array(
            'status' => $status,
            'data'   => $data,
        );
    }
}
