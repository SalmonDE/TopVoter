<?php
namespace SalmonDE\Updater;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;
use pocketmine\utils\Utils;

class CheckVersionTask extends AsyncTask
{
    public function __construct($owner){
        $this->name = $owner->getDescription()->getName();
        $this->cversion = $owner->getDescription()->getVersion();
        $this->website = $owner->getDescription()->getWebsite();
        $this->autoupdate = $owner->getConfig()->get('Auto-Update');
        $this->path = $owner->getDataFolder();
    }

    public function onRun(){
        $url = json_decode(Utils::getURL($this->website.'MCPE-Plugins/Updater/Updater.php?plugin='.$this->name.'&new=1', 20), true);
        $nversion = $url['version'];
        if($nversion){
            if($this->cversion == $nversion){
                $this->setResult(false);
            }else{
                $this->setResult($nversion);
            }
        }else{
            $this->setResult('Empty');
        }
   }

    public function onCompletion(Server $server){
        if($this->getResult() == 'Empty'){
            $server->getPluginManager()->getPlugin($this->name)->getLogger()->error(TF::RED.'Could not check for Update: "Empty Response" !');
        }elseif($this->getResult()){
            $server->getPluginManager()->getPlugin($this->name)->getLogger()->alert(TF::GOLD.'Update available for '.$this->name.'!');
            $server->getPluginManager()->getPlugin($this->name)->getLogger()->alert(TF::RED.'Current version: '.$this->cversion);
            $server->getPluginManager()->getPlugin($this->name)->getLogger()->alert(TF::GREEN.'New Version: '.$this->getResult());
            if($this->autoupdate){
                $server->getPluginManager()->getPlugin($this->name)->getLogger()->alert(TF::AQUA.'Updating to '.$this->getResult().' ...');
                $server->getPluginManager()->getPlugin($this->name)->update();
            }
        }else{
            $server->getPluginManager()->getPlugin($this->name)->getLogger()->notice(TF::GREEN.'No Update available!');
        }
    }
}
