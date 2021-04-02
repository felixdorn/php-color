<?php

use Delight\Color\Generator\Rankeable;
use Delight\Color\Hsl;

it('can add x to the score', function () {
    $rankeable = new Rankeable(Hsl::random());

    expect($rankeable->score())->toBe(0);
    expect($rankeable->add(5)->score())->toBe(5);
});

it('can remove x from the score', function () {
    $rankeable = new Rankeable(Hsl::random());

    expect($rankeable->score())->toBe(0);
    expect($rankeable->add(5)->score())->toBe(5);
    expect($rankeable->remove(5)->score())->toBe(0);
});

it('can retrieve the color', function () {
    $rankeable = new Rankeable($color = Hsl::random());
    expect($rankeable->color())->toBe($color);
});

it('can compare two rankeable', function () {
    $r1 = new Rankeable(Hsl::random());
    $r2 = new Rankeable(Hsl::random());
    $r1->add(2);
    $r2->add(3);

    expect($r2->betterThan($r1))->toBeTrue();
    expect($r1->betterThan($r2))->toBeFalse();
});

it('can check if two rankeable are equal', function () {
    $r1 = new Rankeable(Hsl::random());
    $r2 = new Rankeable(Hsl::random());
    $r1->add(2);
    $r2->add(2);

    expect($r2->equalTo($r1))->toBeTrue();
    expect($r1->equalTo($r2))->toBeTrue();
});
