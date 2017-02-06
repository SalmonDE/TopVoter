<?php
namespace SalmonDE\Tasks;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;
use pocketmine\utils\Utils;
use SalmonDE\TopVoter;

class QueryServerListTask extends AsyncTask
{

    public function __construct(Array $data, Array $lines){
        $this->data = $data;
        $this->lines = $lines;
    }

    public function onRun(){
        try{
            $raw = Utils::getURL('https://minecraftpocket-servers.com/api/?object=servers&element=voters&key='.$this->data['Key'].'&month=current&format=json&limit='.$this->data['Amount']);
            $info = json_decode($raw, true);
            if(!is_array($info)){
                throw new \Exception('Couldn\'t process data! No array was returned!');
            }
            if(!isset($info['voters'])){
                $info['voters'] = [];
            }
            $text[] = TF::DARK_GREEN.$this->lines['Header'];
            foreach($info['voters'] as $voter){
                $text[$voter['nickname']] = TF::GOLD.str_replace(['{player}', '{votes}'], [$voter['nickname'], $voter['votes']], $this->lines['Text']);
            }
            $text = implode("\n", $text);
            $this->setResult(['success' => true, 'text' => $text, 'voters' => $info['voters']]);
        }catch(\Exception $e){
            $this->setResult(['success' => false, 'error' => $e, 'response' => $raw]);
        }
    }

    public function onCompletion(Server $server){
        if($this->getResult()['success'] === true){
            TopVoter::getInstance()->setVoters($this->getResult()['voters']);
            TopVoter::getInstance()->particle->setTitle($this->getResult()['text']);
            TopVoter::getInstance()->particle->setInvisible(false);
            foreach($server->getOnlinePlayers() as $player){
                if(in_array($player->getLevel()->getName(), TopVoter::getInstance()->worlds)){
                    $player->getLevel()->addParticle(TopVoter::getInstance()->particle, [$player]);
                }
            }
        }else{
            TopVoter::getInstance()->getLogger()->warning('Error while processing data from the serverlist!');
            TopVoter::getInstance()->getLogger()->error($this->getResult()['error']->getMessage());
            TopVoter::getInstance()->getLogger()->error('Raw: '.$this->getResult()['response']);
        }
    }
}
