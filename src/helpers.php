<?php

use Delight\Color\Contracts\Ranker;
use Delight\Color\Generator\ColorGenerator;
use Delight\Color\Generator\Rankers\DejectBrightColors;
use Delight\Color\Generator\Rankers\DejectDarkColors;
use Delight\Color\Generator\Rankers\DejectGrayishColors;
use Delight\Color\Generator\Rankers\PrioritizeFlashyColors;
use Delight\Color\Hex;
use Delight\Color\Hsl;
use Delight\Color\Hsla;
use Delight\Color\Rgb;
use Delight\Color\Rgba;

if (!function_exists('random_color')) {
    /**
     * @param Ranker[]|null $rankers
     */
    function random_color(int $sampleSize = 100, ?array $rankers = null): Hsl
    {
        return (new ColorGenerator($rankers ?? [
                DejectGrayishColors::class,
                DejectDarkColors::class,
                DejectBrightColors::class,
                PrioritizeFlashyColors::class,
            ], $sampleSize))->generate();
    }
}

if (!function_exists('hex')) {
    function hex(string $color): Hex
    {
        return Hex::fromString($color);
    }
}

if (!function_exists('hsl')) {
    function hsl(float $hue, float $saturation, float $lightness): Hsl
    {
        return new Hsl($hue, $saturation, $lightness);
    }
}

if (!function_exists('rgb')) {
    function rgb(int $red, int $green, int $blue): Rgb
    {
        return new Rgb($red, $green, $blue);
    }
}

if (!function_exists('rgba')) {
    function rgba(int $red, int $green, int $blue, float $alpha = 1): Rgba
    {
        return new Rgba($red, $green, $blue, $alpha);
    }
}

if (!function_exists('hsla')) {
    function hsla(float $hue, float $saturation, float $lightness, float $alpha = 1): Hsla
    {
        return new Hsla($hue, $saturation, $lightness, $alpha);
    }
}
