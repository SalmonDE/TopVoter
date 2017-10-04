<?php
namespace SalmonDE\TopVoter\Tasks;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Utils;

class QueryServerListTask extends AsyncTask {

    public function __construct(array $data){
        $this->data = $data;
    }

    public function onRun(){
        $success = true;
        $err = '';

        $raw = Utils::getURL('https://minecraftpocket-servers.com/api/?object=servers&element=voters&month=current&format=json&limit='.$this->data['Amount'].'&key='.$this->data['Key'], 10, [
                        ], $err);

        if(strpos($raw, 'Error:') !== false){
            $err = trim(str_replace('Error:', '', $raw));
        }

        if($err !== ''){
            $this->setResult(['success' => false, 'error' => $err, 'response' => empty($raw) === false ? $raw : 'null']);
            $success = false;
        }

        $data = json_decode($raw, true);

        if($success && (!is_array($data) || empty($data))){
            $this->setResult(['success' => false, 'error' => 'No array could be created!',
                'response' => empty($raw) === false ? $raw : 'null']);
            $success = false;
        }

        if($success){
            $this->setResult(['success' => true, 'voters' => $data['voters']]);
        }
    }

    public function onCompletion(Server $server){
        $inst = $server->getPluginManager()->getPlugin('TopVoter');

        if($inst->isDisabled()){
            return;
        }

        if($this->getResult()['success'] === true){
            if($inst->getVoters() !== $this->getResult()['voters']){
                $inst->setVoters($this->getResult()['voters']);
                $inst->updateParticle();
                $inst->sendParticle();
            }
        }else{
            $inst->getLogger()->warning('Error while processing data from the serverlist!');
            $inst->getLogger()->error('Error: '.$this->getResult()['error']);
            $inst->getLogger()->debug('Raw: '.$this->getResult()['response']);

            if($this->getResult()['error'] === 'no server key'){
                $inst->getUpdateTask()->unsetKey();
                $server->getPluginManager()->disablePlugin($inst);
            }
        }
    }

}
