<?php

use Delight\Color\Random;

it('returns the same number with the same seed', function () {
    $seed = 'my_seeeeeeeed';

    expect(Random::between(1, 1e7, $seed))->toBe(Random::between(1, 1e7, $seed));
});
