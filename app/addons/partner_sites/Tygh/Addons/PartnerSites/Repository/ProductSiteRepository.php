<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

namespace Tygh\Addons\PartnerSites\Repository;

class ProductSiteRepository
{
    public static function create(): self
    {
        return new self();
    }

    public function findByProductId(int $product_id): string
    {
        $site = (string) db_get_field(
            'SELECT site FROM ?:exikane_partner_product_sites WHERE product_id = ?i',
            $product_id
        );

        return trim($site);
    }

    public function save(int $product_id, string $site): void
    {
        db_query('REPLACE INTO ?:exikane_partner_product_sites ?e', [
            'product_id' => $product_id,
            'site'       => $site,
        ]);
    }

    public function deleteByProductId(int $product_id): void
    {
        db_query('DELETE FROM ?:exikane_partner_product_sites WHERE product_id = ?i', $product_id);
    }
}
