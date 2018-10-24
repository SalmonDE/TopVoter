<?php
declare(strict_types = 1);

namespace SalmonDE\TopVoter\Tasks;

use pocketmine\Player;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Internet;
use SalmonDE\TopVoter\Events\DataUpdateEvent;

class QueryServerListTask extends AsyncTask {

    private $key;
    private $amount;

    public function __construct(string $key, int $amount){
        $this->key = $key;
        $this->amount = $amount;
    }

    public function onRun(): void{
        $err = '';
        $raw = Internet::getURL('https://minecraftpocket-servers.com/api/?object=servers&element=voters&month=current&format=json&limit='.$this->amount.'&key='.$this->key, 10, [], $err);

        if($err === ''){
            $data = json_decode($raw, true);

            if(is_array($data)){
                $this->setResult(['success' => true, 'voters' => $data['voters']]);
            }
        }else{
            $this->setResult(['success' => false, 'error' => $err, 'response' => empty($raw) ? 'null' : $raw]);
            return;
        }

        if(strpos($raw, 'Error:') !== false){
            $err = trim(str_replace('Error:', '', $raw));
        }
    }

    public function onCompletion(Server $server){
        $topVoter = $server->getPluginManager()->getPlugin('TopVoter');

        if($topVoter->isDisabled()){
            return;
        }

        if($this->getResult()['success']){
            $voters = $this->getResult()['voters'];

            if($topVoter->getConfig()->get('Check-Name', true)){
                foreach($voters as $index => $voteData){
                    if(!Player::isValidUsername($voteData['nickname'])){
                        unset($voters[$index]);
                    }
                }
            }

            $topVoter->getServer()->getPluginManager()->callEvent($event = new DataUpdateEvent($topVoter, $voters));

            if(!$event->isCancelled() && $topVoter->getVoters() !== $event->getVoteData()){
                $topVoter->setVoters($event->getVoteData());
                $topVoter->updateParticles();
                $topVoter->sendParticles();
            }
        }else{
            $topVoter->getLogger()->warning('Error while processing data from the serverlist!');
            $topVoter->getLogger()->error('Error: '.$this->getResult()['error']);
            $topVoter->getLogger()->debug('Raw: '.$this->getResult()['response']);

            if($this->getResult()['error'] === 'no server key' || $this->getResult()['error'] === 'invalid server key'){
                $topVoter->getUpdateTask()->unsetKey();
                $topVoter->getServer()->getPluginManager()->disablePlugin($topVoter);
            }
        }
    }
}
