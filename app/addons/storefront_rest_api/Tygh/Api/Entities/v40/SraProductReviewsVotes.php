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

namespace Tygh\Api\Entities\v40;

use Tygh\Addons\ProductReviews\ServiceProvider as ProductReviewsProvider;
use Tygh\Enum\Addons\ProductReviews\ProductReview\ProductReviewVoteValues;
use Tygh\Enum\ObjectStatuses;
use Tygh\Registry;
use Tygh\Api\Response;
use Tygh\Addons\StorefrontRestApi\ASraEntity;

class SraProductReviewsVotes extends ASraEntity
{
    /** @inheritdoc */
    public function index($id = '', $params = [])
    {
        return [
            'status' => Response::STATUS_FORBIDDEN,
        ];
    }

    /**
     * @param array<string|int> $params Params
     *
     * @return array<string, int>
     */
    public function create($params)
    {
        $service = ProductReviewsProvider::getService();

        $product_review_id = $this->safeGet($params, 'product_review_id', 0);
        $value = $this->safeGet($params, 'action', 'down') === 'up'
            ? ProductReviewVoteValues::VOTE_UP_VALUE
            : ProductReviewVoteValues::VOTE_DOWN_VALUE;

        $vote_data = [
            'product_review_id' => $product_review_id,
            'user_id'           => $this->auth['user_id'],
            'value'             => $value,
        ];

        if (!empty($this->auth['ip'])) {
            $vote_data['ip_address'] = $this->auth['ip'];
        } else {
            $ip = fn_get_ip();
            $vote_data['ip_address'] = $ip['host'];
        }

        $result = $service->addVote($vote_data);

        return [
            'status' => $result
                ? Response::STATUS_OK
                : Response::STATUS_BAD_REQUEST
        ];
    }

    /**
     * Forbids updating reviews via API.
     *
     * @param int                        $id     Product review ID
     * @param array<string, string>|null $params Request parameters
     *
     * @return array<string, int>
     */
    public function update($id, $params)
    {
        return [
            'status' => Response::STATUS_FORBIDDEN,
        ];
    }

    /**
     * Forbids removing reviews via API.
     *
     * @param int $id Product review ID
     *
     * @return array<string, int>
     */
    public function delete($id)
    {
        return [
            'status' => Response::STATUS_FORBIDDEN,
        ];
    }

    /** @inheritdoc */
    public function privilegesCustomer()
    {
        if (!static::isAddonEnabled()) {
            return [];
        }

        return [
            'index'  => false,
            'create' => true,
            'update' => false,
            'delete' => false,
        ];
    }

    /**
     * Checks whether the Product reviews add-on enabled.
     *
     * @return bool
     */
    public static function isAddonEnabled()
    {
        return Registry::ifGet('addons.product_reviews.status', ObjectStatuses::DISABLED) === ObjectStatuses::ACTIVE;
    }
}
