<?php
declare(strict_types=1);

namespace DavyCraft648\MCFurniture\block;

use customiesdevs\customies\block\permutations\RotatableTrait;
use customiesdevs\customies\block\permutations\Permutable;
use pocketmine\block\Transparent;

class WallCabinet extends Transparent implements Permutable {
    use RotatableTrait;
}
