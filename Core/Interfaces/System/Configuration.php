<?php

namespace System;

use Database\Database;

class Configuration
{
    public static $setElement = [
        'proxy' => 'setProxy',
        'autogenerate' => 'setAutogenerate',
    ];

    public static $getElement = [
        'proxy' => 'getProxy',
        'autogenerate' => 'getAutogenerate',
    ];

    public static $removeElement = [
        'proxy' => 'removeProxy',
    ];

    public static function getConfigElement()
    {
        if(file_exists(__DIR__."/../../Config/config.json"))
        {
            return json_decode(file_get_contents(__DIR__."/../../Config/config.json"));
        }
        die('Error: can\'t load configuration file');
    }

    public static function getAutogenerate()
    {
        $autogenerate = Database::getAutogenerate();
        if($autogenerate !== false)
        {
            if($autogenerate->auto_generate == 'disalow')
            {
                return 'autogenerate: *disalow*';
            }
            return 'autogenerate: *activate*';
        }
        else
        {
            return 'Can\'t find autogenerate config';
        }
    }

    public static function setAutogenerate($argument)
    {
        if(count($argument) < 3)
            return 'Invalid argument please use /config set autogenerate activate/disalow';
        else
        {
            $return = Database::setAutogenerate($argument[2]);
            if($return)
                return 'autogenerate => '.$argument[2];
            else
                return 'Please enter correct value ( activate or disalow )';
        }
    }

    public static function setProxy($argument)
    {
        if(count($argument) < 3)
            return 'Invalid argument please use /config set proxy <host> <port>';
        else
        {
            $return = Database::setProxy($argument[2], $argument[3]);
            if($return)
                return 'Argument setup with *Host:* '.$argument[2].' *Port:*'.$argument[3];
            else
                return 'Proxy seem be down...';
        }
    }

    public static function getProxy()
    {
        $proxy = Database::getProxy();
        if($proxy === false)
            return 'Proxy as not configured';
        else
            return '*host:* '.$proxy->proxy_host.' *port:* '.$proxy->proxy_port;
    }

    public static function checkProxy($host, $port)
    {
        $timeout = 5;
        if($con = @fsockopen($host, $port, $errorNumber, $errorMessage, $timeout))
        {
            return true;
        } else {
            return false;
        }
    }

    public static function removeProxy()
    {
        $return = Database::setProxy('','');
        if($return !== false)
            return true;
        else
            return false;
    }
}