<?php

use Delight\Color\Hex;

it('can retrieve red, blue, green from a color', function () {
    $color = new Hex('4C', '8E', 'D5');

    expect($color->red())->toBe(76);
    expect($color->green())->toBe(142);
    expect($color->blue())->toBe(213);
});

it('can create a hex color from a CSS-like string', function () {
    $color = Hex::fromString('#4C8ED5');

    expect($color->red())->toBe(76);
    expect($color->green())->toBe(142);
    expect($color->blue())->toBe(213);
});

it('can convert hex to rgb', function () {
    $hex = new Hex('ff', 'ff', '00');
    $rgb = $hex->toRgb();

    expect($rgb->red())->toBe(255);
    expect($rgb->green())->toBe(255);
    expect($rgb->blue())->toBe(0);
});

it('can convert hex to rgba', function () {
    $hex = new Hex('ff', 'ff', '00');
    $rgba = $hex->toRgba();

    expect($rgba->red())->toBe(255);
    expect($rgba->green())->toBe(255);
    expect($rgba->blue())->toBe(0);
    expect($rgba->alpha())->toBe(1.0);
});

it('can convert hex to hsl', function () {
    $hex = new Hex('e7', 'b8', '36');
    $hsla = $hex->toHsl();

    expect($hsla->hue())->toBe(44.0);
    expect($hsla->saturation())->toBe(79.0);
    expect($hsla->lightness())->toBe(56.0);
});

it('can convert hex to hsla', function () {
    $hex = new Hex('e7', 'b8', '36');
    $hsla = $hex->toHsla();

    expect($hsla->hue())->toBe(44.0);
    expect($hsla->saturation())->toBe(79.0);
    expect($hsla->lightness())->toBe(56.0);
    expect($hsla->alpha())->toBe(1.0);
});

it('can convert hex to hex', function () {
    $hex = new Hex('00', '00', '00');
    expect($hex->toHex())->toBe($hex);
});

it('can convert an hex color to string', function () {
    $hex = new Hex('00', '00', '00');

    expect((string) $hex)->toBe('#000000');
});

it('can generate a random color', function () {
    $hex = Hex::random();

    expect($hex)->toBeInstanceOf(Hex::class);
});
