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

namespace Tygh\Api;

/**
 * Formats manager
 */
class FormatManager
{
    protected static $instance = null;

    /**
     * Instances of formatter objects
     *
     * @var array $format_objects
     */
    protected $format_objects =  array();

    /**
     * Available mime types. Each key in array is mime type name, value - format name
     *
     * @var array $available_mime_types
     */
    protected $available_mime_types = array();

    public static function instance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Initiste static instance of class
     *
     * @param  array         $formats Formats will be used
     * @return FormatManager
     */
    public static function initiate($formats)
    {
        $manager = self::instance();
        $manager->loadFormatters($formats);

        return $manager;
    }

    /**
     * Initiate FormatsManager by creating format objets
     *
     * @return FormatManager
     */
    protected function __construct() {}

    /**
     * Loads format objects and fill available mime types
     *
     * @param  array $formats Formats will be used
     * @return void
     */
    public function loadFormatters($formats)
    {
        foreach ($formats as $format_name) {
            $class_name = $this->getFormatterClassName($format_name);

            if (class_exists($class_name)) {
                $this->format_objects[$format_name] = new $class_name;

                $mime_types = $this->format_objects[$format_name]->getMimetypes();

                if (is_array($mime_types)) {
                    foreach ($mime_types as $mime_type) {
                        $this->available_mime_types[$mime_type] = $format_name;
                    }
                } else {
                    $this->available_mime_types[$mime_types] = $format_name;
                }
            }
        }
    }

    protected function getFormatterClassName($format_name)
    {
        return 'Tygh\\Api\\Formats\\' . ucfirst($format_name);
    }

    /**
     * Encodes $data in the format described by $mime_type
     *
     * @param  array       $data      Data to encode
     * @param  string      $mime_type HTTP mime type
     * @return string|bool Encoded data on success, false otherwise
     */
    public function encode($data, $mime_type)
    {
        if ($this->isMimeTypeSupported($mime_type)) {
            $format_name = $this->available_mime_types[$mime_type];

            return $this->format_objects[$format_name]->encode($data);
        }

        return false;
    }

    /**
     * Decodes $data from the format described by $mime_type
     *
     * @param  string     $data      Data to decode
     * @param  string     $mime_type HTTP mime type
     * @return array|bool Decoded data on success, false otherwise
     */
    public function decode($data, $mime_type)
    {
        if ($this->isMimeTypeSupported($mime_type)) {
            $format_name = $this->available_mime_types[$mime_type];

            return $this->format_objects[$format_name]->decode($data);
        }

        return false;
    }

    public function isMimeTypeSupported($mime_type)
    {
        return isset($this->available_mime_types[$mime_type]);
    }
}
