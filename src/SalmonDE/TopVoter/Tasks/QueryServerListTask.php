<?php
namespace SalmonDE\TopVoter\Tasks;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;
use pocketmine\utils\Utils;
use SalmonDE\TopVoter\TopVoter;

class QueryServerListTask extends AsyncTask
{

    public function __construct(array $data){
        $this->data = $data;
    }

    public function onRun(){
        try{
            $raw = Utils::getURL('https://minecraftpocket-servers.com/api/?object=servers&element=voters&month=current&format=json&limit='.$this->data['Amount'].'&key='.$this->data['Key']);
            $info = json_decode($raw, true);
            if(!is_array($info)){
                throw new \Exception('Couldn\'t process data! No array was returned!');
            }
            if(!isset($info['voters'])){
                $info['voters'] = [];
            }
            $this->setResult(['success' => true, 'voters' => $info['voters']]);
        }catch(\Exception $e){
            $this->setResult(['success' => false, 'error' => $e, 'response' => $raw]);
        }
    }

    public function onCompletion(Server $server){
        $inst = TopVoter::getInstance();
        if($this->getResult()['success'] === true){
            $inst->setVoters($this->getResult()['voters']);
            $inst->updateParticle();
        }else{
            $inst->getLogger()->warning('Error while processing data from the serverlist!');
            $inst->getLogger()->error($this->getResult()['error']->getMessage());
            $inst->getLogger()->error('Raw: '.$this->getResult()['response']);
        }
    }
}
