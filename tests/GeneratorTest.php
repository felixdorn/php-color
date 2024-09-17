<?php

use Felix\PHPColor\Generator;

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

it('can change defaults', function () {
    Generator::withDefaults([0, 120], null, [1, 2]);

    expect(Generator::defaultHue())->toBe([0, 120])
        ->and(Generator::defaultSaturation())->toBe([50, 90])
        ->and(Generator::defaultLightness())->toBe([1, 2]);

    Generator::withDefaults(null, [0, 0], null);

    expect(Generator::defaultHue())->toBe([0, 120])
        ->and(Generator::defaultSaturation())->toBe([0, 0])
        ->and(Generator::defaultLightness())->toBe([1, 2]);
});

it('fails when setting invalid defaults', function ($h, $s, $l) {
    Generator::withDefaults($h, $s, $l);
})->with('invalidHSLRanges')->throws(UnexpectedValueException::class);
