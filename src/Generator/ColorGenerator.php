<?php

namespace Delight\Color\Generator;

use Delight\Color\Hsl;
use Generator;

class ColorGenerator
{
    protected int $sampleSize;
    protected array $rankers;

    public function __construct(array $rankers = [], int $sampleSize = 100)
    {
        $this->rankers    = array_map(fn ($ranker) => is_string($ranker) ? new $ranker() : $ranker, $rankers);
        $this->sampleSize = $sampleSize;
    }

    /**
     * @return Generator<Hsl>
     */
    public function many(int $times): Generator
    {
        for ($i = 0; $i < $times; $i++) {
            yield $this->generate();
        }
    }

    public function generate(): Hsl
    {
        $population = [];

        for ($i = 0; $i < $this->sampleSize; $i++) {
            $population[] = Hsl::random();
        }

        return $this->best($population);
    }

    public function best(iterable $colors): Hsl
    {
        $rankers = new Queue($this->rankers);
        $best    = null;

        foreach ($colors as $color) {
            $ranked = $rankers->rank(
                new Rankeable($color)
            );

            if ($best === null) {
                $best = $ranked;
            }

            if ($best->equalTo($ranked)) {
                continue;
            }

            if ($ranked->betterThan($best)) {
                $best = $ranked;
            }
        }

        /* @phpstan-ignore-next-line  */
        return $best->color();
    }
}
