<?php

namespace Delight\Color\Generator\Rankers;

use Delight\Color\Contracts\Ranker;
use Delight\Color\Generator\Rankeable;
use Delight\Color\Hsl;

class PrioritizeFlashyColors implements Ranker
{
    public function process(Rankeable $rankeable, Hsl $color): void
    {
        if ($color->saturation() > 50
            && $color->lightness() > 50
            && $color->lightness() < 70
            && $color->saturation() < 90
        ) {
            $rankeable->add(2);
        }
    }
}
