# Web fonts repository

[![Latest Stable Version](https://poser.pugx.org/finesse/web-fonts-repository/v/stable)](https://packagist.org/packages/finesse/web-fonts-repository)
[![Total Downloads](https://poser.pugx.org/finesse/web-fonts-repository/downloads)](https://packagist.org/packages/finesse/web-fonts-repository)
![PHP from Packagist](https://img.shields.io/packagist/php-v/finesse/web-fonts-repository.svg)
[![Build Status](https://travis-ci.org/Finesse/web-fonts-repository.svg?branch=master)](https://travis-ci.org/Finesse/web-fonts-repository)
[![Coverage Status](https://coveralls.io/repos/github/Finesse/web-fonts-repository/badge.svg?branch=master)](https://coveralls.io/github/Finesse/web-fonts-repository?branch=master)
[![Dependency Status](https://www.versioneye.com/php/finesse:web-fonts-repository/badge)](https://www.versioneye.com/php/finesse:web-fonts-repository)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/f387a746-709a-4b33-8966-c7e9d6f17391/mini.png)](https://insight.sensiolabs.com/projects/f387a746-709a-4b33-8966-c7e9d6f17391)

A simple webfont hosting inspired by [Google Fonts](http://fonts.google.com).
It runs on your server, stores and distributes webfont files and generates CSS on-the-go for embedding fonts on web pages.


## Quick start

### Requirements

1. HTTP server supporting PHP ≥ 7.0
2. [Composer](http://getcomposer.org) (required for installation)

### Installation

#### 1. Download the source code

Run the following code in the console:

```bash
composer create-project finesse/web-fonts-repository webfonts
```

Where `webfonts` is a path to a directory where the repository should be installed.

Or you can make some things manually:

1. Download [the source code from GitHub](http://github.com/Finesse/web-fonts-repository/archive/master.zip) and extract it.
2. Open a terminal and go to the source code root.
3. Install the libraries by running in the terminal:
    ```bash
    composer install
    ```
4. Prepare the repository by running in the terminal:
    ```bash
    composer run-script post-create-project-cmd
    ```

#### 2. File permissions

Give the user behalf which the web server runs permissions to write inside the `logs` directory.

You can just run this in the console:

```bash
# Don't do it in production!
chmod 777 logs
```

#### 3. Web server

Make the directory `public` be the document root of the web server.
Or just open [http://localhost/public](http://localhost/public) if you installed the repository to the web server root.

Make all the requests to not-existing files be handled by `public/index.php`. 
If your server is Apache, it's already done.

Make the server add the `Access-Control-Allow-Origin: *` HTTP-header to the font files. 
Otherwise some browsers will reject using fonts from the repository.
* Apache: all you need to do is to make sure that the `mod_header.c` module is on
  (run the `a2enmod headers` command and restart the server to turn it on).
* Nginx: use [this instruction](http://davidwalsh.name/cdn-fonts).

### Setup

Put your font files (woff, woff2, ttf, otf, eot, svg) to the `public/fonts` directory. You may separate them by subdirectories.
You can convert webfont files using [Transfonter](http://transfonter.org).

All settings go to the file `config/settings-local.php`.
If you don't have it, copy it from the file `config/settings-local.php.example`.

Parameters:

#### `displayErrorDetails`

Whether errors details should be sent to browser. Anyway errors are written to the file `logs/app.log`.
**You should turn it off on production server.**

#### `logger`/`level`

How many messages should be logged to the file.
The value is one of the [`\Psr\Log\LogLevel`](http://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md#5-psrlogloglevel) constants.
You can read more about log levels [here](http://github.com/apix/log#log-levels).

#### `fonts`

The list of fonts available in the repository. Simple example:

```php
return [
    // ...
    'fonts' => [
        'Open Sans' => [
            'styles' => [
                '300' => 'OpenSans/opensans-light.*',
                '300i' => 'OpenSans/opensans-light-italic.*',
                '400' => 'OpenSans/opensans-regular.*',
                '400i' => 'OpenSans/opensans-regular-italic.*',
            ]
        ],
        'Roboto' => [
            'styles' => [
                '300' => 'Roboto/roboto-light.*',
                '400' => 'Roboto/roboto-regular.*',
                '500' => 'Roboto/roboto-medium.*',
                '700' => 'Roboto/roboto-bold.*',
            ]
        ]
    ]
];
```

The `fonts` array keys are the font families names. The `styles` arrays keys are the styles names.
The numbers in the style names are the font weights, `i` stands for italic.

The font file paths are given relative to the `public/fonts` directory. 
The file paths are the [glob](http://en.wikipedia.org/wiki/Glob_(programming)) search patterns.
It means that the repository should consider all files matching the pattern as font files.

You can find more examples and possibilities [here](docs/fonts-setup.md).

### Usage

Add a `<link>` tag to the HTML code of the page on which you want to embed a font:

```html
<link rel="stylesheet" href="http://web-fonts-repository.local/css?family=Open+Sans:400,400i,700,700i|Roboto:300,400" />
```

Where `http://web-fonts-repository.local` is the root URL of an installed web fonts repository.

The required fonts are specified the same way as on Google Fonts. Font families are divided by `|`, families styles
are divided by `,`, family name is separated from styles list using `:`.

You may omit the styles list. In this case the regular style (`400`) is used.

```html
<link rel="stylesheet" href="http://web-fonts-repository.local/css?family=Open+Sans" />
```

Then embed a font in a CSS code:

```css
body {
    font-family: 'Open Sans', sans-serif;
}
```


## Versions compatibility

The project follows the [Semantic Versioning](http://semver.org).

It means that patch versions are fully compatible (i.e. 1.2.1 and 1.2.2), minor versions are backward compatible 
(i.e. 1.2.1 and 1.3.2) and major versions are not compatible (i.e. 1.2.1 and 3.0).
The pre-release versions (0.*) are a bit different: patch versions are backward compatible and minor versions are not 
compatible.


## License

MIT. See [the LICENSE](LICENSE) file for details.
