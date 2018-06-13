<?php
namespace SalmonDE\TopVoter\Tasks;

use pocketmine\scheduler\Task;
use SalmonDE\TopVoter\TopVoter;

class UpdateVotesTask extends Task {

    private $owner;
    private $data = [];

    public function __construct(TopVoter $owner){
        $this->owner = $owner;
        $this->data = [
            'Key' => $owner->getConfig()->get('API-Key'),
            'Amount' => (int) $owner->getConfig()->get('Amount')
        ];
    }

    public function unsetKey(){
        $this->data['Key'] = null;
    }

    public function onRun(int $currentTick){
        if($this->data['Key'] !== null){
            $this->owner->getServer()->getAsyncPool()->submitTask(new QueryServerListTask($this->data));
        }else{
            $this->owner->getLogger()->warning('Invalid API key!');
            $this->owner->getScheduler()->cancelTask($this->getTaskId());
            $this->owner->getServer()->getPluginManager()->disablePlugin($this->owner);
        }
    }

}
