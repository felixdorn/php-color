<?php

namespace Delight\Color\Generator\Rankers;

use Delight\Color\Contracts\Ranker;
use Delight\Color\Generator\Rankeable;
use Delight\Color\Hsl;

class DejectBrightColors implements Ranker
{
    public function process(Rankeable $rankeable, Hsl $color): void
    {
        if ($color->lightness() >= 85 || $color->saturation() < 15) {
            $rankeable->remove(1);
        }
    }
}
