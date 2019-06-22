<?php
declare(strict_types = 1);

namespace SalmonDE\TopVoter\Tasks;

use pocketmine\player\Player;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Internet;
use SalmonDE\TopVoter\Events\DataUpdateEvent;

class QueryServerListTask extends AsyncTask {

	private const BASE_URL = 'https://minecraftpocket-servers.com/api/?object=servers&element=voters&month=current&format=json&limit={AMOUNT}&key={KEY}';

	private $key;
	private $amount;

	public function __construct(string $key, int $amount){
		$this->key = $key;
		$this->amount = $amount;
	}

	public function onRun(): void{
		$err = '';
		$raw = Internet::getURL(str_replace(['{AMOUNT}', '{KEY}'], [$this->amount, $this->key], self::BASE_URL), 10, [], $err);

		if($err === ''){
			$data = \json_decode($raw, \true);

			if(\is_array($data)){
				$this->setResult(['success' => \true, 'voters' => $data['voters']]);
				return;
			}elseif(\strpos($raw, 'Error:') !== \false){
				$err = \trim(\str_replace('Error:', '', $raw));
			}
		}

		$this->setResult(['success' => \false, 'error' => $err, 'response' => empty($raw) ? 'null' : $raw]);
	}

	public function onCompletion(): void{
		$topVoter = Server::getInstance()->getPluginManager()->getPlugin('TopVoter');

		if($topVoter->isDisabled()){
			return;
		}

		if($this->getResult()['success']){
			$voters = $this->getResult()['voters'];

			if($topVoter->getConfig()->get('Check-Name', \true)){
				foreach($voters as $index => $voteData){
					if(!Player::isValidUsername($voteData['nickname'])){
						unset($voters[$index]);
					}
				}
			}

			($event = new DataUpdateEvent($topVoter, $voters))->call();

			if(!$event->isCancelled() && $topVoter->getVoters() !== $event->getVoteData()){
				$topVoter->setVoters($event->getVoteData());
				$topVoter->updateParticles();
				$topVoter->sendParticles();
			}
		}else{
			$topVoter->getLogger()->warning('Error while processing data from the serverlist!');
			$topVoter->getLogger()->error('Error: '.$this->getResult()['error']);
			$topVoter->getLogger()->debug('Raw: '.$this->getResult()['response']);

			if($this->getResult()['error'] === 'no server key' || $this->getResult()['error'] === 'invalid server key'){
				$topVoter->getUpdateTask()->unsetKey();
				$topVoter->getServer()->getPluginManager()->disablePlugin($topVoter);
			}
		}
	}
}
