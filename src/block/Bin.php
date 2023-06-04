<?php
declare(strict_types=1);

namespace DavyCraft648\MCFurniture\block;

use customiesdevs\customies\block\permutations\{BlockProperty, Permutation};
use pocketmine\block\{Block, Transparent, utils\HorizontalFacingTrait};
use pocketmine\data\bedrock\block\{BlockStateNames, convert\BlockStateReader, convert\BlockStateWriter};
use pocketmine\data\runtime\RuntimeDataDescriber;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;

class Bin extends Transparent implements \customiesdevs\customies\block\permutations\Permutable{
	use HorizontalFacingTrait;

	private bool $open = false;

	public function getBlockProperties() : array{
		return [
			new BlockProperty(BlockStateNames::OPEN_BIT, [false, true]),
			new BlockProperty(BlockStateNames::FACING_DIRECTION, [2, 3, 4, 5])
		];
	}

	public function getPermutations() : array{
		return [
			(new Permutation("q.block_property('facing_direction') == 2"))
				->withComponent("minecraft:rotation", CompoundTag::create()
					->setFloat("x", 0)
					->setFloat("y", 180)
					->setFloat("z", 0)),
			(new Permutation("q.block_property('facing_direction') == 3"))
				->withComponent("minecraft:rotation", CompoundTag::create()
					->setFloat("x", 0)
					->setFloat("y", 0)
					->setFloat("z", 0)),
			(new Permutation("q.block_property('facing_direction') == 4"))
				->withComponent("minecraft:rotation", CompoundTag::create()
					->setFloat("x", 0)
					->setFloat("y", 270)
					->setFloat("z", 0)),
			(new Permutation("q.block_property('facing_direction') == 5"))
				->withComponent("minecraft:rotation", CompoundTag::create()
					->setFloat("x", 0)
					->setFloat("y", 90)
					->setFloat("z", 0)),
			(new Permutation("q.block_property('open_bit') == false"))
				->withComponent("minecraft:geometry", CompoundTag::create()
					->setString("value", "geometry.bin")),
			(new Permutation("q.block_property('open_bit') == true"))
				->withComponent("minecraft:geometry", CompoundTag::create()
					->setString("value", "geometry.bin_open")),
		];
	}

	public function getCurrentBlockProperties() : array{
		return [$this->open, $this->facing];
	}

	public function setOpen(bool $open = true) : Bin{
		$this->open = $open;
		return $this;
	}

	public function isOpen() : bool{
		return $this->open;
	}

	public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
		if($player !== null){
			$this->facing = $player->getHorizontalFacing();
		}
		return parent::place($tx, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
	}

	public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null, array &$returnedItems = []) : bool{
		$this->position->getWorld()->setBlock($this->position, $this->setOpen(!$this->open));
		return true;
	}

	protected function describeBlockOnlyState(RuntimeDataDescriber $w) : void{
		$w->bool($this->open);
		$w->horizontalFacing($this->facing);
	}

	public function serializeState(BlockStateWriter $blockStateOut) : void{
		$blockStateOut->writeBool(BlockStateNames::OPEN_BIT, $this->isOpen())
			->writeHorizontalFacing($this->getFacing());
	}

	public function deserializeState(BlockStateReader $blockStateIn) : void{
		$this->setOpen($blockStateIn->readBool(BlockStateNames::OPEN_BIT))
			->setFacing($blockStateIn->readHorizontalFacing());
	}
}