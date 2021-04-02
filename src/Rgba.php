<?php

namespace Delight\Color;

use Delight\Color\Concerns\IsColor;
use Delight\Color\Contracts\Color;

class Rgba implements Color
{
    use IsColor;

    protected int $red;
    protected int $green;
    protected int $blue;
    protected float $alpha;

    public function __construct(int $red, int $green, int $blue, float $alpha)
    {
        $this->red   = $red;
        $this->green = $green;
        $this->blue  = $blue;
        $this->alpha = $alpha;
    }

    public static function fromString(string $color): Rgba
    {
        preg_match(
            '/rgba\((\d+),(\d+),(\d+),([\d.]+)\)/m',
            (string) preg_replace('/\s+/', '', $color),
            $rgba
        );

        // Removes rgba(...) from the array
        array_shift($rgba);

        return new self(...$rgba);
    }

    public static function random(): Rgba
    {
        return new self(
            random_int(0, 255),
            random_int(0, 255),
            random_int(0, 255),
            round(mt_rand() / mt_getrandmax(), 1)
        );
    }

    public function red(): int
    {
        return $this->red;
    }

    public function green(): int
    {
        return $this->green;
    }

    public function blue(): int
    {
        return $this->blue;
    }

    public function toRgba(float $alpha = -1): self
    {
        if ($alpha != -1) {
            $this->alpha = $alpha;
        }

        return $this;
    }

    public function toHex(): Hex
    {
        $convert = fn ($channel) => str_pad(dechex($channel), 2, '0', STR_PAD_LEFT);

        return new Hex(
            $convert($this->red),
            $convert($this->green),
            $convert($this->blue)
        );
    }

    public function __toString(): string
    {
        return sprintf(
            'rgba(%s,%s,%s,%s)',
            $this->red,
            $this->blue,
            $this->green,
            $this->alpha
        );
    }

    public function alpha(): float
    {
        return $this->alpha;
    }

    /**
     * @param float|int $alpha
     */
    public function toHsla($alpha = -1): Hsla
    {
        $hsl = $this->toHsl();

        return new Hsla(
            $hsl->hue(),
            $hsl->lightness(),
            $hsl->saturation(),
            $alpha !== -1 ? $alpha : $this->alpha
        );
    }

    public function toHsl(): Hsl
    {
        return $this->toRgb()->toHsl();
    }

    public function toRgb(): Rgb
    {
        return new Rgb($this->green, $this->red, $this->blue);
    }
}
