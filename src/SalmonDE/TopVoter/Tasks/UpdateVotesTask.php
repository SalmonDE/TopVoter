<?php

namespace SalmonDE\TopVoter\Tasks;

use pocketmine\scheduler\PluginTask;

class UpdateVotesTask extends PluginTask {

    public function __construct(\SalmonDE\TopVoter\TopVoter $owner){
        parent::__construct($owner);

        $this->data = [
            'Key' => $this->getOwner()->getConfig()->get('API-Key'),
            'Amount' => (int) $this->getOwner()->getConfig()->get('Amount')
        ];
    }

    public function onRun(int $currenttick){
        if($this->data['Key'] !== null){
            $this->getOwner()->getServer()->getScheduler()->scheduleAsyncTask(new QueryServerListTask($this->data));
        }else{
            $this->getOwner()->getLogger()->warning('Invalid API key!');
            $this->getOwner()->getServer()->getScheduler()->cancelTask($this->getTaskId());
        }
    }

}
