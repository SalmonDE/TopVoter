<?php
declare(strict_types = 1);

namespace SalmonDE\TopVoter;

use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;

class EventListener implements Listener {

	private $plugin;

	public function __construct(TopVoter $plugin){
		$this->plugin = $plugin;
	}

	public function onJoin(PlayerJoinEvent $event){
		$this->plugin->sendParticles($event->getPlayer()->getWorld(), [$event->getPlayer()]);
	}

	/**
	* @priority MONITOR
	* @ignoreCancelled true
	*/
	public function onEntityTeleport(EntityTeleportEvent $event){
		if($event->getFrom()->getWorld()->getFolderName() === $event->getTo()->getWorld()->getFolderName()){
			return;
		}

		if($event->getEntity() instanceof Player){
			$this->plugin->removeParticles($event->getFrom()->getWorld(), [$event->getEntity()]);
			$this->plugin->sendParticles($event->getTo()->getWorld(), [$event->getEntity()]);
		}
	}
}
