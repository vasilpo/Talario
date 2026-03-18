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

namespace Tygh\ElFinder;

class Connector extends \elFinderConnector {

    protected function output(array $data) {
        $header = isset($data['header']) ? $data['header'] : $this->header;
        unset($data['header']);
        if ($header) {
            if (is_array($header)) {
                foreach ($header as $h) {
                    header($h);
                }
            } else {
                header($header);
            }
        }

        if (isset($data['pointer'])) {
            $chunksize = 1*(1024*1024); // 1MB
            rewind($data['pointer']);
            while (!feof($data['pointer'])) {
               echo(fread($data['pointer'], $chunksize));
               @ob_flush();
               flush();
            }
            if (!empty($data['volume'])) {
                $data['volume']->close($data['pointer'], $data['info']['hash']);
            }
            exit();
        } else {
            if (!empty($data['raw']) && !empty($data['error'])) {
                exit($data['error']);
            } else {
                exit(json_encode($data));
            }
        }

    }
}
