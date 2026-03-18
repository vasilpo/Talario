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

namespace Tygh\Addons\ProductReviews\Notifications\EventIdProviders;

use Tygh\Notifications\EventIdProviders\IProvider;

class ProductReviewsEventProvider implements IProvider
{
    /**
     * @var string
     */
    protected $prefix = 'product_reviews.';

    /**
     * @var string
     */
    protected $id;

    /**
     * ProductReviewsEventProvider constructor.
     *
     * @param int $product_review_id Product review identifier
     */
    public function __construct($product_review_id)
    {
        $this->id = $this->prefix . $product_review_id;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }
}
