<?php
namespace SalmonDE\Tasks;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;
use pocketmine\utils\Utils;

class QueryServerListTask extends AsyncTask
{

    public function __construct(Array $data, Array $lines){
        $this->data = $data;
        $this->lines = $lines;
    }

    public function onRun(){
        $information = trim(Utils::getURL('https://minecraftpocket-servers.com/api/?object=servers&element=voters&key='.$this->data['Key'].'&month=current&format=json&limit='.$this->data['Amount']));
        if($information !== 'Error: server key not found' || $information !== 'Error: no server key'){
            $information = json_decode($information, true);
            if(isset($information['voters'])){
                $text[] = TF::DARK_GREEN.$this->lines['Header'];
                foreach($information['voters'] as $voter){
                    $text[$voter['nickname']] = TF::GOLD.str_replace(['{player}', '{votes}'], [$voter['nickname'], $voter['votes']], $this->lines['Text']);
                }
                $text = implode("\n", $text);
                $this->setResult($text);
            }else{
                $this->setResult(false);
                var_dump($information);
            }
        }else{
            $this->setResult(false);
            echo($information);
        }
    }

    public function onCompletion(Server $server){
        $plugin = $server->getPluginManager()->getPlugin('TopVoter');
        if($this->getResult()){
            $plugin->particle->setTitle($this->getResult());
            foreach($server->getOnlinePlayers() as $player){
                $player->getLevel()->addParticle($plugin->particle, [$player]);
            }
        }else{
            $plugin->getLogger()->error('Invalid Response!');
        }
    }
}
