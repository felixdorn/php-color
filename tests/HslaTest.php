<?php

use Felix\PHPColor\Hsla;

it('can retrieve hue, saturation, lightness from a color', function () {
    $color = new Hsla(151, 51, 52, 1);

    expect($color)
        ->hue->toBe(151.0)
        ->saturation->toBe(51.0)
        ->lightness->toBe(52.0);
});

it('can retrieve the red, green, blue from a hsl color', function () {
    // Low hue
    $color = new Hsla(40, 100, 40, 1);

    expect($color)
        ->red()->toBe(204)
        ->green()->toBe(136)
        ->blue()->toBe(0);

    // High hue
    $color = new Hsla(151, 51, 52, 1);
    expect($color)
        ->red()->toBe(70)
        ->green()->toBe(195)
        ->blue()->toBe(135);
});

it('can convert an hsl color to string', function () {
    $color = new Hsla(151, 51, 52, 1);

    expect((string) $color)->toBe('hsl(151 51% 52% 100%)');
    expect($color)->toHsla()->toBe('hsl(151 51% 52% 100%)');
});

it('can convert an hsl color to hex', function () {
    $color = new Hsla(216, 12, 84, 1);

    expect($color->toHex())->toBe('#d1d5db');
});

it('can convert create an hsla color from rgba', function () {
    // Sanity checks
    $color = Hsla::fromRGBA(255, 255, 255, 1);
    expect($color)
        ->hue->toBe(0.0)
        ->saturation->toBe(0.0)
        ->lightness->toBe(100.0);

    $color = Hsla::fromRGBA(0, 0, 0, 1);
    expect($color)
        ->hue->toBe(0.0)
        ->saturation->toBe(0.0)
        ->lightness->toBe(0.0);

    // Delta is zero
    $color = Hsla::fromRGBA(127, 127, 127, 1);
    expect($color)
        ->hue->toBe(0.0)
        ->saturation->toBe(0.0)
        ->lightness->toEqualWithDelta(49.8, 0.05);

    // Red channel is biggest
    $color = Hsla::fromRGBA(112, 58, 38, 1);
    expect($color)
        ->hue->toEqualWithDelta(16.2, 0.05)
        ->saturation->toEqualWithDelta(49.3, 0.05)
        ->lightness->toEqualWithDelta(29.4, 0.05);

    // Green channel is biggest
    $color = Hsla::fromRGBA(99, 254, 57, 1);
    expect($color)
        ->hue->toEqualWithDelta(107.2, 0.05)
        ->saturation->toEqualWithDelta(99.0, 0.05)
        ->lightness->toEqualWithDelta(61.0, 0.05);

    // Blue channel is biggest
    $color = Hsla::fromRGBA(167, 181, 225, 1);
    expect($color)
        ->hue->toEqualWithDelta(225.5, 0.05)
        ->saturation->toEqualWithDelta(49.2, 0.05)
        ->lightness->toEqualWithDelta(76.9, 0.05);
});

it('can convert a color in hex to hsl', function () {
    $color = Hsla::fromHex('#123456f0');

    expect($color)
        ->hue->toBe(210.0)
        ->saturation->toEqualWithDelta(65.4, 0.05)
        ->lightness->toEqualWithDelta(20.4, 0.05)
        ->alpha->toEqualWithDelta(94, 0.5);
});

it('throws an exception when converting an invalid hex code', function ($hex) {
    Hsla::fromHex($hex);
})->with(['#12345', '#', '', 'hello world'])->throws(InvalidArgumentException::class);

it('can expand hex shorthands', function () {
    expect(true)->toBeTrue()
        ->and(Hsla::fromHex('#123'))->toHex()->toBe('#112233')
        ->and(Hsla::fromHex('#12'))->toHex()->toBe('#111222')
        ->and(Hsla::fromHex('#1'))->toHex()->toBe('#111111')
        // To test if we properly pad the hex code.
        ->and(Hsla::fromHex('#f0'))->toHex()->toBe('#fff000')
        ->and(Hsla::fromHex('#123f'))->toHex()->toBe('#112233')
        ->and(Hsla::fromHex('#123123ff'))->toHex()->toBe('#123123')
    ;
});

it('can compute contrast', function () {
    $contrast = Hsla::fromHex('#fff')->contrast(
        Hsla::fromHex('#000')
    );
    expect($contrast)->toBe(21.0);

    $contrast = Hsla::fromHex('#000')->contrast(
        Hsla::fromHex('#000')
    );
    expect($contrast)->toBe(1.0);
});

it('fails when given invalid ranges', function ($h, $s, $l) {
    Hsla::boundedRandom($h, $s, $l, 1);
})->with('invalidHSLRanges')->throws(UnexpectedValueException::class);

it('fails when given invalid HSL values', function ($h, $s, $l) {
    new Hsla($h, $s, $l, 1);
})->with([
    [-1, 10, 10],
    [400, 10, 10],
    [10, -1, 10],
    [10, 400, 10],
    [10, 10, -1],
    [10, 10, 400],
])->throws(InvalidArgumentException::class);
