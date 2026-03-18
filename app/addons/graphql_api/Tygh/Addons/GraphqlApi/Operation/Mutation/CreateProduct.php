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

namespace Tygh\Addons\GraphqlApi\Operation\Mutation;

class CreateProduct extends UpdateProduct
{
    public function run()
    {
        $product_id = 0;
        $company_id = $this->context->getCompanyId();
        if ($company_id) {
            $this->args['product']['company_id'] = $company_id;
        }

        $this->prepareFeatures($this->args['product']);
        $this->prepareImages($this->args['product'], $product_id);
        $this->prepareShippingParameters($this->args['product']);

        $result = fn_update_product($this->args['product'], $product_id, $this->context->getLanguageCode());

        return $result;
    }
}
