<?php

namespace Delight\Color\Generator;

use Delight\Color\Contracts\Color;
use Delight\Color\Contracts\Ranker;
use Delight\Color\Hsl;
use Generator;
use RuntimeException;

class ColorGenerator
{
    protected int $sampleSize;
    /** @var Ranker[] */
    private array $rankers;

    /** @param Ranker[]|string[] $rankers */
    public function __construct(array $rankers = [], int $sampleSize = 100)
    {
        $this->rankers    = array_map(fn ($ranker) => is_string($ranker) ? new $ranker() : $ranker, $rankers);
        $this->sampleSize = $sampleSize;
    }

    /** @return Color[] */
    public function many(int $times): array
    {
        return array_map(function () {
            return $this->generate();
        }, array_fill(0, $times, null));
    }

    public function generate(): Hsl
    {
        $population = $this->colors($this->sampleSize);

        $rankers = new Queue($this->rankers);
        $best    = null;

        foreach ($population as $k => $color) {
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

        if ($best === null) {
            throw new RuntimeException('No good colors found on this sample size');
        }

        return $best->color();
    }

    /**
     * @return Generator<Hsl>
     */
    private function colors(int $sampleSize): Generator
    {
        for ($i = 0; $i < $sampleSize; $i++) {
            yield Hsl::random();
        }
    }
}
