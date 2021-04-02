<?php

namespace Delight\Color\Concerns;

use Delight\Color\Contracts\Color;
use Delight\Color\Hsl;
use Illuminate\Support\Traits\Macroable;
use RuntimeException;

/** @mixin Color */
trait IsColor
{
    use Macroable;

    public function convertTo(string $to): Color
    {
        if (!class_exists($to)) {
            throw new RuntimeException("Can not convert the color to an existing class ($to)");
        }

        $classParts = explode('\\', $to);
        $class      = ucfirst(strtolower($classParts[array_key_last($classParts)]));

        return $this->{'to' . $class}();
    }

    public function isDark(): bool
    {
        return $this->toHsl()->lightness() <= 15;
    }

    public function isBright(): bool
    {
        return $this->toHsl()->lightness() >= 90;
    }

    public function darken(int $percentage): Color
    {
        $hsl      = $this->toHsl();
        $darkened = new Hsl(
            $hsl->hue(),
            $hsl->saturation(),
            $hsl->lightness() - $percentage,
        );

        return $darkened->convertTo(self::class);
    }

    public function lighten(int $percentage): Color
    {
        $hsl       = $this->toHsl();
        $lightened = new Hsl(
            $hsl->hue(),
            $hsl->saturation(),
            $hsl->lightness() + $percentage
        );

        return $lightened->convertTo(self::class);
    }

    public function isLegibleWithForeground(Color $color, int $variance = 0): bool
    {
        return $this->contrast($color) >= 4.5 - $variance;
    }

    public function contrast(Color $color): float
    {
        $original = $this->luminance();
        $against  = $color->luminance();

        $brightest = max($original, $against) + 0.05;
        $darkest   = min($original, $against) + 0.05;

        return $brightest / $darkest;
    }

    public function luminance(): float
    {
        [$r, $g, $b] = array_map(function ($channel) {
            $channel /= 255;

            return $channel <= 0.03928 ?
                $channel / 12.92
                : (($channel + 0.055) / 1.055) ** 2.4;
        }, [
            ($rgb = $this->toRgb())->red(),
            $rgb->green(),
            $rgb->blue(),
        ]);

        return $r * 0.02126 + $g * 0.7152 + $b * 0.0722;
    }

    public function isLegibleWithBackground(Color $color, int $variance = 0): bool
    {
        return $color->contrast($this) >= 4.5 - $variance;
    }
}
