<?php

use Delight\Color\Rgb;

it('can retrieve red, blue, green from a color', function () {
    $color = new Rgb(255, 0, 0);

    expect($color->red())->toBe(255);
    expect($color->green())->toBe(0);
    expect($color->blue())->toBe(0);
});

it('can create a rgb color from a CSS-like string', function () {
    $color = Rgb::fromString('rgb(128, 245, 12)');

    expect($color->red())->toBe(128);
    expect($color->green())->toBe(245);
    expect($color->blue())->toBe(12);
});

it('can convert rgb to hex', function () {
    $rgb = new Rgb(255, 255, 0);
    $hex = $rgb->toHex();

    expect($hex->red())->toBe(255);
    expect($hex->green())->toBe(255);
    expect($hex->blue())->toBe(0);
});

it('can convert rgb to rgba', function () {
    $color = new Rgb(255, 255, 0);
    $rgba = $color->toRgba();

    expect($rgba->red())->toBe(255);
    expect($rgba->green())->toBe(255);
    expect($rgba->blue())->toBe(0);
    expect($rgba->alpha())->toBe(1.0);
});

it('can convert rgb to rgb', function () {
    $color = new Rgb(0, 0, 0);
    expect($color->toRgb())->toBe($color);
});

it('can convert black rgb to hsl rgb without division by zero', function () {
    $color = new Rgb(0, 0, 0);
    $hsl = $color->toHsl();

    expect($hsl->hue())->toBe(0.0);
    expect($hsl->lightness())->toBe(0.0);
    expect($hsl->saturation())->toBe(0.0);
});

it('can convert an rgb color to string', function () {
    $color = new Rgb(0, 0, 0);

    expect((string) $color)->toBe('rgb(0,0,0)');
});

it('can generate a random color', function () {
    $rgb = Rgb::random();

    expect($rgb)->toBeInstanceOf(Rgb::class);
});
