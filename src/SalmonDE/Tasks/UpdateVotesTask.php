<?php
namespace SalmonDE\Tasks;

use pocketmine\scheduler\PluginTask;
use SalmonDE\Tasks\QueryServerListTask;

class UpdateVotesTask extends PluginTask
{

    public function __construct($owner, $lines){
        parent::__construct($owner);
        $this->lines = $lines;
        $this->data= [
            'Key' => $this->getOwner()->getConfig()->get('API-Key'),
            'Amount' => $this->getOwner()->getConfig()->get('Amount')
        ];
    }

    public function onRun($currenttick){
        $this->getOwner()->getServer()->getScheduler()->scheduleAsyncTask(new QueryServerListTask($this->data, $this->lines));
        foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player){
            $player->getLevel()->addParticle($this->getOwner()->particle, [$player]);
        }
    }
}
