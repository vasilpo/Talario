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

namespace Tygh\SmartyEngine;

use Smarty\Resource\FilePlugin;
use Smarty\Template;
use Smarty\Template\Source;

class FileResource extends FilePlugin
{
    /**
     * Allows to override template source with addons
     *
     * @param Source        $source    Source
     * @param Template|null $_template Template
     */
    public function populate(Source $source, Template $_template = null)
    {
        if ($_template !== null) {
            /** @var Core $smarty */
            $smarty = $_template->getSmarty();
            $overridden_resource = fn_addon_template_overrides($source->resource, $smarty);

            if ($overridden_resource != $source->resource) {
                $source->name = $overridden_resource;
                $source->resource = $overridden_resource;
            }
        }

        parent::populate($source, $_template);
    }
}
