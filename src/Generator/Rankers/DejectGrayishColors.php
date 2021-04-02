<?php

namespace Delight\Color\Generator\Rankers;

use Delight\Color\Contracts\Ranker;
use Delight\Color\Generator\Rankeable;
use Delight\Color\Hsl;

class DejectGrayishColors implements Ranker
{
    public function process(Rankeable $rankeable, Hsl $color): void
    {
        if ($color->saturation() <= 30) {
            $rankeable->remove(1);
        }
    }
}
