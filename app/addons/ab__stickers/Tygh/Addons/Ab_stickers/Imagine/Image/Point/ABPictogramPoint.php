<?php
/*******************************************************************************************
*   ___  _          ______                     _ _                _                        *
*  / _ \| |         | ___ \                   | (_)              | |              © 2025   *
* / /_\ | | _____  _| |_/ /_ __ __ _ _ __   __| |_ _ __   __ _   | |_ ___  __ _ _ __ ___   *
* |  _  | |/ _ \ \/ / ___ \ '__/ _` | '_ \ / _` | | '_ \ / _` |  | __/ _ \/ _` | '_ ` _ \  *
* | | | | |  __/>  <| |_/ / | | (_| | | | | (_| | | | | | (_| |  | ||  __/ (_| | | | | | | *
* \_| |_/_|\___/_/\_\____/|_|  \__,_|_| |_|\__,_|_|_| |_|\__, |  \___\___|\__,_|_| |_| |_| *
*                                                         __/ |                            *
*                                                        |___/                             *
* ---------------------------------------------------------------------------------------- *
* This is commercial software, only users who have purchased a valid license and accept    *
* to the terms of the License Agreement can install and use this program.                  *
* ---------------------------------------------------------------------------------------- *
* website: https://cs-cart.alexbranding.com                                                *
*   email: info@alexbranding.com                                                           *
*******************************************************************************************/
namespace Tygh\Addons\Ab_stickers\Imagine\Image\Point;
use Imagine\Image\BoxInterface;
use Imagine\Image\PointInterface;

final class ABPictogramPoint implements PointInterface
{

private $x;

private $y;

public function __construct($x, $y)
{
$this->x = $x;
$this->y = $y;
}

public function getX()
{
return $this->x;
}

public function getY()
{
return $this->y;
}

public function in(BoxInterface $box)
{
return $this->x < $box->getWidth() && $this->y < $box->getHeight();
}

public function move($amount)
{
return new self($this->x + $amount, $this->y + $amount);
}

public function __toString()
{
return sprintf('(%d, %d)', $this->x, $this->y);
}
}
