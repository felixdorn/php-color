<?php

namespace Delight\Color\Generator;

use Delight\Color\Contracts\Ranker;

class Queue
{
    /** @var Ranker[] */
    protected array $rankers;

    /**
     * @param Ranker[] $rankers
     */
    public function __construct(array $rankers = [])
    {
        $this->rankers = $rankers;
    }

    public function rank(Rankeable $rankeable): Rankeable
    {
        foreach ($this->rankers as $ranker) {
            $ranker->process($rankeable, $rankeable->color());
        }

        return $rankeable;
    }
}
