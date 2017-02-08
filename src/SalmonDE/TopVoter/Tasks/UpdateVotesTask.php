<?php
namespace SalmonDE\TopVoter\Tasks;

use pocketmine\scheduler\PluginTask;
use SalmonDE\Tasks\QueryServerListTask;

class UpdateVotesTask extends PluginTask
{

    public function __construct($owner){
        parent::__construct($owner);
    }

    public function onRun($currenttick){
        $data = [
            'Key' => $this->getOwner()->getConfig()->get('API-Key'),
            'Amount' => (int) $this->getOwner()->getConfig()->get('Amount')
        ];
        if($data['Key'] !== null){
            $this->getOwner()->getServer()->getScheduler()->scheduleAsyncTask(new QueryServerListTask($data));
        }else{
            $this->getOwner()->getLogger()->warning('Invalid API key!');
            $this->getOwner()->getServer()->getScheduler()->cancelTask($this->getTaskId());
        }
    }
}
