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
    expect($color->blue())->toBe(134);
});

it('can create a hsl color from a CSS-like string', function () {
    $color = Hsl::fromString('hsl(151, 51%, 52%)');

    expect($color->hue())->toBe(151.0);
    expect($color->saturation())->toBe(51.0);
    expect($color->lightness())->toBe(52.0);

    expect($color->red())->toBe(70);
    expect($color->green())->toBe(195);
    expect($color->blue())->toBe(134);
});

it('can convert an hsl color to string', function () {
    $color = new Hsl(151, 51, 52);

    expect((string) $color)->toBe('hsl(151,51%,52%)');
});

it('can generate a random color', fn () => expect(Hsl::random())->toBeInstanceOf(Hsl::class));
