<?php

namespace Delight\Color\Generator\Rankers;

use Delight\Color\Contracts\Ranker;
use Delight\Color\Generator\Rankeable;
use Delight\Color\Hsl;

class PrioritizeHueRange implements Ranker
{
    private int $from;
    private int $to;
    private int $bonus;

    public function __construct(int $from, int $to, int $bonus = 1)
    {
        $this->from  = $from;
        $this->to    = $to;
        $this->bonus = $bonus;
    }

    public function process(Rankeable $rankeable, Hsl $color): void
    {
        $hue = $color->hue();

        if ($hue >= $this->from && $hue <= $this->to) {
            $rankeable->add($this->bonus);
        }
    }
}
