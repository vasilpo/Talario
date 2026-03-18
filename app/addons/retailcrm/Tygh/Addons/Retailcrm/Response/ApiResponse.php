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

namespace Tygh\Addons\Retailcrm\Response;

use RetailCrm\Response\ApiResponse as BaseResponse;

/**
 * The class wrapper for base RetailCrm ApiResponse.
 * Adds the ability to resend the checking a status.
 *
 * @package Tygh\Addons\Retailcrm
 */
class ApiResponse extends BaseResponse
{
    /**
     * @inheritDoc
     */
    public function isSuccessful()
    {
        return (isset($this->response['success']) ? $this->response['success'] : true) && parent::isSuccessful();
    }

    public static function fromOriginalResponse(BaseResponse $original_response)
    {
        $self = new self($original_response->statusCode);
        $self->response = $original_response->response;

        return $self;
    }
}