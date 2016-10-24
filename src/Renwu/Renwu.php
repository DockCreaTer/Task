<?php
namespace Renwu;

use pocketmine\utils\Config;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\Server;
//use pocketmine\level\Level;
use pocketmine\utils\TextFormat;
use pocketmine\Plugin\PluginBase; 
//use pocketmine\entity\Effect;
//use onebone\economyapi\EconomyAPI;
//use pocketmine\event\entity\EntityDamageByEntityEvent;
//use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\command\CommandExecutor;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\item\Item;
//use pocketmine\math\Vector3;
use pocketmine\block\Block;
use pocketmine\block\Air;
//use pocketmine\scheduler\CallbackTask;
//use pocketmine\event\block\BlockPlaceEvent;
//use pocketmine\event\player\PlayerMoveEvent;
//use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
//use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\inventory\Inventory;
use MagicDroidX\Exp;

class Renwu extends PluginBase implements Listener{
	public $RenwuId = array();
	public $RenwuExp = array();
	public $RenwuRq = array();
	public $RenwuRqItem = array();
	public $RenwuInt = array();
	public $RenwuType = array();
	public function onEnable(){
		if (!is_dir($this->getDataFolder())) mkdir($this->getDataFolder());
		$this->user = new Config($this->getDataFolder()."Renwu.yml", Config::YAML);
		$this->cfg = new Config($this->getDataFolder()."config.yml", Config::YAML,array());
		 if(!$this->cfg->exists("RenwuId1"))
         {
         $this->cfg->set("RenwuId1","1");
         $this->cfg->save();
         }
		 if(!$this->cfg->exists("RenwuExp1"))
         {
         $this->cfg->set("RenwuExp1","1");
         $this->cfg->save();
         }
		 if(!$this->cfg->exists("RenwuRq1"))
         {
         $this->cfg->set("RenwuRq1","1");
         $this->cfg->save();
         }
		 if(!$this->cfg->exists("RenwuRqItem1"))
         {
         $this->cfg->set("RenwuRqItem1","1");
         $this->cfg->save();
         }
		 if(!$this->cfg->exists("RenwuInt1"))
         {
         $this->cfg->set("RenwuInt1","1");
         $this->cfg->save();
         }
		 if(!$this->cfg->exists("RenwuType1"))
         {
         $this->cfg->set("RenwuType1","1");
         $this->cfg->save();
         }
		 for($i = 1;$this->cfg->exists("RenwuId" . $i);$i++){
		$this->RenwuId[$i] = (int)$this->cfg->get("RenwuId" . $i);
		$this->RenwuExp[$i] = (int)$this->cfg->get("RenwuExp" . $i);
        $this->RenwuRq[$i] = (int)$this->cfg->get("RenwuRq" . $i);
        $this->RenwuRqItem[$i] = (int)$this->cfg->get("RenwuRqItem" . $i);
        $this->RenwuInt[$i] = $this->cfg->get("RenwuInt" . $i);
		$this->RenwuType[$i] = (int)$this->cfg->get("RenwuType" . $i);
		}
		$this->getLogger()->info("Renwu v2.2.0,By SKULL");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
    public function onDisable()
    {
    $this->getLogger()->info(">>   §bRenwu - 卸载!");
	}
	//------------------------------------------------------------
    public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		$pname = $sender->getName();
		$ny = date("Y");
		$nm = date("m");
		$nd = date("d");
		switch($command->getName())
		{
			case "rq" :
			    if(isset($args[1])){
					if($args[0] == "done"){
						if(!isset($args[1])){$this->ErrorMessage(2,$sender);return true;}
						if(!isset($this->RenwuId[$args[1]])){$this->ErrorMessage(1,$sender);return true;}
						$CheckDone = $this->CheckRenwu($sender,$args[1],$ny,$nm,$nd);
						if($CheckDone){
							
							Exp::getInstance()->addExp($pname,$this->RenwuExp[$args[1]]);
							$sender->sendMessage("成功！");
						}
					}
					if($args[0] == "get"){
						if(!isset($args[1])){$this->ErrorMessage(2,$sender);return true;}
						if(!isset($this->RenwuId[$args[1]])){$this->ErrorMessage(1,$sender);echo("hi1 \n");return true;}
						if($this->getRenwu($args[1],$sender,$ny,$nm,$nd) == 4){
							$sender->sendMessage("成功接受任务!");
							}
						else{
							$returned = $this->getRenwu($args[1],$sender,$ny,$nm,$nd);
							switch($returned){
							    case 1 :								
								$this->ErrorMessage(3,$sender);return true;
								case 2 :
								$this->ErrorMessage(1,$sender);return true;
								case 3 :
								$this->ErrorMessage(5,$sender);return true;
								case 5 :
								$this->ErrorMessage(8,$sender);return true;
							}
							}
					}
                    if($args[0] == "check" and isset($this->RenwuId[$args[1]])){
					$this->RenwuList($sender,$args[1]);return true;
					}
					else
					{
						$this->ErrorMessage(1,$sender);return true;
					}
		}
		else{$this->ErrorMessage(2,$sender);return;}
	}}
	//000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
	public function showList($p){
		$p->sendMessage("123");
	}
	//000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
	public function getRenwu($id,$sender,$ny,$nm,$nd){
		if($this->RenwuId[$id] == false){return 2;}
		$isDoing = $this->user->get($sender->getName() . "Renwu" . $id . "Doing");
		if($isDoing == false){$this->user->set($sender->getName() . "Renwu" . $id . "Doing",1);$this->user->save();$this->getRenwu($id,$sender,$ny,$nm,$nd);return 99;}
		if($isDoing == "2"){return 1;}
		$isDid = $this->user->get($sender->getName() . "Renwu" . $id . "Did" . $ny.$nm.$nd);
		$isDid2 = $this->user->get($sender->getName() . "Renwu" . $id . "Did");
		if($isDid == 1){return 3;}
		if($isDid2 == 1){return 5;}
		$ThisRenwu = $this->RenwuId[$id];
		$this->user->set($sender->getName() . "Renwu" . $id . "Doing",2);
		$this->user->save();
		$sender->sendMessage("任务要求：" . $this->RenwuInt[$id] . "!");
		return 4;
	}
	//000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
	public function RenwuList($sender,$id){
		$sender->sendMessage("任务" . $id . "完成条件：");
		$sender->sendMessage($this->RenwuInt[$id] . "!");
		return true;
	}
	//000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
	public function CheckRenwu($sender,$RenwuId,$ny,$nm,$nd){
		$isDoing = $this->user->get($sender->getName() . "Renwu" . $RenwuId . "Doing");
		if($isDoing == false or $isDoing == 1){$this->ErrorMessage(7,$sender);return false;}
		$isDid = $this->user->get($sender->getName() . "Renwu" . $RenwuId . "Did" . $ny.$nm.$nd);
		if($isDid == 1){$this->ErrorMessage(6,$sender);return true;}
		$hand = $sender->getInventory()->getItemInHand();
		if($hand->getId() == $this->RenwuRqItem[$RenwuId]){
			$Shuliang = $hand->getCount();
			if($Shuliang < $this->RenwuRq[$RenwuId]){$this->ErrorMessage(4,$sender);return false;}
            $this->removeItem($sender,new item($this->RenwuRqItem[$RenwuId],1,$this->RenwuRq[$RenwuId]));
			if($this->RenwuType[$RenwuId] == 1){
			$this->user->set($sender->getName() . "Renwu" . $RenwuId . "Did" . $ny.$nm.$nd,1);
			}
			$this->user->set($sender->getName() . "Renwu" . $RenwuId . "Doing",1);
			if($this->RenwuType[$RenwuId] == 2){$this->user->set($sender->getName() . "Renwu" . $RenwuId . "Did",1);}
			$this->user->save();
			return true;
		}
		else{$this->ErrorMessage(4,$sender);return false;}
	}
	//RemoveItem00000000000000000000000000000000000000000000000000000000000000000000000000000
	public function removeItem($sender, $getitem){
		$getcount = $getitem->getCount();
		if ($getcount <= 0)
			return;
		for($index = 0; $index < $sender->getInventory()->getSize(); $index ++){
			$setitem = $sender->getInventory()->getItem($index);
			if ($getitem->getID() == $setitem->getID()){
				if ($getcount >= $setitem->getCount()){
					$getcount -= $setitem->getCount();
					$sender->getInventory()->setItem($index, Item::get(Item::AIR, 0, 1));
				} else if ($getcount < $setitem->getCount()){
					$sender->getInventory()->setItem($index, Item::get($getitem->getID(), 0, $setitem->getCount() - $getcount));
					break;
				}
			}
		}
	}
	//000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
public function ErrorMessage($ErrorID,$Sender){
  switch($ErrorID){
  case 1 :$Sender->sendMessage(">>   §b并没有这个任务");break;
  case 2 :$Sender->sendMessage(">>   §b格式：/renwu <jieshou/tijiao/list> [任务ID]");break;
  case 3 :$Sender->sendMessage(">>   §b您已经接受这个任务了!");break;
  case 4 :$Sender->sendMessage(">>   §b您并没有达到任务完成要求!");break;
  case 5 :$Sender->sendMessage(">>   §b一天只能接受一次这个任务!");break;
  case 6 :$Sender->sendMessage(">>   §b你今天已经完成过这个任务了！");break;
  case 7 :$Sender->sendMessage(">>   §b你还没接受这个任务！");break;
  case 8 :$Sender->sendMessage(">>   §b你只能完成这个任务一次!");break;
  }
}
}









