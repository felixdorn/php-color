<?php

it('can a generate a random color', function () {
    $first = \Delight\Color\Generator::one();
    $second = \Delight\Color\Generator::one();

    expect($first->toString())->not->toBe($second->toString());
});

it('can generate many colors quickly', function () {
    $start = microtime(true);

    \Delight\Color\Generator::many(1000);

    $end = microtime(true);

    expect(round($end - $start, 3))->toBeLessThanOrEqual(1);
});
