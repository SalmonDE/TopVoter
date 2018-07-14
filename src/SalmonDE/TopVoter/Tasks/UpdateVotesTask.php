<?php
declare(strict_types = 1);

namespace SalmonDE\TopVoter\Tasks;

use pocketmine\scheduler\Task;
use SalmonDE\TopVoter\TopVoter;

class UpdateVotesTask extends Task {

    private $owner;
    private $key = null;
    private $amount;

    public function __construct(TopVoter $owner){
        $this->owner = $owner;
        $this->key = $owner->getConfig()->get('API-Key');
        $this->amount = min(500, (int) $owner->getConfig()->get('Amount'));
    }

    public function unsetKey(): void{
        $this->key = null;
    }

    public function onRun(int $currentTick): void{
        if(!empty($this->key)){
            $this->owner->getServer()->getAsyncPool()->submitTask(new QueryServerListTask($this->key, $this->amount));
        }else{
            $this->owner->getLogger()->warning('Invalid API key');
            $this->owner->getScheduler()->cancelTask($this->getTaskId());
            $this->owner->getServer()->getPluginManager()->disablePlugin($this->owner);
        }
    }
}
