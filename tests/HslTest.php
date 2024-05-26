<?php

use Delight\Color\Hsl;

it('can retrieve hue, saturation, lightness from a color', function () {
    $color = new Hsl(151, 51, 52);

    expect($color)
        ->hue->toBe(151.0)
        ->saturation->toBe(51.0)
        ->lightness->toBe(52.0);
});

it('can retrieve the red, green, blue from a hsl color', function () {
    // Low hue
    $color = new Hsl(40, 100, 40);

    expect($color)
        ->red()->toBe(204)
        ->green()->toBe(136)
        ->blue()->toBe(0);

    // High hue
    $color = new Hsl(151, 51, 52);
    expect($color)
        ->red()->toBe(70)
        ->green()->toBe(195)
        ->blue()->toBe(135);
});

it('can create a hsl color from a CSS-like string', function () {
    $color = Hsl::fromString('hsl(151, 51%, 52%)');

    expect($color)
        ->hue->toBe(151.0)
        ->saturation->toBe(51.0)
        ->lightness->toBe(52.0);
})->skip();

it('can convert an hsl color to string', function () {
    $color = new Hsl(151, 51, 52);

    expect((string) $color)->toBe('hsl(151, 51%, 52%)');
    expect($color)->toHsl()->toBe('hsl(151, 51%, 52%)');
});

it('can convert an hsl color to hex', function () {
    $color = new Hsl(216, 12, 84);

    expect($color->toHex())->toBe('#d1d5db');
});

it('can convert create an hsl color from rgb', function () {
    // Sanity checks
    $color = Hsl::fromRGB(255, 255, 255);
    expect($color)
        ->hue->toBe(0.0)
        ->saturation->toBe(0.0)
        ->lightness->toBe(100.0);

    $color = Hsl::fromRGB(0, 0, 0);
    expect($color)
        ->hue->toBe(0.0)
        ->saturation->toBe(0.0)
        ->lightness->toBe(0.0);

    // Delta is zero
    $color = Hsl::fromRGB(127, 127, 127);
    expect($color)
        ->hue->toBe(0.0)
        ->saturation->toBe(0.0)
        ->lightness->toBe(49.8);

    // Red channel is biggest
    $color = Hsl::fromRGB(112, 58, 38);
    expect($color)
        ->hue->toBe(16.2)
        ->saturation->toBe(49.3)
        ->lightness->toBe(29.4);

    // Green channel is biggest
    $color = Hsl::fromRGB(99, 254, 57);
    expect($color)
        ->hue->toBe(107.2)
        ->saturation->toBe(99.0)
        ->lightness->toBe(61.0);

    // Blue channel is biggest
    $color = Hsl::fromRGB(167, 181, 225);
    expect($color)
        ->hue->toBe(225.5)
        ->saturation->toBe(49.2)
        ->lightness->toBe(76.9);
});

it('can convert a color in hex to hsl', function () {
    $color = Hsl::fromHex('#123456');

    expect($color)
        ->hue->toBe(210.0)
        ->saturation->toBe(65.4)
        ->lightness->toBe(20.4);
});

it('throws an exception when converting an invalid hex code', function ($hex) {
    Hsl::fromHex($hex);
})->with(['#12345', '#', '', 'hello world'])->throws(InvalidArgumentException::class);

it('can expand hex shorthands', function () {
    expect(Hsl::fromHex('#123'))->toHex()->toBe('#112233')
        ->and(Hsl::fromHex('#12'))->toHex()->toBe('#111222')
        ->and(Hsl::fromHex('#1'))->toHex()->toBe('#111111')
        // To test if we properly pad the hex code.
        ->and(Hsl::fromHex('#f0'))->toHex()->toBe('#fff000')
        ->and(Hsl::fromHex('#123f'))->toHex()->toBe('#112233')
        ->and(Hsl::fromHex('#123123ff'))->toHex()->toBe('#123123');
});

it('can compute contrast', function () {
    $contrast = Hsl::fromHex('#fff')->contrast(
        Hsl::fromHex('#000')
    );
    expect($contrast)->toBe(21.0);

    $contrast = Hsl::fromHex('#000')->contrast(
        Hsl::fromHex('#000')
    );
    expect($contrast)->toBe(1.0);
});

it('can create an hsl color from a css-like string', function () {
    $hex = Hsl::fromString('#f0');
    expect($hex)->toHex()->toBe('#fff000');

    // ignore the alpha
    $hexa = Hsl::fromString('#ffffff15');
    expect($hexa)->toHex()->toBe('#ffffff');

    $rgb = Hsl::fromString('rgb(10, 20, 30)');
    expect($rgb)->toRgb()->toBe('rgb(10, 20, 30)');

    // it should just ignore the alpha
    $rgba = Hsl::fromString('rgba(100, 200, 150, 0.5)');
    expect($rgba)->toRgb()->toBe('rgb(100, 200, 150)');

    $hsl = Hsl::fromString('hsl(180, 90%, 45%)');
    expect($hsl)
        ->hue->toBe(180.0)
        ->lightness->toBe(45.0)
        ->saturation->toBe(90.0);

    // it should just ignore the alpha
    $hsla = Hsl::fromString('hsla(180, 90%, 45%, .5)');
    expect($hsla)
        ->hue->toBe(180.0)
        ->lightness->toBe(45.0)
        ->saturation->toBe(90.0);
});

it('throws an exception if it can not create an hsl color from a given string', function (string $str) {
    Hsl::fromString($str);
})->with(['', 'Hello', 'gbr(1,3,3)'])->throws(InvalidArgumentException::class);

it('fails when given invalid ranges', function ($h, $s, $l) {
    Hsl::boundedRandom($h, $s, $l);
})->with('invalidHSLRanges')->throws(UnexpectedValueException::class);

it('fails when given invalid HSL values', function ($h, $s, $l) {
    new Hsl($h, $s, $l);
})->with([
    [-1, 10, 10],
    [400, 10, 10],
    [10, -1, 10],
    [10, 400, 10],
    [10, 10, -1],
    [10, 10, 400],
])->throws(InvalidArgumentException::class);
