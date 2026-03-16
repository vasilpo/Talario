<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

namespace Tygh\Addons\PartnerSites\Repository;

class PartnerSiteClickRepository
{
    public static function create(): self
    {
        return new self();
    }

    public function createClick(array $data)
    {
        return db_query('INSERT INTO ?:exikane_partner_site_clicks ?e', $data);
    }

    public function deleteByProductId(int $product_id): void
    {
        db_query('DELETE FROM ?:exikane_partner_site_clicks WHERE product_id = ?i', $product_id);
    }

    public function getTotalCount(): int
    {
        return (int) db_get_field('SELECT COUNT(*) FROM ?:exikane_partner_site_clicks');
    }

    public function findAll(?string $limit = null): array
    {
        return db_get_array(
            'SELECT click_id, user_id, email, product_id, timestamp'
            . ' FROM ?:exikane_partner_site_clicks'
            . ' ORDER BY click_id DESC ?p',
            $limit ?: ''
        );
    }
}
