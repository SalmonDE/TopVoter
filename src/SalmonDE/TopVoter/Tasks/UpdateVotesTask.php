<?php
namespace SalmonDE\TopVoter\Tasks;

use pocketmine\scheduler\PluginTask;
use SalmonDE\TopVoter\TopVoter;

class UpdateVotesTask extends PluginTask {

    private $data = [];

    public function __construct(TopVoter $owner){
        parent::__construct($owner);

        $this->data = [
            'Key' => $this->getOwner()->getConfig()->get('API-Key'),
            'Amount' => (int) $this->getOwner()->getConfig()->get('Amount')
        ];
    }

    public function unsetKey(){
        $this->data['Key'] = null;
    }

    public function onRun(int $currentTick){
        if($this->data['Key'] !== null){
            $this->getOwner()->getServer()->getScheduler()->scheduleAsyncTask(new QueryServerListTask($this->data));
        }else{
            $this->getOwner()->getLogger()->warning('Invalid API key!');
            $this->getOwner()->getServer()->getScheduler()->cancelTask($this->getTaskId());
            $this->getOwner()->getServer()->getPluginManager()->disablePlugin($this->getOwner());
        }
    }

}
