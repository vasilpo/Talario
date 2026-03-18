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

namespace Tygh\SmartyEngine\Functions;

use Smarty\FunctionHandler\Base;
use Smarty\Template;

class Split extends Base
{
    /**
     * Split array into chunks
     * phpcs:ignore
     * @param array    $params   Function args
     * @param Template $template Template
     *
     * @return void
     */
    //phpcs:ignore
    public function handle($params, Template $template)
    {
        if (empty($params['data'])) {
            //$smarty->trigger_error("split: array doesn't defined");
            return;
        }
        if (empty($params['size'])) {
            trigger_error("split: size doesn't defined");

            return;
        }
        if (empty($params['assign'])) {
            trigger_error("split: assing variable doesn't defined");

            return;
        }

        $params['preserve_keys'] = !empty($params['preverse_keys']) ? $params['preverse_keys'] : false;

        $chunks = [];
        $size = count($params['data']);
        if ($params['simple']) {
            $items_per_column = (int) (!empty($params['size_is_horizontal']) ? ceil($size / $params['size']) : $params['size']);
            for ($i = 0; $i < $size; $i = $i + $items_per_column) {
                $chunks[] = array_slice($params['data'], $i, $items_per_column);
            }
        } else {
            if (!$params['vertical_delimition']) {
                $chunks = array_chunk($params['data'], $params['size'], $params['preserve_keys']);
            } else {
                $chunk_count = $params['size_is_horizontal'] ? ceil(count($params['data']) / $params['size']) : $size;
                $chunk_index = 0;
                foreach ($params['data'] as $value) {
                    $chunks[$chunk_index][] = $value;
                    if (++$chunk_index === $chunk_count) {
                        $chunk_index = 0;
                    }
                }
            }

            if (empty($params['skip_complete'])) {
                end($chunks);
                $end_key = key($chunks);
                while (sizeof($chunks[$end_key]) < $params['size']) {
                    $chunks[$end_key][] = '';
                }
            }
        }

        $template->getSmarty()->assign($params['assign'], $chunks, false);
    }
}
