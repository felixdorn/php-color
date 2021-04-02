<?php

namespace Delight\Color\Contracts;

use Delight\Color\Hex;
use Delight\Color\Hsl;
use Delight\Color\Hsla;
use Delight\Color\Rgb;
use Delight\Color\Rgba;

interface Color
{
    public static function fromString(string $color): Color;

    public static function random(): self;

    public function red(): int;

    public function blue(): int;

    public function green(): int;

    public function toRgb(): Rgb;

    public function toRgba(float $alpha = 1): Rgba;

    public function toHsl(): Hsl;

    public function toHsla(float $alpha = 1): Hsla;

    public function toHex(): Hex;

    public function __toString(): string;

    public function luminance(): float;

    public function contrast(Color $color): float;

    public function isDark(): bool;

    public function isBright(): bool;

    public function convertTo(string $to): self;

    public function darken(int $percentage): self;

    public function lighten(int $percentage): self;

    public function isLegibleWithForeground(Color $color, int $variance = 0): bool;

    public function isLegibleWithBackground(Color $color, int $variance = 0): bool;
}
