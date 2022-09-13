# PHP Color

[![Tests](https://github.com/felixdorn/php-color/actions/workflows/tests.yml/badge.svg?branch=master)](https://github.com/felixdorn/php-color/actions/workflows/tests.yml)
[![Formats](https://github.com/felixdorn/php-color/actions/workflows/formats.yml/badge.svg?branch=master)](https://github.com/felixdorn/php-color/actions/workflows/formats.yml)
[![Version](https://poser.pugx.org/felixdorn/php-color/version)](//packagist.org/packages/delights/color)
[![Total Downloads](https://poser.pugx.org/felixdorn/php-color/downloads)](//packagist.org/packages/delights/color)
[![License](https://poser.pugx.org/felixdorn/php-color/license)](//packagist.org/packages/delights/color)

## Installation

> Requires [PHP 8.1+](https://php.net/releases)

You can install the package via composer:

```bash
composer require delights/color
```

## Usage

## Creating a color

```php
use Delight\Color\Hsl;

$color = new Hsl(100, 20, 20);

Hsl::limitedRandom([0, 360], [0,100], [0,100], $seed)

Hsl::random($seed);

Hsl::fromString('hsl(100, 20%, 20%)');
```

## Converting a color

```php
$color->toHex();
$color->toRgb();
```

## Accessing Hue, Saturation, Luminance



```php
$color->hue; # between 0-360
$color->saturation; # between 0-100
$color->lumination; # between 0-100
```

## Accessing Red, Green, Blue

```php
$color->red();
$color->green();
$color->blue();
```

## Brightness / Darkness

```php
$color->isDark();
$color->isBright();

// Returns a new instance of the color
$color->darken($percentage = 15);
$color->lighten($percentage = 15);
```

## Luminance

As in [https://www.w3.org/TR/2008/REC-WCAG20-20081211/#relativeluminancedef](https://www.w3.org/TR/2008/REC-WCAG20-20081211/#relativeluminancedef)

```php
$color->luminance();
```

## Testing

```bash
composer test
```

**PHP Color** was created by **[Félix Dorn](https://twitter.com/afelixdorn)** under
the **[MIT license](https://opensource.org/licenses/MIT)**.
