# PHP Color

[![Tests](https://github.com/felixdorn/php-color/actions/workflows/tests.yml/badge.svg?branch=master)](https://github.com/felixdorn/php-color/actions/workflows/tests.yml)
[![Formats](https://github.com/felixdorn/php-color/actions/workflows/formats.yml/badge.svg?branch=master)](https://github.com/felixdorn/php-color/actions/workflows/formats.yml)
[![Version](https://poser.pugx.org/felixdorn/php-color/version)](//packagist.org/packages/delights/color)
[![Total Downloads](https://poser.pugx.org/felixdorn/php-color/downloads)](//packagist.org/packages/delights/color)
[![License](https://poser.pugx.org/felixdorn/php-color/license)](//packagist.org/packages/delights/color)

## Installation





> Requires [PHP7.4.0+](https://php.net/releases)

You can install the package via composer:

```bash
composer require delights/color
```

## Usage

### Supported colors representations

* [Hex](src/Hex.php)
* [Rgb](src/Rgb.php)
* [Rgba](src/Rgba.php)
* [Hsl](src/Hsl.php)
* [Hsla](src/Hsla.php)

We'll use the [Rgba](src/Rgba.php) color in the examples below but keep it mind that those methods are available for
every color representation listed above.

## Creating a color

```php
use Delight\Color\Rgba;$color = new Rgba(255, 0, 0, $alpha = 0.7);

Rgba::random();

rgba(255, 0, 0, $alpha = 0.7);

Rgba::fromString('rgba(255, 0, 0, 0.7)');
```

## Converting a color

```php
use Delight\Color\Hsl;$color->toHex();
$color->toRgb();
$color->toRgba();
$color->toHsl();
$color->toHsla();

$color->convertTo(Hsl::class);
```

## Accessing Red, Green, Blue

```php
use Delight\Color\Hsl;$color = Hsl::random();

$color->red();
$color->green();
$color->blue();
```

## Brightness / Darkness

```php
use Delight\Color\Rgb;$color = Rgb::random();

$color->isDark();
$color->isBright();

// Returns a new instance of the color
$color->darken($percentage = 15);
$color->lighten($percentage = 15);
```

## Color legibility

```php
use Delight\Color\Rgba;$color = Rgba::random();,

$color->isLegibleWithBackground(
    hex('#00ff00')
);

$color->isLegibleWithForeground(
    hex('#0000ff')
);
```

## Luminance

As
in [https://www.w3.org/TR/2008/REC-WCAG20-20081211/#relativeluminancedef](https://www.w3.org/TR/2008/REC-WCAG20-20081211/#relativeluminancedef)

```php
use Delight\Color\Rgb;Rgb::random()->luminance();
```

## Add macros

We use [illuminate/macroable](https://github.com/illuminate/macroable).

## To a single color representation

```php
use Delight\Color\Hsl;Hsl::macro('darkenByTenPoints', function () {
    return $this->darken(10);
});

Hsl::random()->darkenByTenPoints();
```

## To all color representations

```php
\Delight\Color\Concerns\IsColor::macro('looksGood', function () {
    return true;
});

\Delight\Color\Rgb::random()->looksGood();
\Delight\Color\Hsl::random()->looksGood();
// ..., you got the idea.
```

## Testing

```bash
composer test
```

**PHP Color** was created by **[Félix Dorn](https://twitter.com/afelixdorn)** under
the **[MIT license](https://opensource.org/licenses/MIT)**.
