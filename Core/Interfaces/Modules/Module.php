<?php

namespace Modules;

use Classes\GlobalAction;
use Database\Database;
use System\Configuration;

class Module
{
    /*
     * Master module class
     */

    protected $target;
    protected $chat_id;
    // Payload is base64 php code executed on backdoor
    protected $payload;
    protected $module_information = [
        'name'          => '',
        'description'   => '',
        'author'        => '',
        'type'          => '',
    ];
    protected $fields = [];
    protected $fieldsRules = [
        'required' => [],
        'optional' => [],
    ];
    protected $configuration;
    protected $bdd;

    protected function setConfiguration()
    {
        $this->configuration = Configuration::getConfigElement();
    }

    protected function setDatabase()
    {
        $this->bdd = Database::get_database();
    }

    public function getArgument($name)
    {
        foreach ($this->fields as $field) {
            if (is_array($field)) {
                foreach ($field as $f) {
                    if ($f['name'] == $name) {
                        return $f['value'];
                    }
                }
            }
        }
        return false;
    }

    public function setArgument($name, $value)
    {
        foreach ($this->fields as $key => $field)
        {
            if(is_array($field))
            {
                foreach ($field as $key2 => $f)
                {
                    if($f['name'] == $name)
                    {
                        $this->fields[$key][$key2]['value'] = $value;
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function getPayload($type)
    {
        if($this->payload != "")
        {
            if($type == "decode")
            {
                return "```php ".base64_decode($this->payload)." ```";
            }
            elseif($type == "raw")
            {
                return "```php ".$this->payload." ```";
            }
        }
        else
            return false;
    }

    public function showArgument()
    {
        $array = [];
        if(count($this->fields) > 0)
        {
            foreach ($this->fields as $field)
            {
                if(is_array($field))
                {
                    foreach ($field as $f)
                    {
                        if(in_array($f['name'],$this->fieldsRules['required']))
                        {
                            $type = "*required*";
                        }
                        else
                        {
                            $type = "_optional_";
                        }
                        $element = $f['name']." ".$type;
                        $array[] = $element;
                    }
                }
            }
        }
        return $array;
    }

    protected function addField($field, $type = "optional")
    {
        if(!isset($this->fields[$field]))
        {
            $this->fields[] = [$field => ['name' => $field, 'value' => '']];
            array_push($this->fieldsRules[$type], $field);
            return true;
        }
        return false;
    }

    protected function getOneField($name)
    {
        foreach ($this->fields as $field)
        {
            if(is_array($field))
            {
                foreach ($field as $f)
                {
                    if($f['name'] == $name)
                        return $f['value'];
                }
            }
        }
        return false;
    }

    protected function getAllFields()
    {
        if(isset($this->fields) && count($this->fields) > 0)
        {
            return $this->fields;
        }
        return false;
    }

    protected function setRule($fieldName, $type = 'optional')
    {
        foreach ($this->fields as $field)
        {
            if(is_array($field))
            {
                foreach ($field as $item) {
                    if($item['name'] == $fieldName)
                    {
                        array_push($this->fieldsRules[$type], $fieldName);
                        return true;
                    }

                }
            }
        }
        return false;
    }

    protected function getRules($fieldName)
    {
        if(isset($this->fields[$fieldName]))
        {
            if(isset($this->fieldsRules['required'][$fieldName]))
            {
                return 'required';
            }
            return 'optional';
        }
        return false;
    }

    protected function setTarget($value)
    {
        if(isset($this->target))
        {
            $this->target = $value;
            return true;
        }
        return false;
    }
    protected function setInformation($name, $value)
    {
        if(isset($this->module_information[$name]))
        {
            $this->module_information[$name] = $value;
            return true;
        }
        return false;
    }

    protected function getInformation($name)
    {
        if(isset($this->module_information[$name]))
            return $this->module_information[$name];
        return false;
    }
}