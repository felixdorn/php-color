<?php

namespace Delight\Color\Generator;

use Delight\Color\Hsl;

class Rankeable
{
    private Hsl $color;
    private int $score = 0;

    public function __construct(Hsl $color)
    {
        $this->color = $color;
    }

    public function add(int $x): self
    {
        $this->score += $x;

        return $this;
    }

    public function remove(int $x): self
    {
        $this->score -= $x;

        return $this;
    }

    public function betterThan(Rankeable $rankeable): bool
    {
        return $this->score() > $rankeable->score();
    }

    public function score(): int
    {
        return $this->score;
    }

    public function equalTo(Rankeable $rankeable): bool
    {
        return $this->score() === $rankeable->score();
    }

    public function color(): Hsl
    {
        return $this->color;
    }
}
