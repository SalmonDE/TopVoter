<?php
namespace SalmonDE;

use pocketmine\event\Listener;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;
use SalmonDE\Tasks\UpdateVotesTask;
use SalmonDE\Updater\CheckVersionTask;
use SalmonDE\Updater\UpdaterTask;

class TopVoter extends PluginBase implements Listener
{

    private static $instance = null;
    private $voters = [];
    public $particle;
    public $worlds = [];

    public function onEnable(){
        self::$instance = $this;
        $this->saveResource('config.yml');
        if(!isset($this->particle)){
            $pos = $this->getConfig()->get('Pos');
            $this->particle = new FloatingTextParticle(new Vector3($pos['X'], $pos['Y'], $pos['Z']), '', TF::DARK_GREEN.TF::BOLD.$this->getConfig()->get('Header'));
        }
        $this->worlds = $this->getConfig()->get('Worlds');
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new UpdateVotesTask($this), $this->getConfig()->get('Update-Interval') * 20);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getScheduler()->scheduleAsyncTask(new CheckVersionTask($this));
    }

    public function onJoin(PlayerJoinEvent $event){
        if(in_array($event->getPlayer()->getLevel()->getName(), $this->worlds)){
            $event->getPlayer()->getLevel()->addParticle($this->particle, [$event->getPlayer()]);
        }
    }

    public function onLevelChange(EntityLevelChangeEvent $event){
        if($event->getEntity() instanceof Player){
            if(!in_array($event->getTarget()->getName(), $this->worlds)){
                $this->particle->setInvisible();
                $event->getTarget()->addParticle($this->particle, [$event->getEntity()]);
            }else{
                $this->particle->setInvisible(false);
                $event->getTarget()->addParticle($this->particle, [$event->getEntity()]);
            }
        }
    }

    public function setVoters(array $voters){
        $this->voters = $voters;
    }

    public function getVoters(){
        return $this->voters;
    }

    public static function getInstance(){
        return self::$instance;
    }

    public function update(){
		    $this->getServer()->getScheduler()->scheduleTask(new UpdaterTask($this, $this->getDescription()->getVersion()));
	  }
}
