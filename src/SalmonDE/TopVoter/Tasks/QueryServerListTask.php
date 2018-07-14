<?php
declare(strict_types = 1);

namespace SalmonDE\TopVoter\Tasks;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Utils;

class QueryServerListTask extends AsyncTask {

    private $key;
    private $amount;

    public function __construct(string $key, int $amount){
        $this->key = $key;
        $this->amount = $amount;
    }

    public function onRun(): void{
        $err = '';
        $raw = Utils::getURL('https://minecraftpocket-servers.com/api/?object=servers&element=voters&month=current&format=json&limit='.$this->amount.'&key='.$this->key, 10, [], $err);

        if(strpos($raw, 'Error:') !== false){
            $err = trim(str_replace('Error:', '', $raw));
        }

        if($err === ''){
            $data = json_decode($raw, true);

            if(is_array($data)){
                $this->setResult(['success' => true, 'voters' => $data['voters']]);
            }
        }else{
            $this->setResult(['success' => false, 'error' => $err, 'response' => empty($raw) ? 'null' : $raw]);
        }
    }

    public function onCompletion(Server $server){
        $topVoter = $server->getPluginManager()->getPlugin('TopVoter');

        if($topVoter->isDisabled()){
            return;
        }

        if($this->getResult()['success']){
            if($topVoter->getVoters() !== $this->getResult()['voters']){
                $topVoter->setVoters($this->getResult()['voters']);
                $topVoter->updateParticles();
                $topVoter->sendParticles();
            }
        }else{
            $topVoter->getLogger()->warning('Error while processing data from the serverlist!');
            $topVoter->getLogger()->error('Error: '.$this->getResult()['error']);
            $topVoter->getLogger()->debug('Raw: '.$this->getResult()['response']);

            if($this->getResult()['error'] === 'no server key' || $this->getResult()['error'] === 'invalid server key'){
                $topVoter->getUpdateTask()->unsetKey();
                $server->getPluginManager()->disablePlugin($topVoter);
            }
        }
    }
}
