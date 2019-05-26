<?php
declare(strict_types = 1);

namespace SalmonDE\TopVoter;

use pocketmine\world\World;
use pocketmine\world\Position;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;
use SalmonDE\TopVoter\Tasks\UpdateVotesTask;

class TopVoter extends PluginBase {

	private $updateTask;
	private $particles = [];

	private $voters = [];

	public function onEnable(): void{
		$this->saveResource('config.yml');
		$this->initParticles();
		$this->getScheduler()->scheduleRepeatingTask($this->updateTask = new UpdateVotesTask($this), \max(180, $this->getConfig()->get('Update-Interval')) * 20);

		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
	}

	private function initParticles(): void{
		foreach((array) $this->getConfig()->get('Positions') as $pos){
			if(($world = $this->getServer()->getWorldManager()->getWorldByName($pos['world'])) instanceof World){
				$particle = new FloatingTextParticle(new Vector3($pos['x'], $pos['y'], $pos['z']), '', $this->getConfig()->get('Header'));
				$particle->encode($particle->getVector3()); // prevent empty batch error
				$this->particles[$world->getFolderName()][] = $particle;
			}
		}
	}

	public function getParticles(): array{
		return $this->particles;
	}

	public function sendParticles(World $world = \null, array $players = \null){
		if($world === \null){
			foreach(\array_keys($this->particles) as $world){
				if(($world = $this->getServer()->getWorldManager()->getWorldByName($world)) instanceof World){
					$this->sendParticles($world);
				}
			}

			return;
		}

		if($players === \null){
			$players = $world->getPlayers();
		}

		foreach($this->particles[$world->getFolderName()] ?? [] as $particle){
			$particle->setInvisible(\false);
			$world->addParticle($particle->getVector3(), $particle, $players);
		}
	}

	public function removeParticles(World $world, array $players = \null){
		if($players === \null){
			$players = $world->getPlayers();
		}

		foreach($this->particles[$world->getFolderName()] ?? [] as $particle){
			$particle->setInvisible();
			$world->addParticle($particle->getVector3(), $particle, $players);
			$particle->setInvisible(\false);
		}
	}

	public function updateParticles(): void{
		$text = '';

		foreach($this->voters as $voter){
			$text .= \str_replace(['{player}', '{votes}'], [$voter['nickname'], $voter['votes']], $this->getConfig()->get('Text'))."\n";
		}

		foreach($this->particles as $worldParticles){
			foreach($worldParticles as $particle){
				$particle->setText($text);
			}
		}
	}

	public function setVoters(array $voters): void{
		$this->voters = $voters;
	}

	public function getVoters(): array{
		return $this->voters;
	}

	public function getUpdateTask(): ?UpdateVotesTask{
		return $this->updateTask;
	}

	public function onDisable(): void{
		foreach($this->particles as $world => $particles){
			$world = $this->getServer()->getWorldManager()->getWorldByName($world);

			if($world instanceof World){
				foreach($particles as $particle){
					$particle->setInvisible();
					$world->addParticle($particle->getVector3(), $particle);
				}
			}
		}

		$this->particles = [];

		if(isset($this->updateTask)){
			$this->updateTask->unsetKey();
			$this->getScheduler()->cancelTask($this->updateTask->getTaskId());
		}
	}
}
