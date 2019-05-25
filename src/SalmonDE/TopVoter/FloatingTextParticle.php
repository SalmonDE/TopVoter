<?php
declare(strict_types = 1);

namespace SalmonDE\TopVoter;

use pocketmine\world\particle\FloatingTextParticle as FTP;
use pocketmine\math\Vector3;

class FloatingTextParticle extends FTP {

	private $vector3;

	public function __construct(Vector3 $vector3, string $text, string $title = ''){
		parent::__construct($text, $title);
		$this->vector3 = $vector3->asVector3();
	}

	public function getVector3(): Vector3{
		return $this->vector3;
	}
}
