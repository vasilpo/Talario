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

namespace Tygh\Common;

use Tygh\Registry;
use Tygh\Tygh;

/**
 * Editing llms.txt file
 */
class Llms
{
    /**
     * @var bool
     */
    public $default = false;

    /**
     * @var string
     */
    public $path;

    /**
     * Constructor
     *
     * @param bool $default Whether instance is default llms
     *
     * @return void
     */
    public function __construct($default = false)
    {
        $this->default = $default;

        $this->path = $this->getPath();
    }

    /**
     * Gets the data of llms from the llms_data table for a storefront with specified identifier.
     * If identifier not specified - it gets llms data for default storefront.
     *
     * @param int|null $storefront_id The identifier of the storefront.
     *
     * @return array
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function getLlmsDataByStorefrontId($storefront_id)
    {
        if (empty($storefront_id)) {
            /** @var \Tygh\Storefront\Repository $repository */
            $repository = Tygh::$app['storefront.repository'];
            $storefront = $repository->findDefault();
            $storefront_id = $storefront->storefront_id ?? 0;
        }

        return db_get_row('SELECT llms_id, data FROM ?:llms_data WHERE storefront_id = ?i', $storefront_id);
    }

    /**
     * Adds an entry with the llms.txt data for a storefront with the specified id to the llms_data table;
     * updates the entry with the specified storefront_id, if it already exists in the llms_data table.
     *
     * @param int    $storefront_id The identifier of the storefront.
     * @param string $content       The content of llms.
     *
     * @return void
     */
    public function setLlmsDataForStorefrontId($storefront_id, $content)
    {
        $data = [
            'storefront_id' => $storefront_id,
            'data' => $content
        ];

        $llms_data = $this->getLlmsDataByStorefrontId($storefront_id);
        if (!empty($llms_data['llms_id'])) {
            $data['llms_id'] = $llms_data['llms_id'];
        }

        db_replace_into('llms_data', $data);
    }

    /**
     * Gets the content of the llms.txt file, if it exists. Returns void otherwise.
     *
     * @return string|void Returns the content of the llms.txt file.
     */
    public function getLlmsTxtContent()
    {
        $llms_path = $this->getPath();

        if (!file_exists($llms_path)) {
            return null;
        }

        return file_get_contents($llms_path);
    }

    /**
     * Deletes an entry with the specified storefront_id from the llms_data table.
     *
     * @param int $storefront_id The identifier of the storefront.
     *
     * @return void
     */
    public function deleteLlmsDataByStorefrontId($storefront_id)
    {
        db_query('DELETE FROM ?:llms_data WHERE storefront_id = ?i', $storefront_id);
    }

    /**
     * Gets llms.txt file path.
     *
     * @return string
     */
    protected function getPath()
    {
        $path = Registry::get('config.dir.root');
        if ($this->default) {
            $path .= '/var';
        }
        $path .= '/llms.txt';

        fn_set_hook('llms_get_path', $this, $path);

        return $path;
    }
}
