<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

namespace Tygh\Addons\LtYandexCdnStatic\SmartyEngine\Extensions;

use Smarty\Extension\Base;
use Tygh\Addons\LtYandexCdnStatic\SmartyEngine\Filters\Output\RewriteStorefrontStaticUrls;

class LtYandexCdnStatic extends Base
{
    /**
     * @return \Smarty\Filter\FilterInterface[]
     */
    public function getOutputFilters(): array
    {
        return [
            new RewriteStorefrontStaticUrls(),
        ];
    }
}
