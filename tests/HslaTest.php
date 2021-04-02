<?php

use Delight\Color\Hsla;

it('can retrieve hue, saturation, lightness from a color', function () {
    $color = new Hsla(151, 51, 52, 0.4);

    expect($color->hue())->toBe(151.0);
    expect($color->saturation())->toBe(51.0);
    expect($color->lightness())->toBe(52.0);
    expect($color->alpha())->toBe(0.4);
});

it('can retrieve the red, green, blue from a hsla color', function () {
    $color = new Hsla(151, 51, 52, 0.6);

    expect($color->red())->toBe(70);
    expect($color->green())->toBe(195);
    expect($color->blue())->toBe(135);
    expect($color->alpha())->toBe(0.6);
});

it('can create a hsla color from a CSS-like string', function () {
    $color = Hsla::fromString('hsla(151, 51%, 52%, 1)');

    expect($color->hue())->toBe(151.0);
    expect($color->saturation())->toBe(51.0);
    expect($color->lightness())->toBe(52.0);
    expect($color->alpha())->toBe(1.0);

    expect($color->red())->toBe(70);
    expect($color->green())->toBe(195);
    expect($color->blue())->toBe(135);
});

it('can convert hsla to hex', function () {
    $color = new Hsla(151, 51, 52, 0);
    $hex = $color->toHex();

    expect($hex->red())->toBe(70);
    expect($hex->green())->toBe(195);
    expect($hex->blue())->toBe(135);
});

it('can convert hsla to rgba', function () {
    $color = new Hsla(151, 51, 52, 0.75);
    $rgba = $color->toRgba();

    expect($rgba->red())->toBe(70);
    expect($rgba->green())->toBe(195);
    expect($rgba->blue())->toBe(135);
    expect($rgba->alpha())->toBe(0.75);
});

it('can convert hsla to hsla', function () {
    $color = new Hsla(151, 51, 52, 0.4);
    expect($color)->toBe($color->toHsla(0.4));
});

it('can convert an hsla color to string', function () {
    $color = new Hsla(151, 51, 52, 0.44);

    expect((string) $color)->toBe('hsla(151,51%,52%,0.44)');
});

it('can generate a random color', function () {
    expect(Hsla::random())->toBeInstanceOf(Hsla::class);
});
