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

class TopVoter extends PluginBase implements Listener
{

    public function onEnable(){
        $this->saveResource('config.yml');
        $pos = $this->getConfig()->get('Pos');
        $this->particle = new FloatingTextParticle(new Vector3($pos['X'], $pos['Y'], $pos['Z']), '', TF::DARK_GREEN.'Voter <3 ( ͡° ͜ʖ ͡°)'."\n".'------');
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new UpdateVotesTask($this), $this->getConfig()->get('Update-Interval') * 20);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onJoin(PlayerJoinEvent $event){
        $event->getPlayer()->getLevel()->addParticle($this->particle, [$event->getPlayer()]);
    }
}
