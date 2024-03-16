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

## Usage

### Generating nice looking colors

You can generate colors on the fly:

```php
use Delight\Color\Generator;

Generator::one();
Generator::many(n: 10)
Generator::manyLazily(n: 10_000)
```

You may force the generator to use a certain seed:

```php
use Delight\Color\Generator;

$avatarColor = Generator::one(seed: $email); // will always return the same color for the given seed.
```

This also works for `Generator::many` and `Generator::manyLazily`.


You may change the defaults used by the `Generator`.

```php
use Delight\Color\Generator;

Generator::withDefaults(
    hue: [100, 200],
    saturation: [1, 20],
    lightness: [40, 60]
);
```

Or some of the defaults
```php
Generator::withDefaults(
    hue: [120, 140] // just restrict the hue but keep the saturation and lightness settings
);
```

### Working with color

```php
use Delight\Color\Hsl;

$color = new Hsl(100, 20, 20);
$color = new Hsl(100, .2, .2); // automatically normalized to 0-100

Hsl::boundedRandom([0, 360], [0,100], [0,100], $seed)

Hsl::random($seed);

// Alphas are silently ignored.
 // Works with rgb, rgba, hsla, hex...
 // This accepts _CSS-like_ string, emphasis on _like_.
Hsl::fromString('hsl(100, 20%, 20%)');
```

### Converting a color

Print a CSS string in the given format.

```php
$color->toHex();
$color->toRgb();
$color->toHsl();
```

###  Hue, saturation, lightness

```php
$color->hue; # between 0-360
$color->saturation; # between 0-100
$color->lightness; # between 0-100
```

### Red, green, blue

```php
$color->colorChannels(); // returns [r, g, b]
$color->red();
$color->green();
$color->blue();
```

## Brightness, darkness

```php
$color->isDark();
$color->isBright();

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
