<?php

use Delight\Color\Generator;

use function Spatie\Snapshots\assertMatchesJsonSnapshot;

it('can a generate a random color', function () {
    $first  = Generator::one();
    $second = Generator::one();

    expect($first->toString())->not->toBe($second->toString());
});

it('returns the same color with the same seed', function () {
    $first  = Generator::one('my_seed');
    $second = Generator::one('my_seed');

    expect($first->toString())->toBe($second->toString());
});

it('does not return the same color with different color', function () {
    $first  = Generator::one('some_seed');
    $second = Generator::one('some_other_seed');

    expect($first->toString())->not->toBe($second->toString());
});

it('can generate many colors based on a given seed', function () {
    $colors = Generator::many(10, 'some_seed');

    assertMatchesJsonSnapshot(json_encode($colors));
});

it('can generate colors lazily', function () {
    $colors = Generator::many(10, 'this_seed_is_cool');
    $lazy   = Generator::manyLazily(10, 'this_seed_is_cool');
    foreach ($lazy as $color) {
        expect($color->toString())->toBe($colors[$lazy->key()]->toString());
    }
});
