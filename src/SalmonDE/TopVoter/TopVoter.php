<?php
declare(strict_types = 1);

namespace SalmonDE\TopVoter;

use pocketmine\level\Level;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\level\Position;
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
        $this->getScheduler()->scheduleRepeatingTask($this->updateTask = new UpdateVotesTask($this), max(180, $this->getConfig()->get('Update-Interval')) * 20);

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    }

    private function initParticles(): void{
        foreach((array) $this->getConfig()->get('Positions') as $pos){
            if(($level = $this->getServer()->getLevelByName($pos['world'])) instanceof Level){
                $this->particles[$level->getFolderName()][] = new FloatingTextParticle(new Vector3($pos['x'], $pos['y'], $pos['z']), '', $this->getConfig()->get('Header'));
            }
        }
    }

    public function getParticles(): array{
        return $this->particles;
    }

    public function sendParticles(Level $level = null, array $players = null){
        if($level === null){
            foreach(array_keys($this->particles) as $level){
                if(($level = $this->getServer()->getLevelByName($level)) instanceof Level){
                    $this->sendParticles($level);
                }
            }

            return;
        }

        if($players === null){
            $players = $level->getPlayers();
        }

        foreach($this->particles[$level->getFolderName()] ?? [] as $particle){
            $particle->setInvisible(false);
            $level->addParticle($particle, $players);
        }
    }

    public function removeParticles(Level $level, array $players = null){
        if($players === null){
            $players = $level->getPlayers();
        }

        foreach($this->particles[$level->getFolderName()] ?? [] as $particle){
            $particle->setInvisible();
            $level->addParticle($particle, $players);
            $particle->setInvisible(false);
        }
    }

    public function updateParticles(): void{
        $text = '';

        foreach($this->voters as $voter){
            $text .= str_replace(['{player}', '{votes}'], [$voter['nickname'], $voter['votes']], $this->getConfig()->get('Text'))."\n";
        }

        foreach($this->particles as $levelParticles){
            foreach($levelParticles as $particle){
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

    public function onDisable(): void{
        foreach($this->particles as $level => $particles){
            $level = $this->getServer()->getLevelByName($level);

            if($level instanceof Level){
                foreach($particles as $particle){
                    $particle->setInvisible();
                    $level->addParticle($particle);
                }
            }
        }

        $this->particles = [];
        $this->updateTask->unsetKey();
        $this->getScheduler()->cancelTask($this->updateTask->getTaskId());
    }
}
