<?php
namespace SalmonDE\TopVoter;

use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\player\PlayerJoinEvent;

class EventListener implements Listener
{

    public function onJoin(PlayerJoinEvent $event){
        $inst = TopVoter::getInstance();
        if(in_array($event->getPlayer()->getLevel()->getName(), $inst->worlds)){
            $inst->sendParticle([$event->getPlayer()]);
        }
    }

    public function onLevelChange(EntityLevelChangeEvent $event){
        $inst = TopVoter::getInstance();
        if($event->getEntity() instanceof Player){
            if(!in_array($event->getTarget()->getName(), $inst->worlds)){
                $inst->removeParticle([$event->getEntity()]);
            }elseif(!in_array($event->getFrom()->getName(), $inst->worlds)){
                $inst->sendParticle([$event->getEntity()]);
            }
        }
    }
}
