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

namespace Tygh\Shippings;

/**
 * Shipping services interface
 */
interface IService
{
    /**
     * Prepares request data for real-time shipping services.
     *
     * @return array Prepated shipping service information
     */
    public function prepareData($shipping_info);

    /**
     * Gets information about prices
     *
     * @param  string $response response information from real-time shipping services
     * @return array  Service shipping rate as 'cost' and returned errors as 'error'
     */
    public function processResponse($response);

    /**
    * Gets errors information from response
    *
    * @param string $response response information from real-time shipping services
    * @return string Error message from shipping service
    */
    public function processErrors($response);

    /**
     * Gets information about availability multithreading in the module
     *
     * @return bool True if service allows multithreading
     */
    public function allowMultithreading();

    /**
     * Gets data for request
     *
     * @return array Prepared data (method, url, post data, content type)
     */
    public function getRequestData();

    /**
     * Return calculated shipping rates
     *
     * @return array Shipping rates
     */
    public function getSimpleRates();
}
