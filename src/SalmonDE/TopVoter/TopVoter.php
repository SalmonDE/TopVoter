<?php
namespace SalmonDE\TopVoter;

use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;
use SalmonDE\TopVoter\Tasks\UpdateVotesTask;
use SalmonDE\TopVoter\Updater\CheckVersionTask;
use SalmonDE\TopVoter\Updater\UpdaterTask;

class TopVoter extends PluginBase
{

    private static $instance = null;
    private $voters = [];
    private $particle = null;
    public $worlds = [];

    public function onEnable(){
        self::$instance = $this;
        $this->saveResource('config.yml');
        $this->initParticle();
        $this->worlds = $this->getConfig()->get('Worlds');
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new UpdateVotesTask($this), (($iv = $this->getConfig()->get('Update-Interval')) > 180 ? $iv : 180) * 20);
        $this->getServer()->getScheduler()->scheduleAsyncTask(new CheckVersionTask($this));
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
    }

    private function initParticle(){
        if(!$this->particle instanceof FloatingTextParticle){
            $pos = $this->getConfig()->get('Pos');
            $this->particle = new FloatingTextParticle(new Vector3($pos['X'], $pos['Y'], $pos['Z']), '', TF::DARK_GREEN.TF::BOLD.$this->getConfig()->get('Header'));
        }
    }

    public function sendParticle(array $players = null){
        $this->particle->setInvisible(false);
        if($players === null){
            $players = $this->getServer()->getOnlinePlayers();
        }
        foreach($players as $player){
            if(!in_array($player->getLevel()->getName(), $this->worlds)){
                $player->getLevel()->addParticle($this->particle, [$player]);
            }
        }
    }

    public function removeParticle(array $players = null){
        $this->particle->setInvisible();
        if($players === null){
            $players = $this->getServer()->getOnlinePlayers();
        }
        foreach($players as $player){
            $player->getLevel()->addParticle($this->particle, [$player]);
        }
    }

    public function updateParticle() : string{
        $text[] = TF::DARK_GREEN.$this->getConfig()->get('Header');
        foreach($this->voters as $voter){
            $text .= "\n".TF::GOLD.str_replace(['{player}', '{votes}'], [$voter['nickname'], $voter['votes']], $this->getConfig()->get('Text')).TF::RESET;
        }
        $this->particle->setTitle($text);
        return $text;
    }

    public function setVoters(array $voters){
        $this->voters = $voters;
    }

    public function getVoters() : array{
        return $this->voters;
    }

    public static function getInstance() : TopVoter{
        return self::$instance;
    }

    public function update(){ // This is not part of the API!
		    $this->getServer()->getScheduler()->scheduleTask(new UpdaterTask($this, $this->getDescription()->getVersion()));
	  }
}
