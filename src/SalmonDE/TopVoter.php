<?php
namespace SalmonDE;

use pocketmine\event\Listener;
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

    public function onEnable(){
        $this->saveResource('config.yml');
        $pos = $this->getConfig()->get('Pos');
        if(!isset($this->particle)){
            $this->particle = new FloatingTextParticle(new Vector3($pos['X'], $pos['Y'], $pos['Z']), '', TF::DARK_GREEN.TF::BOLD.$this->getConfig()->get('Header'));
        }
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new UpdateVotesTask($this), $this->getConfig()->get('Update-Interval') * 20);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getScheduler()->scheduleAsyncTask(new CheckVersionTask($this));
    }

    public function onJoin(PlayerJoinEvent $event){
        $event->getPlayer()->getLevel()->addParticle($this->particle, [$event->getPlayer()]);
    }

    public function update(){
			$this->getServer()->getScheduler()->scheduleTask(new UpdaterTask($this, $this->getDescription()->getVersion()));
	  }
}
