# PHP Color

[![Tests](https://github.com/felixdorn/php-color/actions/workflows/tests.yml/badge.svg?branch=master)](https://github.com/felixdorn/php-color/actions/workflows/tests.yml)
[![Formats](https://github.com/felixdorn/php-color/actions/workflows/formats.yml/badge.svg?branch=master)](https://github.com/felixdorn/php-color/actions/workflows/formats.yml)
[![Version](https://poser.pugx.org/delights/color/version)](https://packagist.org/packages/delights/color)
[![Total Downloads](https://poser.pugx.org/delights/color/downloads)](https://packagist.org/packages/delights/color)
[![License](https://poser.pugx.org/delights/color/license)](https://packagist.org/packages/delights/color)

## Installation

> Requires [PHP 8.3+](https://php.net/releases)

You can install the package via composer:

```bash
composer require delights/color
```

### Features
* Support HSL, HEX, RGB
* Generate a color for a given seed (like a user email) 
* Darken, lighten the color.
* Check the lightness, darkness
* Check the contrast of two colors
* Parse colors from CSS

### TOC

* [Generating nice looking colors](#generating-nice-looking-colors)
* [Working with the HSL object](#working-with-the-hsl-object-)
  * [From RGB to HSL](#from-rgb-to-hsl)
  * [From Hex to HSL](#from-hex-to-hsl)
  * [From CSS to HSL](#from-css-to-hsl)

## Usage

### Generating nice looking colors

You can generate colors on the fly:

```php
use Delight\Color\Generator;

Generator::one();
Generator::many(n: 10)
Generator::manyLazily(n: 10_000)
```

**Important:** the colors generated are generate with the following defaults
* Hue: [0, 360] (all hues)
* Saturation: [50, 90] (out of [0, 100])
* Lightness [50, 70] (out of [0, 100])

This generates bright, saturated colors.

You may change the defaults for all generated colors used by the `Generator`.

```php
use Delight\Color\Generator;

Generator::withDefaults(
    hue: [100, 200],
    saturation: 50,
    lightness: [40, 60]
);
```

Or some of the defaults
```php
Generator::withDefaults(
    hue: [120, 140] // just restrict the hue but keep the saturation and lightness settings
);
```

You may force the generator to use a certain seed:
```php
$avatarColor = Generator::one(seed: $email); // will always return the same color for the given seed.
```

This also works for `Generator::many` and `Generator::manyLazily`.

You may override the default hue, saturation, and lightness ranges used to generate a color:

```php
use Delight\Color\Generator;

$avatarColor = Generator::one(
    hue: [100, 200],
    lightness: [40, 80]
);
$avatarColor = Generator::many(
    hue: null, // use global defaults
    saturation: [100, 100]
    lightness: [40, 80]
);
$avatarColor = Generator::manyLazily(
    lightness: [50, 60]
)
```

Or specify a single number instead of a range:

```php
use Delight\Color\Generator;

$avatarColor = Generator::one(hue: [0, 360], lightness: 50, saturation: 100)
```

The generator returns `Hsl` objects. Let us see how they work.

### Working with the HSL object. 

You may be getting a color from somewhere which is not HSL, you can convert them:

#### From RGB to HSL

```php
\Delight\Color\Hsl::fromRGB(255, 0, 0);
```

#### From Hex to HSL

```php
\Delight\Color\Hsl::fromHex("#FF0000")
\Delight\Color\Hsl::fromHex("FF0000")
```

#### From CSS to HSL

This function silently ignores the transparent counterpart of the HSL, hex, and RGB format.

```php
\Delight\Color\Hsl::fromString("rgb(255, 0,0)")
\Delight\Color\Hsl::fromString("rgba(255, 0,0, .5)") // silently ignores the transparency
\Delight\Color\Hsl::fromString("hsl(144, 100%, 14.4)")
```

### From scratch

```php
use Delight\Color\Hsl;

$color = new Hsl(100, 20, 20);
$color = new Hsl(100, .2, .2); // automatically normalized to 0-100

Hsl::boundedRandom([0, 360], [0,100], [0,100], $seed)

Hsl::random($seed);
```

You may convert your HSL color back to hex, RGB, HSL...

```php
$color->toHex(); // #000000
$color->toRgb(); // rgb(0, 0, 0)
$color->toHsl(); // hsl(0, 0, 0)
```

You may access the properties of the color:

```php
$color->hue; # between 0-360
$color->saturation; # between 0-100
$color->lightness; # between 0-100

$color->colorChannels(); // returns [r, g, b]
$color->red(); // 0-255
$color->green(); // 0-255
$color->blue(); // 0-255
```

And check the brightness of a color:

```php
$color->isDark();
$color->isBright();
```

You may also specify a threshold, a number between 0 (darkest) and 100 (brightest):
```php
$color->isDark(threshold: 5);
```

You may darken or lighten a given color:
```php
// Returns a new instance of the color
$color->darken($percentage = 15);
$color->lighten($percentage = 15);
```

## Luminance

As in <https://www.w3.org/TR/2008/REC-WCAG20-20081211/#relativeluminancedef>

```php
$color->luminance();
```

## Contrast
As in <https://www.w3.org/TR/2008/REC-WCAG20-20081211/#contrast-ratiodef>. Very useful for accessibility testing.
```php
$color->contrast($otherColor);
```

## Testing

```bash
composer test
```

**PHP Color** was created by **[Félix Dorn](https://twitter.com/afelixdorn)** under
the **[MIT license](https://opensource.org/licenses/MIT)**.
