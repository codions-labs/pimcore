<?php
/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in 
 * LICENSE.md which is distributed with this source code.
 *
 * @category   Pimcore
 * @package    Object|Class
 * @copyright  Copyright (c) 2009-2016 pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Model\Object\ClassDefinition\Data;

use Pimcore\Model;
use Pimcore\Model\Object;

class Multiselect extends Model\Object\ClassDefinition\Data
{

    /**
     * Static type of this element
     *
     * @var string
     */
    public $fieldtype = "multiselect";

    /**
     * Available options to select
     *
     * @var array
     */
    public $options;

    /**
     * @var integer
     */
    public $width;

    /**
     * @var integer
     */
    public $height;

    /**
     * Type for the column to query
     *
     * @var string
     */
    public $queryColumnType = "text";

    /**
     * Type for the column
     *
     * @var string
     */
    public $columnType = "text";

    /**
     * Type for the generated phpdoc
     *
     * @var string
     */
    public $phpdocType = "array";

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return void
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param array $width
     * @return void
     */
    public function setWidth($width)
    {
        $this->width = $this->getAsIntegerCast($width);
        return $this;
    }

    /**
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param array $height
     * @return void
     */
    public function setHeight($height)
    {
        $this->height = $this->getAsIntegerCast($height);
        return $this;
    }

    /**
     * @see Object\ClassDefinition\Data::getDataForResource
     * @param string $data
     * @param null|Model\Object\AbstractObject $object
     * @param mixed $params
     * @return string
     */
    public function getDataForResource($data, $object = null, $params = array())
    {
        if (is_array($data)) {
            return implode(",", $data);
        }
    }

    /**
     * @see Object\ClassDefinition\Data::getDataFromResource
     * @param string $data
     * @param null|Model\Object\AbstractObject $object
     * @param mixed $params
     * @return string
     */
    public function getDataFromResource($data, $object = null, $params = array())
    {
        if (strlen($data)) {
            return explode(",", $data);
        } else {
            return null;
        }
    }

    /**
     * @see Object\ClassDefinition\Data::getDataForQueryResource
     * @param string $data
     * @param null|Model\Object\AbstractObject $object
     * @param mixed $params
     * @return string
     */
    public function getDataForQueryResource($data, $object = null, $params = array())
    {
        if (!empty($data) && is_array($data)) {
            return ",".implode(",", $data).",";
        }
        return;
    }


    /**
     * @see Object\ClassDefinition\Data::getDataForEditmode
     * @param string $data
     * @param null|Model\Object\AbstractObject $object
     * @param mixed $params
     * @return string
     */
    public function getDataForEditmode($data, $object = null, $params = array())
    {
        if (is_array($data)) {
            return implode(",", $data);
        }
    }

    /**
     * @see Model\Object\ClassDefinition\Data::getDataFromEditmode
     * @param string $data
     * @param null|Model\Object\AbstractObject $object
     * @param mixed $params
     * @return string
     */
    public function getDataFromEditmode($data, $object = null, $params = array())
    {
        if (\Pimcore\Tool\Admin::isExtJS6()) {
            return $data;
        } elseif (strlen($data)) {
            return explode(",", $data);
        }
        return null;
    }

    /**
     * @see Object\ClassDefinition\Data::getVersionPreview
     * @param string $data
     * @param null|Object\AbstractObject $object
     * @param mixed $params
     * @return string
     */
    public function getVersionPreview($data, $object = null, $params = array())
    {
        if (is_array($data)) {
            return implode(",", $data);
        }
    }

    /**
     * Checks if data is valid for current data field
     *
     * @param mixed $data
     * @param boolean $omitMandatoryCheck
     * @throws \Exception
     */
    public function checkValidity($data, $omitMandatoryCheck = false)
    {
        if (!$omitMandatoryCheck and $this->getMandatory() and empty($data)) {
            throw new \Exception("Empty mandatory field [ ".$this->getName()." ]");
        }

        if (!is_array($data) and !empty($data)) {
            throw new \Exception("Invalid multiselect data");
        }
    }

    /**
     * converts object data to a simple string value or CSV Export
     * @abstract
     * @param Object\AbstractObject $object
     * @param array $params
     * @return string
     */
    public function getForCsvExport($object, $params = array())
    {
        $data = $this->getDataFromObjectParam($object, $params);
        if (is_array($data)) {
            return implode(",", $data);
        } else {
            return null;
        }
    }

    /**
     * @param $importValue
     * @param null|Model\Object\AbstractObject $object
     * @param mixed $params
     * @return array|mixed
     */
    public function getFromCsvImport($importValue, $object = null, $params = array())
    {
        return explode(",", $importValue);
    }

    /**
     * returns sql query statement to filter according to this data types value(s)
     * @param  $value
     * @param  $operator
     * @return string
     */
    public function getFilterCondition($value, $operator)
    {
        return $this->getFilterConditionExt($value, $operator, array(
                "name" => $this->name)
        );
    }

    /**
     * returns sql query statement to filter according to this data types value(s)
     * @param  $value
     * @param  $operator
     * @param  $params optional params used to change the behavior
     * @return string
     */
    public function getFilterConditionExt($value, $operator, $params = array()) {
        if ($operator == "=") {
            $name = $params["name"] ? $params["name"] : $this->name;
            $value = "'%".$value."%'";
            return "`".$name."` LIKE ".$value." ";
        }
    }

    /** True if change is allowed in edit mode.
     * @param string $object
     * @param mixed $params
     * @return bool
     */
    public function isDiffChangeAllowed($object, $params = array())
    {
        return true;
    }

    /** Generates a pretty version preview (similar to getVersionPreview) can be either html or
     * a image URL. See the ObjectMerger plugin documentation for details
     * @param $data
     * @param null $object
     * @param mixed $params
     * @return array|string
     */
    public function getDiffVersionPreview($data, $object = null, $params = array())
    {
        if ($data) {
            $map = array();
            foreach ($data as $value) {
                $map[$value] = $value;
            }

            $html = "<ul>";

            foreach ($this->options as $option) {
                if ($map[$option["value"]]) {
                    $value = $option["key"];
                    $html .= "<li>" . $value . "</li>";
                }
            }

            $html .= "</ul>";

            $value = array();
            $value["html"] = $html;
            $value["type"] = "html";
            return $value;
        } else {
            return "";
        }
    }

    /**
     * @param Object\ClassDefinition\Data $masterDefinition
     */
    public function synchronizeWithMasterDefinition(Object\ClassDefinition\Data $masterDefinition)
    {
        $this->options = $masterDefinition->options;
    }
}
