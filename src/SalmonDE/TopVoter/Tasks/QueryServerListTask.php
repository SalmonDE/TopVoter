<?php
namespace SalmonDE\TopVoter\Tasks;

use pocketmine\scheduler\AsyncTask;
use SalmonDE\TopVoter\TopVoter;

class QueryServerListTask extends AsyncTask
{

    public function __construct(array $data){
        $this->data = $data;
    }

    public function onRun(){
        $success = true;
        $err = '';

        $raw = \pocketmine\utils\Utils::getURL('https://minecraftpocket-servers.com/api/?object=servers&element=voters&month=current&format=json&limit='.$this->data['Amount'].'&key='.$this->data['Key'], 10, [], $err);

        if($err !== ''){
            $this->setResult(['success' => false, 'error' => $err, 'response' => empty($raw) === false ? $raw : 'null']);
            $success = false;
        }

        $data = json_decode($raw, true);

        if(!is_array($data) || empty($data)){
            $this->setResult(['success' => false, 'error' => $e, 'response' => empty($raw) === false ? $raw : 'null']);
            $success = false;
        }

        if($success){
            $this->setResult(['success' => true, 'voters' => $data['voters']]);
        }
    }

    public function onCompletion(\pocketmine\Server $server){
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
        }
    }
}
