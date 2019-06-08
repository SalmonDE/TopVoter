<?php
declare(strict_types = 1);

namespace SalmonDE\TopVoter\Tasks;

use pocketmine\scheduler\Task;
use ProjectInfinity\PocketVote\PocketVote;
use SalmonDE\TopVoter\TopVoter;

class PocketVoteUpdateTask extends Task {

	private $owner;

	public function __construct(TopVoter $owner) {
		$this->owner = $owner;
	}

	public function onRun(int $currentTick): void {
		// PocketVote's array differ from the one provided by minecraftpocket-servers.com, so we need to recreate it.
		$voters = [];
		foreach(PocketVote::getAPI()->getTopVoters() as $topVoter) {
			$voters[] = ['nickname' => $topVoter['player'], 'votes' => $topVoter['votes']];
		}
		$this->owner->setVoters($voters);
		$this->owner->updateParticles();
		$this->owner->sendParticles();
	}
}