<?php

namespace Interfaces;

Class Boot
{
    public static function register(){
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    public static function autoload($class){

        $parts = preg_split('#\\\#', $class);

        $className = array_pop($parts);

        $path = implode(DS, $parts);
        $file = $className.'.php';

        $filepath = ROOT.$path.DS.ucfirst($file);

        require $filepath;
    }
}