<?php

namespace Delight\Color\Contracts;

use Delight\Color\Generator\Rankeable;
use Delight\Color\Hsl;

interface Ranker
{
    public const POINT = 1;

    public function process(Rankeable $rankeable, Hsl $color): void;
}
