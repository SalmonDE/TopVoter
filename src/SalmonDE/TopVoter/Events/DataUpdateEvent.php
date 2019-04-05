<?php
declare(strict_types = 1);

namespace SalmonDE\TopVoter\Events;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\plugin\PluginEvent;
use SalmonDE\TopVoter\TopVoter;

class DataUpdateEvent extends PluginEvent implements Cancellable {
	use CancellableTrait;

	private $voteData;

	public function __construct(TopVoter $plugin, array $voteData){
		parent::__construct($plugin);
		$this->voteData = $voteData;
	}

	public function getVoteData(): array{
		return $this->voteData;
	}

	public function setVoteData(array $voteData): void{
		$this->voteData = $voteData;
	}
}
