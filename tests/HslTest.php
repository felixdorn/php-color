<?php

use Delight\Color\Hsl;

it('can retrieve hue, saturation, lightness from a color', function () {
    $color = new Hsl(151, 51, 52);

    expect($color->hue())->toBe(151.0);
    expect($color->saturation())->toBe(51.0);
    expect($color->lightness())->toBe(52.0);
});

it('can retrieve the red, green, blue from a hsl color', function () {
    $color = new Hsl(151, 51, 52);

    expect($color->red())->toBe(70);
    expect($color->green())->toBe(195);
    expect($color->blue())->toBe(135);
});

it('can create a hsl color from a CSS-like string', function () {
    $color = Hsl::fromString('hsl(151, 51%, 52%)');

    expect($color->hue())->toBe(151.0);
    expect($color->saturation())->toBe(51.0);
    expect($color->lightness())->toBe(52.0);

    expect($color->red())->toBe(70);
    expect($color->green())->toBe(195);
    expect($color->blue())->toBe(135);
});

it('can convert hsl to hex', function () {
    $color = new Hsl(151, 51, 52);
    $hex = $color->toHex();

    expect($hex->red())->toBe(70);
    expect($hex->green())->toBe(195);
    expect($hex->blue())->toBe(135);
});

it('can convert hsl to rgba', function () {
    $color = new Hsl(151, 51, 52);
    $rgba = $color->toRgba();

    expect($rgba->red())->toBe(70);
    expect($rgba->green())->toBe(195);
    expect($rgba->blue())->toBe(135);
    expect($rgba->alpha())->toBe(1.0);
});

it('can convert hsl to hsl', function () {
    $color = new Hsl(151, 51, 52);
    expect($color->toHsl())->toBe($color);
});

it('can convert hsl to hsla', function () {
    $color = new Hsl(151, 51, 52);
    $hsla = $color->toHsla();

    expect($hsla->hue())->toBe(151.0);
    expect($hsla->saturation())->toBe(51.0);
    expect($hsla->lightness())->toBe(52.0);
});

it('can convert an hsl color to string', function () {
    $color = new Hsl(151, 51, 52);

    expect((string) $color)->toBe('hsl(151,51%,52%)');
});

it('can generate a random color', fn () => expect(Hsl::random())->toBeInstanceOf(Hsl::class));
