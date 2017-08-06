# Web fonts repository

A simple webfont hosting for your own server. Inspired by [Google Fonts](https://fonts.google.com).

* Generates CSS on-the-go for embedding fonts on web pages.
* Stores and distributes webfont files.


## Quick start

### Requirements

1. PHP ≥ 7.0
2. [Composer](https://getcomposer.org) (required for installation)

### Installation

#### 1. Download the source code

Run the following code in the console:

```bash
composer create-project finesse/web-fonts-repository project_path
```

Where `project_path` is the directory path where the project should be installed.

Or you can make some things manually:

1. Download [the source code from GitHub]((http://github.com/FinesseRus/web-fonts-repository/archive/master.zip)) and extract it.
2. Open a terminal and go to the source code root.
3. Run in the console:
	```bash
	composer install
	```
4. Copy the file `config/settings-local.php.example` to `config/settings-local.php`.

#### 2. File permissions

Give the user behalf which the web server runs permissions to write inside the `logs` directory.

You can just run this in the console (it is not OK in production):

```bash
chmod 777 logs
```

#### 3. Web server

Make the directory `public` be the document root of the web server.
Or just open [http://localhost/public](http://localhost/public) if you installed the project to the web server root.

Make all the requests to not-existing files be handled by `public/index.php`. 
If you use Apache, you don't have to do anything. 

### Setup

Put your font files to the `public/fonts` directory. You may separate them by subdirectories.

All settings go to the file `config/settings-local.php`.
If you don't have it, copy it from the file `config/settings-local.php.example`.

Parameters:

#### `displayErrorDetails`

Whether errors details should be sent to browser. Anyway errors are written to the file `logs/app.log`.
**You should turn it off on production server.**

#### `logger`/`level`

How many messages should be logged to the file. The value is on of the `\Monolog\Logger` constants.
You can read more about log levels [here](https://seldaek.github.io/monolog/doc/01-usage.html#log-levels).

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

The `fonts` array keys are the fonts names. The `styles` arrays keys are the styles names.
The numbers in the style names are the font weights, `i` stands for italic.

The font file paths are given relative to the `public/fonts` directory. 
The file paths are the [glob](https://en.wikipedia.org/wiki/Glob_(programming)) search patterns.
It means that the repository should consider all files matching the pattern as font files.

You can find more examples and possibilities [here](docs/fonts-setup.md).

### Usage

Add the `<link>` tag to the HTML code of the page on which you want to embed a font:

```html
<link rel="stylesheet" href="http://web-fonts-repository.local/css?family=Open+Sans:400,400i,700,700i|Roboto:300,400" />
```

Where `http://web-fonts-repository.local` is the root URL of a web fonts repository.

The required fonts are set the same way as on Google Fonts. Font families are divided by `|`, families styles
are divided by `,`, family name is separated from styles list using `:`.

You may omit the styles list. In this case the regular style (`400`) is be used.

```html
<link rel="stylesheet" href="http://web-fonts-repository.local/css?family=Open+Sans" />
```

Then embed a font in a CSS code:

```css
body {
    font-family: 'Open Sans', sans-serif;
}
```
