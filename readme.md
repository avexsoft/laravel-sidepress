# Sidepress

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

## What does this do?
A Laravel package to let Wordpress share the same domain root, side-by-side.

- This is best illustrated using a set of URIs below
- Laravel has the final control of all URIs
- As such, `php artisan down` also brings down Wordpress frontend pages

| URI | Remarks |
|:----|:-----|
| https://example.com/ | Can be handled by Laravel or Wordpress |
| https://example.com/home | Can be handled by Laravel or Wordpress |
| https://example.com/about | Can be handled by Laravel or Wordpress |


## Why do we need this?
> PHP is used by 79.1% of all the websites
>
> -- <cite>https://w3techs.com/technologies/details/pl-php, 6th Mar 2021</cite>

> Wordpress is used by... 40.5% of all websites.
>
> -- <cite>https://w3techs.com/technologies/details/cm-wordpress, 6th Mar 2021</cite>

Codewise, Laravel and Wordpress were built very differently and seemed quite impossible to co-exist until now. Given the numerous other attempts to marry them, we consider ourselves lucky to have chanced upon this approach.

The main benefit here is to include the Wordpress community in our Laravel projects. This package hopes to serve as a progressive step to assimilate Wordpress projects into Laravel.

## How does it work?

1. All URIs are handled by Laravel first
2. Non-existent URIs end up in a catch-all route that appends `sidep=1` and performs a `redirect()`
3. When the browser retries with `sidep=1`, Laravel's `public/index.php` sends it off to Wordpress

### Pros
- Instead of placing Wordpress in a subfolder such as `/blog`, Wordpress now sits on the root domain as a 1st class citizen (almost!).
- This gives the folks who are more familiar with Wordpress to continue as usual, yet allows Laravel to come into the picture and overwrite any URI as required.
- There is minimal interference in terms of code and operation between the 2 projects. See [Known issues/behavior](#known-issuesbehavior).

### Cons
- Using this package, every Wordpress URI is loaded twice, however, with caching and the high performance levels of PHP and Laravel today, this can be an acceptable trade off for some.

### Aim
- To maintain the small, low-code footprint
- To reduce the performance impact of loading each URI twice

Take a look at [contributing.md](contributing.md) to see a to do list.

## Known issues/behavior

- Some Wordpress plugins will modify `public/.htaccess` and break Laravel, monitor this file closely
- POST via Wordpress's `index.php` will be blocked by Laravel's `VerifyCsrfToken` middleware, i.e. HTTP 419, and that is actually a good thing. You can add to the middleware's `$except` list to workaround this. Wordpress comments will work without modification as it POST via `wp-comments-post.php`

## Installation

Via Composer

``` bash
$ composer require avexsoft/laravel-sidepress
```

## Usage

``` bash
# Patch Laravel and create the Wordpress installation shell script
$ php artisan sidepress:install

# Install and configure Wordpress
$ ./sidepress-install.sh
```

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

No tests yet, feel free to submit PRs

``` bash
$ composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

We recommend having a separate database for Wordpress to mitigate any malicious/vunerable plugins.

## Credits

- [Avexsoft][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/avexsoft/sidepress.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/avexsoft/sidepress.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/avexsoft/sidepress/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/avexsoft/laravel-sidepress
[link-downloads]: https://packagist.org/packages/avexsoft/laravel-sidepress
[link-travis]: https://travis-ci.org/avexsoft/sidepress
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/avexsoft
[link-contributors]: ../../contributors
