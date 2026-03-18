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

namespace Tygh;
use Tygh\Exceptions\InputException;

/**
 * Extension for SimpleXMLElement
 */
class ExSimpleXmlElement extends \SimpleXMLElement
{
    /**
     * Returns count of child elements
     *
     * @return int
     */
    public function exCount()
    {
        return count($this->children());
    }

    /**
     * Add CDATA text in a node
     * @param string $cdata_text The CDATA value to add
     */
    private function addCData($cdata_text)
    {
        $node= dom_import_simplexml($this);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($cdata_text));
    }

    /**
     * Create a child with CDATA value
     * @param  string             $name       The name of the child element to add.
     * @param  string             $cdata_text The CDATA value of the child element.
     * @return ExSimpleXMLElement
     */
    public function addChildCData($name, $cdata_text)
    {
        $child = $this->addChild($name);
        $child->addCData($cdata_text);

        return $child;
    }

    /**
     * Add SimpleXMLElement code into a SimpleXMLElement
     * @param  ExSimpleXMLElement $append
     * @return ExSimpleXMLElement
     */
    public function appendXML($append)
    {
        if ($append) {
            if (strlen(trim((string) $append))==0) {
                $xml = $this->addChild($append->getName());
                foreach ($append->children() as $child) {
                    $xml->appendXML($child);
                }
            } else {
                $xml = $this->addChild($append->getName(), (string) $append);
            }

            foreach ($append->attributes() as $n => $v) {
                @$xml->addAttribute($n, $v);
            }

            return $xml;
        }
    }

    /**
     * Add child from array.
     *
     * @param array     $data
     */
    public function addChildFromArray(array $data)
    {
        foreach ($data as $key => $item) {
            if (is_int($key)) {
                $key = 'item';
            }

            if (is_array($item)) {
                /** @var self $node */
                $node = $this->addChild($key);
                $node->addChildFromArray($item);
            } else {
                if ($item && is_string($item)) {
                    $this->addChildCData($key, $item);
                } else {
                    $this->addChild($key, $item);
                }
            }
        }
    }

    /**
     * Convert xml object to array.
     * Xml node attributes will be ignored.
     *
     * @return array
     */
    public function toArray()
    {
        $result = array();
        $count = $this->count();

        foreach ($this->children() as $key => $value) {
            $cnt = $this->{$key}->count();

            /** @var self $value */
            if ($value->count()) {
                $value = $value->toArray();
            } else {
                $value = (string) $value;
            }

            if ($cnt > 1) {
                if ($cnt == $count) {
                    $result[] = $value;
                } else {
                    $result[$key][] = $value;
                }
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Convert xml object to string.
     *
     * @return string
     */
    public function toString()
    {
        // prettify xml output if DOM extension is exist
        if (function_exists('dom_import_simplexml')) {
            $dom = dom_import_simplexml($this)->ownerDocument;
            $dom->formatOutput = true;
            $output = $dom->saveXML();
        } else {
            $output = $this->asXML();
        }

        return $output;
    }


    /**
     * Load xml document from xml file.
     *
     * @param string $file_path File path.
     *
     * @throws InputException
     * @return ExSimpleXmlElement
     */
    public static function loadFromFile($file_path)
    {
        if (!file_exists($file_path)) {
            throw new InputException("File not found.");
        }

        return self::loadFromString(file_get_contents($file_path));
    }

    /**
     * Load xml document from string.
     *
     * @throws InputException
     * @return ExSimpleXmlElement
     */
    public static function loadFromString($xml)
    {
        libxml_use_internal_errors(true);
        /** @var ExSimpleXmlElement $xml */
        $xml = simplexml_load_string($xml, '\Tygh\ExSimpleXmlElement', LIBXML_NOCDATA);

        if ($xml === false) {
            $errors = array();

            foreach (libxml_get_errors() as $error) {
                /** @var \LibXMLError $error */
                $errors[] = $error->message;
            }

            libxml_clear_errors();
            throw new InputException(implode(PHP_EOL, $errors));
        }

        return $xml;
    }

    /**
     * @param string $xpath Xpath
     *
     * @return $this
     */
    public function remove($xpath)
    {
        if (empty($xpath)) {
            return $this;
        }

        $elements = $this->xpath($xpath);

        if (!is_array($elements)) {
            return $this;
        }

        foreach ($elements as $target) {
            $dom = dom_import_simplexml($target);

            $dom && $dom->parentNode && $dom->parentNode->removeChild($dom);
        }

        return $this;
    }
}
