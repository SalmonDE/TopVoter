<?php
declare(strict_types = 1);

namespace SalmonDE\TopVoter\Tasks;

use pocketmine\scheduler\Task;
use ProjectInfinity\PocketVote\PocketVote;
use SalmonDE\TopVoter\TopVoter;

class UpdateVotesTask extends Task {

	private $owner;
	private $key = \null;
	private $amount;
	private $usePocketVote;

	public function __construct(TopVoter $owner){
		$this->owner = $owner;
		$this->key = $owner->getConfig()->get('API-Key');
		$this->usePocketVote = $owner->usePocketVote;
		$this->amount = \min(500, (int) $owner->getConfig()->get('Amount'));
	}

	public function unsetKey(): void{
		$this->key = \null;
	}

	public function onRun(int $currentTick): void{
	    if($this->usePocketVote) {
	        // PocketVote's array differ from the one provided by minecraftpocket-servers.com, so we need to recreate it.
	        $voters = [];
	        foreach(PocketVote::getAPI()->getTopVoters() as $topVoter) {
	            $voters[] = ['nickname' => $topVoter['player'], 'votes' => $topVoter['votes']];
            }
	        $this->owner->setVoters($voters);
            $this->owner->updateParticles();
            $this->owner->sendParticles();
	        return;
        }

		if(!empty($this->key)){
			$this->owner->getServer()->getAsyncPool()->submitTask(new QueryServerListTask($this->key, $this->amount));
		}else{
			$this->owner->getLogger()->warning('Invalid API key');
			$this->owner->getScheduler()->cancelTask($this->getTaskId());
			$this->owner->getServer()->getPluginManager()->disablePlugin($this->owner);
		}
	}
}
