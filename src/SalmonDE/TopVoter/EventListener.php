<?php
namespace SalmonDE\TopVoter;

use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\player\PlayerJoinEvent;

class EventListener implements Listener
{

    private $plugin;

    public function __construct(TopVoter $plugin){
        $this->plugin = $plugin;
    }

    public function onJoin(PlayerJoinEvent $event){
        if(in_array($event->getPlayer()->getLevel()->getName(), $this->plugin->worlds)){
            $this->plugin->sendParticle([$event->getPlayer()]);
        }
    }

    public function onLevelChange(EntityLevelChangeEvent $event){
        if(!$event->isCancelled()){
            if($event->getEntity() instanceof Player){
                if(!in_array($event->getTarget()->getName(), $this->plugin->worlds)){
                    $this->plugin->removeParticle([$event->getEntity()]);
                }else{
                    $this->plugin->sendParticle([$event->getEntity()], true);
                }
            }
        }
    }
}
