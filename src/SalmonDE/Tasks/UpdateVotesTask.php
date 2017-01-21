<?php
namespace SalmonDE\Tasks;

use pocketmine\scheduler\PluginTask;
use SalmonDE\Tasks\QueryServerListTask;

class UpdateVotesTask extends PluginTask
{

    public function __construct($owner){
        parent::__construct($owner);
        $this->lines = ['Header' => $this->getOwner()->getConfig()->get('Header'), 'Text' => $this->getOwner()->getConfig()->get('Text')];
        $this->data = [
            'Key' => $this->getOwner()->getConfig()->get('API-Key'),
            'Amount' => $this->getOwner()->getConfig()->get('Amount')
        ];
    }

    public function onRun($currenttick){
        if($this->data['Key'] && $this->data['Amount'] > 0){
            $this->getOwner()->getServer()->getScheduler()->scheduleAsyncTask(new QueryServerListTask($this->data, $this->lines));
        }else{
            $this->getOwner()->getLogger()->warning('Invalid API key or voter amount!');
            $this->getOwner()->getServer()->getScheduler()->cancelTask($this->getTaskId());
        }
    }
}
