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


namespace Tygh\Addons\ProductVariations\Product\Group;

use Tygh\Tools\SecurityHelper;

/**
 * Class GroupCodeGenerator
 *
 * @package Tygh\Addons\ProductVariations\Product\Group
 */
class GroupCodeGenerator
{
    /**
     * @var \Tygh\Addons\ProductVariations\Product\Group\Repository
     */
    protected $repository;

    /**
     * @var \Tygh\Tools\SecurityHelper
     */
    protected $security_helper;

    /**
     * GroupCodeGenerator constructor.
     *
     * @param \Tygh\Addons\ProductVariations\Product\Group\Repository $repository
     * @param \Tygh\Tools\SecurityHelper                              $security_helper
     */
    public function __construct(Repository $repository, SecurityHelper $security_helper)
    {
        $this->repository = $repository;
        $this->security_helper = $security_helper;
    }

    public function next()
    {
        do {
            $code = $this->generate();
        } while ($this->repository->exists($code));

        return $code;
    }

    protected function generate()
    {
        return sprintf('PV-%s', strtoupper(substr($this->security_helper->generateRandomString(), 0, 9)));
    }
}