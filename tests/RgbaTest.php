<?php

use Delight\Color\Rgba;

it('can retrieve red, blue, green, alpha from a color', function () {
    $color = new Rgba(255, 0, 0, 0.8);

    expect($color->red())->toBe(255);
    expect($color->green())->toBe(0);
    expect($color->blue())->toBe(0);
    expect($color->alpha())->toBe(0.8);
});

it('can create a rgba color from a CSS-like string', function () {
    $color = Rgba::fromString('rgba(128, 245, 12, 0.4)');

    expect($color->red())->toBe(128);
    expect($color->green())->toBe(245);
    expect($color->blue())->toBe(12);
    expect($color->alpha())->toBe(0.4);
});

it('can convert rgba to hex', function () {
    $rgb = new Rgba(255, 255, 0, 0.4);
    $hex = $rgb->toHex();

    expect($hex->red())->toBe(255);
    expect($hex->green())->toBe(255);
    expect($hex->blue())->toBe(0);
});

it('can convert rgba to rgb', function () {
    $color = new Rgba(255, 255, 0, 0.4);
    $rgb = $color->toRgb();

    expect($rgb->red())->toBe(255);
    expect($rgb->green())->toBe(255);
    expect($rgb->blue())->toBe(0);
});

it('can convert rgba to rgba', function () {
    $rgb = new Rgba(0, 0, 0, 1);
    expect($rgb->toRgba())->toBe($rgb);
});

it('can convert an rgb color to string', function () {
    $rgb = new Rgba(0, 0, 0, 0.4);

    expect((string) $rgb)->toBe('rgba(0,0,0,0.4)');
});
