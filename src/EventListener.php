<?php

declare(strict_types=1);

namespace DavyCraft648\MCFurniture;

use DavyCraft648\MCFurniture\block\BarStool;
use DavyCraft648\MCFurniture\block\Chair;
use DavyCraft648\MCFurniture\utils\SitUtils;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\Position;
use function strtolower;

class EventListener implements \pocketmine\event\Listener{

	public Main $plugin;

	public function __construct(Main $plugin){
		$this->plugin = $plugin;
	}

	public function onPlayerJoin(PlayerJoinEvent $event) : void{
		$this->plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($event) : void{
			foreach(SitUtils::$sittingData as $playerName => $data){
				$sittingPlayer = $this->plugin->getServer()->getPlayerExact($playerName);

				if($sittingPlayer !== null){
					$block = $sittingPlayer->getWorld()->getBlock($sittingPlayer->getPosition()->add(0, -0.3, 0));
					if($block instanceof Chair or $block instanceof BarStool){
						$pos = $block->getPosition()->add(0.5, 1.6, 0.5);
					}else{
						return;
					}

					SitUtils::setSit($sittingPlayer, new Position($pos->x, $pos->y, $pos->z, $sittingPlayer->getWorld()), SitUtils::$sittingData[strtolower($sittingPlayer->getName())]['eid']);
				}
			}
		}), 30);
	}

	public function onPlayerQuit(PlayerQuitEvent $event) : void{
		$player = $event->getPlayer();
		if(SitUtils::isSitting($player)){
			SitUtils::unsetSit($player);
		}
	}

	public function onTeleport(EntityTeleportEvent $event) : void{
		$entity = $event->getEntity();

		if($entity instanceof Player){
			if(SitUtils::isSitting($entity)){
				SitUtils::unsetSit($entity);
			}
		}
	}

	public function onDeath(PlayerDeathEvent $event) : void{
		$player = $event->getPlayer();
		if(SitUtils::isSitting($player)){
			SitUtils::unsetSit($player);
		}
	}

	public function onMove(PlayerMoveEvent $event) : void{
		$player = $event->getPlayer();

		if(SitUtils::isSitting($player)){
			SitUtils::optimizeRotation($player);
		}
	}

	public function onBlockBreak(BlockBreakEvent $event) : void{
		$block = $event->getBlock();
		if($block instanceof Chair or $block instanceof BarStool){
			$pos = $block->getPosition()->add(0.5, 1.6, 0.5);
		}else{
			return;
		}

		foreach(SitUtils::$sittingData as $playerName => $data){
			if($pos->equals($data["pos"])){
				$sittingPlayer = $this->plugin->getServer()->getPlayerExact($playerName);

				if($sittingPlayer !== null){
					SitUtils::unsetSit($sittingPlayer);
				}
			}
		}
	}

	public function onDataPacketReceive(DataPacketReceiveEvent $event) : void{
		$packet = $event->getPacket();
		$player = $event->getOrigin()->getPlayer();

		if($player === null){
			return;
		}

		if($packet instanceof InteractPacket and $packet->action === InteractPacket::ACTION_LEAVE_VEHICLE && SitUtils::isSitting($player)){
			SitUtils::unsetSit($player);
		}
	}
}