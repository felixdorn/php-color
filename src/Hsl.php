<?php

namespace Delight\Color;

use Delight\Color\Concerns\IsColor;
use Delight\Color\Contracts\Color;

class Hsl implements Color
{
    use IsColor;

    protected float $hue;
    protected float $saturation;
    protected float $lightness;

    public function __construct(float $hue, float $saturation, float $lightness)
    {
        $this->hue        = $hue;
        $this->saturation = $saturation;
        $this->lightness  = $lightness;
    }

    public static function fromString(string $color): Hsl
    {
        [$h, $s, $l] = explode(',', str_replace(['hsl(', ')', " \t\n\r\0\x0B", '%'], '', $color));

        return new self((int) $h, (int) $s, (int) $l);
    }

    public static function random(): Hsl
    {
        return new self(
            random_int(0, 360),
            random_int(0, 100),
            random_int(0, 100)
        );
    }

    public function red(): int
    {
        return $this->toRgb()->red();
    }

    public function toRgb(): Rgb
    {
        $chroma = (1 - abs(2 * ($this->lightness / 100) - 1)) * ($this->saturation / 100);
        $x      = $chroma * (1 - abs(fmod($this->hue / 60, 2) - 1));
        $m      = ($this->lightness / 100) - ($chroma / 2);
        $hue    = (360 + ($this->hue % 360)) % 360;

        $rgb = [-1, -1, -1];

        if ($hue > 60 && $hue <= 120) {
            $rgb = [round(($x + $m) * 255), round(($chroma + $m) * 255), round($m * 255)];
        }

        if ($hue > 120 && $hue <= 180) {
            $rgb = [round($m * 255), round(($chroma + $m) * 255), round(($x + $m) * 255)];
        }

        if ($hue > 180 && $hue <= 240) {
            $rgb = [round($m * 255), round(($x + $m) * 255), round(($chroma + $m) * 255)];
        }

        if ($hue > 240 && $hue <= 300) {
            $rgb = [round(($x + $m) * 255), round($m * 255), round(($chroma + $m) * 255)];
        }

        /* @phpstan-ignore-next-line */
        if ($hue > 300 && $hue <= 360) {
            $rgb = [round(($chroma + $m) * 255), round($m * 255), round(($x + $m) * 255)];
        }

        return new Rgb(...array_map(fn ($c) => (int) $c, $rgb));
    }

    public function blue(): int
    {
        return $this->toRgb()->blue();
    }

    public function green(): int
    {
        return $this->toRgb()->green();
    }

    public function toHsl(): Hsl
    {
        return $this;
    }

    public function toHex(): Hex
    {
        return $this->toRgb()->toHex();
    }

    public function toRgba(float $alpha = 1): Rgba
    {
        return $this->toRgb()->toRgba($alpha);
    }

    public function __toString(): string
    {
        return sprintf(
            'hsl(%s,%s,%s)',
            $this->hue,
            $this->saturation . '%',
            $this->lightness . '%'
        );
    }

    public function toHsla(float $alpha = 1): Hsla
    {
        return new Hsla(
            $this->hue(),
            $this->saturation(),
            $this->lightness(),
            $alpha
        );
    }

    public function hue(): float
    {
        return $this->hue;
    }

    public function saturation(): float
    {
        return $this->saturation;
    }

    public function lightness(): float
    {
        return $this->lightness;
    }
}
