<?php
declare(strict_types=1);

namespace DavyCraft648\MCFurniture\block;

use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class BarStool extends \pocketmine\block\Transparent{

	public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null, array &$returnedItems = []) : bool{
		return false; //TODO: sit
	}
}