<?php

namespace Database;

use System\Configuration;
use \PDO;

class Database
{
    public static $instance;


    public static function get_database()
    {
        $config = Configuration::getConfigElement();
        if(isset($config->database))
        {
            self::$instance = new PDO('mysql:host='.$config->database->hostname.';dbname='.$config->database->dbname, $config->database->username, $config->database->password);
            return self::$instance;
        }
    }

    public static function updateModule($moduleName)
    {
        $bdd = Database::get_database();
        $update = $bdd->prepare("UPDATE session_backdoor SET current_module = :module_name, module_session = ''");
        $update->bindParam('module_name', $moduleName);
        $update->execute();
        return true;
    }

    public static function saveModule($object)
    {
        $object = serialize($object);
        $bdd = Database::get_database();
        $update = $bdd->prepare("UPDATE session_backdoor SET module_session = :module_session");
        $update->bindParam(':module_session',$object);
        $update->execute();
        return true;
    }

    public static function resetFramework()
    {
        $bdd = Database::get_database();
        $update = $bdd->prepare("UPDATE session_backdoor SET module_session = '', current_module = '',backdoor_selected = 'master'");
        $update->execute();
        return true;
    }

    public static function setProxy($host, $port)
    {
        if($host != '' && $port != '')
            if(!Configuration::checkProxy($host, $port))
                return false;
        $bdd = Database::get_database();
        $update = $bdd->prepare("UPDATE gshark_settings SET proxy_host = :proxy_host,proxy_port = :proxy_port");
        $update->bindParam('proxy_host', $host);
        $update->bindParam('proxy_port', $port);
        $update->execute();
        return true;
    }

    public static function getProxy()
    {
        $bdd = Database::get_database();
        $select = $bdd->prepare("SELECT proxy_host, proxy_port FROM gshark_settings");
        $select->execute();
        if($select->rowCount() > 0) {
            $fetch = $select->fetch(PDO::FETCH_OBJ);
            if(!empty($fetch->proxy_host) && !empty($fetch->proxy_port))
                return $fetch;
            return false;
        }
        else
            return false;
    }

    public static function getAutogenerate()
    {
        $bdd = Database::get_database();
        $select = $bdd->prepare("SELECT auto_generate FROM gshark_settings");
        $select->execute();
        if($select->rowCount() > 0) {
            $fetch = $select->fetch(PDO::FETCH_OBJ);
            if(!empty($fetch->auto_generate))
                return $fetch;
            return false;
        }
        else
            return false;
    }

    public static function setAutogenerate($argument)
    {
        if(strtolower($argument) != "activate" and strtolower($argument) != "disalow")
            return false;
        $bdd = Database::get_database();
        $select = $bdd->prepare("UPDATE gshark_settings SET auto_generate = :auto_generate");
        $select->bindParam(':auto_generate', $argument);
        $select->execute();
        return true;
    }

    public static function getCurrentModuleSession()
    {
        $bdd = Database::get_database();
        $select = $bdd->prepare("SELECT module_session FROM session_backdoor");
        $select->execute();
        $fetch = $select->fetch(PDO::FETCH_OBJ);
        if($fetch->module_session && $fetch->module_session != "")
        {
            return unserialize($fetch->module_session);
        }
        return false;
    }

    public static function backdoor_selected()
    {
        $bdd = Database::get_database();
        $select = $bdd->prepare("SELECT backdoor_selected FROM session_backdoor");
        $select->execute();
        if($select->rowCount() > 0)
            return $select->fetch(PDO::FETCH_OBJ)->backdoor_selected;
        return 'master';
    }

    public static function getCurrentModuleName()
    {
        $bdd = Database::get_database();
        $select = $bdd->prepare("SELECT current_module FROM session_backdoor");
        $select->execute();
        $fetch = $select->fetch(PDO::FETCH_OBJ);
        if($fetch->current_module && $fetch->current_module != "")
        {
            return $fetch->current_module;
        }
        return false;
    }

    public static function addBackdoorToList($backdoorUrl)
    {
        $bdd = Database::get_database();
        $select = $bdd->prepare("SELECT id_backdoor FROM backdoor_list WHERE backdoor_url = :backdoor_url");
        $select->bindParam(':backdoor_url', $backdoorUrl);
        $select->execute();
        if($select->rowCount() > 0)
        {
            $fetch = $select->fetch(PDO::FETCH_OBJ);
            if($fetch->id_backdoor != null)
                return false;
        }
        $string = str_shuffle("AZERTYUIOPQSDFKLMWXCVBNazertyuiopqsdfgklmwxcvbn1234567890");
        $identifier = substr($string,0,5);
        $identifier .= mt_rand(0,999999);
        $current_date = date('d-m-Y');
        $insert = $bdd->prepare("INSERT INTO backdoor_list(backdoor_identifier,backdoor_url,created_at) VALUES(:backdoor_identifier,:backdoor_url,:created_at)");
        $insert->bindParam(':backdoor_identifier', $identifier);
        $insert->bindParam(':backdoor_url', $backdoorUrl);
        $insert->bindParam(':created_at',$current_date);
        $insert->execute();
        return true;
    }

    public static function getBackdoorList()
    {
        $bdd = Database::get_database();
        $select = $bdd->prepare("SELECT * FROM backdoor_list");
        $select->execute();
        if($select->rowCount() > 0)
            return $select->fetchAll(PDO::FETCH_OBJ);
        return false;
    }

    public static function insertBackdoorSelected($identifier)
    {
        $bdd = Database::get_database();
        if($identifier != 'master')
        {
            $select = $bdd->prepare("SELECT * FROM backdoor_list WHERE backdoor_identifier = :backdoor_identifier");
            $select->bindParam(':backdoor_identifier', $identifier);
            $select->execute();
            if($select->rowCount() < 1)
                return false;
        }
        $update = $bdd->prepare("UPDATE session_backdoor SET backdoor_selected = :backdoor_selected");
        $update->bindParam(':backdoor_selected', $identifier);
        $update->execute();
        return true;
    }

    public static function getCurrentBackdoorInformation($identifier)
    {
        $bdd = Database::get_database();
        $select = $bdd->prepare("SELECT * FROM backdoor_list WHERE backdoor_identifier = :backdoor_identifier");
        $select->bindParam(':backdoor_identifier', $identifier);
        $select->execute();
        if($select->rowCount() < 1)
            return false;
        else
            return $select->fetch(PDO::FETCH_OBJ);
    }

    public static function removeBackdoor($identifier)
    {

        if(Database::getCurrentBackdoorInformation($identifier))
        {
            $bdd = Database::get_database();
            $remove = $bdd->prepare("DELETE FROM backdoor_list WHERE backdoor_identifier = :backdoor_identifier");
            $remove->bindParam(':backdoor_identifier', $identifier);
            $remove->execute();
            return true;
        }
        return false;
    }
}