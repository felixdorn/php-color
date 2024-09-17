# PHP Color

[![Tests](https://github.com/felixdorn/php-color/actions/workflows/tests.yml/badge.svg?branch=master)](https://github.com/felixdorn/php-color/actions/workflows/tests.yml)
[![Formats](https://github.com/felixdorn/php-color/actions/workflows/formats.yml/badge.svg?branch=master)](https://github.com/felixdorn/php-color/actions/workflows/formats.yml)
[![Version](https://poser.pugx.org/felixdorn/php-color/version)](https://packagist.org/packages/felixdorn/php-color)
[![Total Downloads](https://poser.pugx.org/felixdorn/php-color/downloads)](https://packagist.org/packages/felixdorn/php-color)
[![License](https://poser.pugx.org/felixdorn/php-color/license)](https://packagist.org/packages/felixdorn/php-color)

## Installation

> Requires [PHP 8.3+](https://php.net/releases)

You can install the package via composer:

```bash
composer require felixdorn/php-color
```

### Features

* Support HSLA (and HSL)&, HEX, RGBA (and RGB)
* Generate a color for a given seed (like a user email)
* Darken, lighten the color.
* Compute the luminance, lightness, darkness
* Check the contrast of two colors

### TOC

* [Generating nice looking colors](#generating-nice-looking-colors)
* [Working with the HSLA object](#working-with-the-hsla-object-)
    * [From RGB to HSLA](#from-rgb-to-hsla)
    * [From Hex to HSLA](#from-hex-to-hsla)
    * [From CSS to HSLA](#from-css-to-hsla)

## Usage

### Generating nice looking colors

You can generate colors on the fly:

```php
use Felix\PHPColor\Generator;

Generator::one();
Generator::many(n: 10)
Generator::manyLazily(n: 10_000)
```

**Important:** the colors generated are generate with the following defaults

* Hue: [0, 360] (all hues)
* Saturation: [50, 90] (out of [0, 100])
* Lightness [50, 70] (out of [0, 100])
* Alpha [100, 100] (out of [0, 100])

This generates bright, saturated colors.

You may change the defaults for all generated colors used by the `Generator`.

```php
use Felix\PHPColor\Generator;

Generator::withDefaults(
    hue: [100, 200],
    saturation: 50,
    lightness: [40, 60],
    alpha: [100, 100]
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
use Felix\PHPColor\Generator;

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
use Felix\PHPColor\Generator;

$avatarColor = Generator::one(hue: [0, 360], lightness: 50, saturation: 100)
```

The generator returns `Hsla` objects. Let us see how they work.

### Working with the HSLA object.

You may be getting a color from somewhere which is not HSLA, you can convert them:

#### From RGB to HSLA

```php
\Felix\PHPColor\Hsla::fromRGB(255, 0, 0);
```

#### From Hex to HSLA

```php
\Felix\PHPColor\Hsla::fromHex("#FF0000")
\Felix\PHPColor\Hsla::fromHex("FF0000")
```

#### From scratch

```php
use Felix\PHPColor\Hsla;

$color = new Hsla(100, 20, 20);
$color = new Hsla(100, .2, .2); // automatically normalized to 0-100

Hsla::boundedRandom([0, 360], [0,100], [0,100], [0, 100], $seed)

Hsla::random($seed);
```

You may convert your HSLA color back to hex, RGB, HSLA...

```php
$color->toHex(); // #000000
$color->toRgba(); // rgb(0, 0, 0)
$color->toHsla(); // hsl(0, 0, 0)
```

You may access the properties of the color:

```php
$color->hue; # between 0-360
$color->saturation; # between 0-100
$color->lightness; # between 0-100
$color->alpha; # between 0-100

$color->setHue(...)->setSaturation(...)->setLightness(...)->setAlpha(...); // modifies the color
$color->withHue(...) // returns a new instance
$color->withSaturation(...); // returns a new instance
$color->withLightness(...); // returns a new instance
$color->withAlpha(...); // returns a new instance

// If you chain more than one with...(), use clone() + set...() instead:
$color->clone() 
     ->setHue(...)
     ->setSaturation(...)
     ->setLightness()
     ->setAlpha();

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

As in <https://www.w3.org/TR/2008/REC-WCAG20-20081211/#relativeluminancedef>.

```php
$color->luminance(); // 0.0 - 1.0
```

## Contrast

As in <https://www.w3.org/TR/2008/REC-WCAG20-20081211/#contrast-ratiodef>. Very useful for accessibility testing.
Returns a value between 1 and 21. Usually, this is written as 1:1 or 21:1. This returns "n:1".

```php
$color->contrast($otherColor); // 1 - 21
```

## Testing

```bash
composer test
```

**PHP Color** was created by **Félix Dorn** under the **[MIT license](https://opensource.org/licenses/MIT)**.
