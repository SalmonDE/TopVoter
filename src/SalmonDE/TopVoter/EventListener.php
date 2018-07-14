<?php
declare(strict_types = 1);

namespace SalmonDE\TopVoter;

use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;

class EventListener implements Listener {

    private $plugin;

    public function __construct(TopVoter $plugin){
        $this->plugin = $plugin;
    }

    public function onJoin(PlayerJoinEvent $event){
        $this->plugin->sendParticles($event->getPlayer()->getLevel(), [$event->getPlayer()]);
    }

    /**
    * @priority MONITOR
    * @ignoreCancelled true
    */
    public function onLevelChange(EntityLevelChangeEvent $event){
        if($event->getEntity() instanceof Player){
            $this->plugin->removeParticles($event->getOrigin(), [$event->getEntity()]);
            $this->plugin->sendParticles($event->getTarget(), [$event->getEntity()]);
        }
    }
}
